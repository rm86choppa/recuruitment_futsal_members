<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\User;
use App\Tag;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //全投稿情報取得(投稿に紐づくユーザ、タグ、いいね情報も取得)
        $posts = Post::with('user', 'tags', 'likes', 'applications')->orderBy('updated_at', 'desc')->get();

        //ユーザの投稿一覧を表示する情報取得
        $users = User::with('posts')->orderby('updated_at', 'desc')->get();

        //タグを選択し、選択したタグに紐づく投稿を取得する
        $tags = Tag::with('posts')->orderby('updated_at', 'desc')->get();

        return view('home', compact('posts', 'users', 'tags'));
    }

    /**
     * sorttypeを受け取り、typeに応じた並べ替え後の投稿view部分を返す
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function sort() {

        //全投稿情報取得(投稿に紐づくユーザ、タグ、いいね情報も取得)
        $posts = Post::with('user', 'tags', 'likes', 'applications')->withcount('applications')->orderBy('applications_count', 'desc')->get();

        //ユーザの投稿一覧を表示する情報取得
        $users = User::with('posts')->orderby('updated_at', 'desc')->get();

        //タグを選択し、選択したタグに紐づく投稿を取得する
        $tags = Tag::with('posts')->orderby('updated_at', 'desc')->get();

        return view('home', compact('posts', 'users', 'tags'));
    }
}
