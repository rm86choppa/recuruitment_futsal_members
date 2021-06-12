$(function () {

    $('#user_name_change_button').on('click', function() {

        //ユーザ名変更の入力欄に初期値として今のユーザネームを取得
        const name = $('#user_name_text').text();

        //入力した変更後のnameを取得
        const user_name_after_change = window.prompt("新しいユーザ名を入力してください", name);
        //更新対象のユーザを検索するためユーザIDを取得
        const user_id = $('.likes_btn').children('#user_id').val();

        $.ajax({
            type: 'get',
            url: '/name_change', //web.phpのURLと同じ形にする
            data: {
                'name': user_name_after_change, //ここはサーバーに贈りたい情報。
                'user_id': user_id,
            },
            dataType: 'json', //json形式で受け取る
            timeout: 900000,
            beforeSend: function () {
            } 
        }).done(function(data){
            
            $('.name').each(function() {
                //変更後のnameの値で表示されてるネーム情報更新
                $(this).text(data['name']);
            });
            alert('ユーザ名を変更しました');

        }).fail(function(){
            alert("通信に失敗しました");
        });
    });
});