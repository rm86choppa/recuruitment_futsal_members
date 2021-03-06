<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Socialite\Contracts\User as ProviderUser;//SNSサイトから返ってきたユーザ情報
use App\Http\Controllers\Auth;

class Social extends Model
{
    protected $guarded = ['id'];//autoincrimentで、自動的に入るため値が代入されない

    //usersとの紐づけ(リレーション)
    //主テーブル名の単数_id(post_id)にしてる場合は特に必要ないが、それ以外の場合外部キー、ローカルキーを指定が必要
    public function user() 
    {
        return $this->belongsto('App\User', 'user_id', 'id');
    }

    //SNS連携したプロバイダーがすでに登録済なら、登録を省略
    public function create($providerInfo)
    {
        $provider = $providerInfo['provider'];
        $providerUser = $providerInfo['user'];

        //既に登録済なら検索で取得したusersテーブルの情報を返す(なければnullが返る)
        $account = $this->find($providerInfo);

        if($account) 
        {
            //SNS連携済なら、それに紐づくusersテーブルの情報を返す。(SNSサイトから渡された情報に変更がある可能性があるので更新)
            $name_search_user = User::where('name', $providerUser->getName())->first();
            if($name_search_user) {
                $user = User::find($account['user_id'])
                                ->update(['email' => $providerUser->getEmail()]);;
            }else {
                $user = User::find($account['user_id'])
                ->update(['name' => $providerUser->getName(), 'email' => $providerUser->getEmail()]);;
            }
            $user = User::find($account['user_id']);

            return $user;
        }

        //上記でまだ未登録だった場合、UsersテーブルにSNSサイトから渡されたメールアドレスで検索した情報があればそのユーザに紐づけ登録、
        //なければSNSサイトから渡されたメールアドレスで新規登録(同じユーザで既に登録があっても複数ＳＮＳで同じメールアドレスを登録してない場合、同じユーザか判断できないため別ユーザ扱いになるので注意)
        //Usersテーブルに登録すると紐づいてるSocialsテーブルにも設定される
        $email_search_user = User::where('email', $providerUser->getEmail())->first();
        if (!$email_search_user) {
            //名前が同一の場合登録エラーになるので、名前でも検索して1件もなければ名前も登録、既に登録があれば名前は登録しない(名前でのログインでユニークに設定してるため)
            $name_search_user = User::where('name', $providerUser->getName())->first();

            if($name_search_user) {
                $user = User::create([  
                    'email' => $providerUser->getEmail(),
                ]);
            } else {
                $user = User::create([  
                    'email' => $providerUser->getEmail(),
                    'name'  => $providerUser->getName(),
                ]);
            }

            // 取得(or作成)したusersテーブルに紐づくlinked_social_accountsのレコードを1行追加
            $user->accounts()->create([
                'provider_id'   => $providerUser->getId(),
                'provider_name' => $provider,
            ]);

            // 取得したusersテーブルの情報を返す
            return $user;
        } else {
            // usersには情報ありなので、ソーシャルテーブルのみ作成
            $email_search_user->accounts()->create([
                'provider_id'   => $providerUser->getId(),
                'provider_name' => $provider,
            ]);

            // 取得したusersテーブルの情報を返す
            return $email_search_user;
        }
    }

    //プロバイダーIDで検索した結果を返す。
    public function find($providerInfo)
    {
        $provider = $providerInfo['provider'];
        $providerUser_id = $providerInfo['user']->getId();

        $account = Social::where('provider_name', $provider)
        ->where('provider_id', $providerUser_id)->first();

        return $account;
    }

    //認証完了処理(sns連携を使用しない登録だと、メール確認を使用してるのでemail_verified_atの更新が必要)
    public function authentication($user) {
        
        if (isset($user['email_verified_at'])) {
            return $user;
        } else {
            $result = User::find($user['id'])->update(['email_verified_at' => now()]);

            $verifiedUser = User::find($user['id']);

            return $verifiedUser;
        }
    }
}