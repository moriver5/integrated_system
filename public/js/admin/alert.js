    /*
     *  リターンキーでのpostを無効にする
     */
    $("input").keydown(function(e) {
        if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
                return false;
        } else {
                return true;
        }
    });

    /*
     * submitボタンを押下したときに表示するダイアログ確認メッセージ
     * 引数：FormのID、Formのmethod、dialogのtitle、dialogのmsg、通信完了後にdialogに表示させるmsg、dialogのcancelmsg、timeout、　完了後にmsgを表示させるかのflg、cancel時にmsgを表示させるかのflg、リダイレクトさせるかのflg、リダイレクトURL
     */
    function submitAlert(form_id, method, dialog_title, dialog_msg, dialog_end_msg, dialog_cancel_msg, timeout, result_msg_flg, cancel_flg, redirect_flg, redirect_url){
        //タイムアウトの指定がないときデフォルトのタイムアウトを設定
        if( timeout == undefined ){
                timeout = 10000;
        }

        if( method == undefined ){
                method = 'post';
        }

        //アカウント編集ボタン押下
        $('#' + form_id).submit(function(event){
            //確認ダイアログ表示
            swal({
              title: dialog_title,
              text: dialog_msg,
              icon: "warning",
              buttons: true,
              dangerMode: true,
            })
            .then((exec_flg) => {
                    //OKボタンを押下
                    if( exec_flg ){
                            event.preventDefault();

                            //ajax通信(アカウント編集処理)
                            $.ajax({
                                    url: $(this).prop('action'),
                                    type: method,
                                    data: $(this).serialize(),
                                    timeout: timeout,
                                    success:function(result_flg){
//alert(result_flg);
                                        //通信完了後のメッセージ
                                        if( result_msg_flg == true ){
                                            swal(dialog_end_msg, {
                                              icon: "success", 
                                            }).then(() => {
                                                //親ウィンドウがあればリロード
                                                if( window.opener ){
                                                    window.opener.location.reload();
                                                }
                                                //redirect_flgがtrueならリダイレクト
                                                if( redirect_flg == true ){
                                                        window.location.href = redirect_url;
                                                        return false;
                                                }
                                                window.location.reload();
                                            });
                                        }else{
                                            window.location.reload();
                                        }
                                    },
                                    error: function(error) {
                                            //エラーメッセージ表示
                                            var list_error= JSON.parse(error.responseText);
//alert(error.responseText);
                                            for (var item in list_error){
                                                    swal("'"+list_error[item]+"'"); 
                                            }
                                    }
                            });

                    //キャンセルボタンを押下
                    }else{
                            //キャンセルしたときに表示させるメッセージ
                            if( cancel_flg === true ){
                                    swal(dialog_cancel_msg); 
                            }
                    }
            });

            return false;
        });
    }
