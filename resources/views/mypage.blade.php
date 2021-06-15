@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card">
                <div class="card-body">
                    <label id="user_name_text" class="row col-md-4 col-form-label text-md-left name">{{ __(Auth::user()->name) }}</label>
                    <label class="row col-md-4 col-form-label text-md-left">{{ __(Auth::user()->email) }}</label>
                    <input type="hidden" name='user_id' id="login_user_id" value="{{ Auth::user()->id }}">
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="btn-group btn-group-lg w-100">
                        <a href="#" class="btn btn-primary border" id="user_name_change_button">{{ __('ユーザ名変更') }}</a>
                        <a href="#" class="btn btn-primary border">{{ __('メールアドレス変更') }}</a>
                        <a href="#" class="btn btn-primary border" id="password_change_button">{{ __('パスワード変更') }}</a>
                        <a href="/newPost" class="btn btn-primary border">{{ __('新規投稿') }}</a>
                    </div>
                </div>
            </div>

            <div class="text-right mt-3">
                <button type="submit" class="btn btn-primary switch_button col-md-2" onclick="location.href='#">
                    {{ __('切替') }}
                </button>
            </div>

            <!-- 全投稿をループ -->
            @foreach($posts as $post)
                <!-- ログイン中のユーザの投稿のみ表示 -->
                @if(Auth::user()->id == $post->user_id)
                    <div class="card mt-3">
                        <div class="card-header text-center">{{ __($post->title) }}</div>
                        
                        <div class="card-body">
                            <label class="row col-md-12 col-form-label text-md-left name">{{ __($post->user->name) }}</label>
                            <label class="row col-md-12 col-form-label text-md-left">{{ __($post->recruitment_area_prefecture) }} {{ __($post->recruitment_area) }}</label>
                            <label class="row col-md-12 col-form-label text-md-left">{{ __($post->recruitment_level) }}</label>
                            <label class="row col-md-12 col-form-label text-md-left">{{ __($post->practice_content) }}</label>
                            <label class="row col-md-12 col-form-label text-md-left">{{ __($post->schedule) }}</label>
                            <!-- 1投稿に紐づく全タグ情報表示 -->
                            <div class="row col-md-12 ">
                            @isset($post->tags)
                                @foreach($post->tags as $tag)
                                    @isset($tag)
                                      <label class="text-md-left"><b>{{ __('#') }}</b> {{ __($tag->tag) }}</label>
                                    @endisset
                                @endforeach
                            @endisset
                            </div>
                            <div class="row justify-content-center">
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-primary col-md-7 border" onclick="location.href='#">
                                        {{ __('編集') }}
                                    </button>
                                    <button type="submit" class="btn btn-danger col-md-7 border" onclick="document.getElementById('post_delete_form{{ $loop->index }}').submit();">
                                        {{ __('削除') }}
                                    </button>
                                    <!-- ボタンをform内に配置するとボタンが横並びしないため、
                                        表示上の削除ボタンが押下されたときonclickで隠れてるform内の削除ボタンのクリックイベントを発火する -->
                                    <form id="post_delete_form{{ $loop->index }}" class="contents_Form" action="{{ url('post/'.$post->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger col-md-7 border" style="display:none">
                                            {{ __('削除') }}
                                        </button>
                                    </form>
                                </div>
                                <div class="btn btn-link likes_btn">
                                    <input type="hidden" name='post_id' id="post_id" value="{{ $post->id }}">
                                    <input type="hidden" name='user_id' id="user_id" value="{{ Auth::user()->id }}">
                                    <!-- 今処理してる投稿に自分がいいねしてるか調べていいね済アイコンか未いいねアイコンのどちらを表示するか判定 -->
                                    <!-- 方法：今ループで処理してる投稿に紐づいてるいいねの中で自分(ログインユーザ)のidと一致してるレコードがあるか条件で検索 -->
                                    @if($post->likes->where('id', Auth::user()->id)->count() >= 1)
                                        <i class="far fa-heart hide">{{ $post->likes->count() }}</i>
                                        <i class="fas fa-heart">{{ $post->likes->count() }}</i>
                                    @else
                                        <i class="far fa-heart">{{ $post->likes->count() }}</i>
                                        <i class="fas fa-heart hide">{{ $post->likes->count() }}</i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

        </div>
    </div>
</div>
@endsection
