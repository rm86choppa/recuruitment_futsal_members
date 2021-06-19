<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\User;
use Illuminate\Support\Facades\Hash;

class MypageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //全投稿情報取得(投稿に紐づくユーザ、タグ、いいね情報も取得)
        $posts = Post::with('user', 'tags', 'likes')->orderBy('updated_at', 'desc')->get();

        //ログインユーザがいいねした投稿の一覧を表示するため、ユーザに紐づく投稿(いいねした投稿)を取得
        $users = User::with('likes')->orderby('updated_at', 'desc')->get();

        return view('mypage', compact('posts'), compact('users'));
    }

    /**
     * ユーザネーム変更
     *
     * @return \Illuminate\Http\Response
     */
    public function nameChange(Request $request) {
        
        $user = User::find($request['user_id']);
        $user->name = $request['name'];
        $user->save();

        $ajax_return_data['name'] = $user->name;

        return response()->json($ajax_return_data);
    }

    /**
     * パスワード変更
     *
     * @return \Illuminate\Http\Response
     */
    public function passwordChange(Request $request) {
        
        $user = User::find($request['user_id']);

        //登録しようしてるパスワードと現在、登録中のパスワードを比較し同じであればエラーを入れてレスポンスを返す
        if(Hash::check($request['password'], $user->password)) {
            //同じ値だったとき、クライアントへエラーをレスポンス
            $ajax_return_data['error'] = "違う値で登録してください";

            return response()->json($ajax_return_data);
        } else {
            //パスワードを暗号化してからDBに保存
            $hashPass = Hash::make($request['password']);
            $user->password = $hashPass;
            $user->save();

            return response()->json();
        }

        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
