<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Tag;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PostRequest;

class PostController extends Controller
{
    //ログインしているかチェックし、してない場合ログイン画面に遷移させる
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //compactで変数を渡してるが、未実装のためコメント
        //return view('post', compact('posts'));
        return view('post');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('newPost');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $requestDatas = $request->all();
        
        $post_tag_datas;
        //複数のDBを更新するため、一部の失敗等でのデータ不整合を防ぐためトランザクションを使用しDB更新が全て成功でコミット
        DB::beginTransaction();
        try {
            $post = new Post;
            $afterSplitTags = $post->splitByTag($requestDatas['tag']);

            //タグをハッシュタグ毎に分ける
            if (isset($afterSplitTags)){
                //全タグデータ追加(ユニークなので既に登録済の部分は追加しない)
                foreach ($afterSplitTags as $tagData) {
                    //存在しないタグなら追加し追加データ取得、存在していたらデータ取得のみ
                    $tagDatas = Tag::firstOrCreate(['tag' => $tagData]);
                    $post_tag_datas[] = $tagDatas['id'];
                }

            }

            //投稿の不要なデータ削除
            unset($requestDatas['_token']);
            unset($requestDatas['tag']);

            //投稿内容を新規追加
            $post->fill($requestDatas)->save();

            //追加した投稿のID取得
            $insertedId = $post->id;

            //中間テーブル更新
            $search_post = Post::find($insertedId);

            //新規登録
            if (isset($post_tag_datas)) {
                $search_post->tags()->attach($post_tag_datas);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            \Session::flash('flash_message', '登録が失敗しました');
            return back();
        }

        \Session::flash('flash_message', '登録が完了しました');
        return view('newPost');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
