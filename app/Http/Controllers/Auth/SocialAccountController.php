<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Social;

class SocialAccountController extends Controller
{
    //コントローラより、連携するSNSのプロバイダー名を引数で渡す
    public function redirectToProvider(string $provider)
    {
        return \Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(\App\Social $social, string $provider)
    {
        try {
            $user = \Socialite::with($provider)->user();
        } catch (\Exception $e) {
            return redirect('/start');
        }

        //SNSサイトから受け取った情報を配列に保持
        $providerInfo['user'] = $user;
        $providerInfo['provider'] = $provider;

        //登録処理
        $authUser = $social->create($providerInfo);

        //認証(登録済か新規作成したユーザ情報でログイン)
        auth()->login($authUser, true);
        return redirect()->to('/home');

    }
}
