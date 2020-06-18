@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-7 col-md-offset-3">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>出力用画像設定</b>
				</div>
                <div class="panel-body">
                    <form id="formImageUpload" class="form-horizontal" method="POST" action="/admin/member/master/img/upload/send">
                        {{ csrf_field() }}
                        <div class="form-group">
							<table>
								<tr>
									<td>
										<div class="col-md-12"　id="file_upload_section" style="width:100%;">
											<div id="drop" style="text-align:center;width:700px;height:200px; vertical-align:middle; display:table-cell; border:3px solid burlywood;" ondragleave="onDragLeave(event, 'drop', 'white')" ondragover="onDragOver(event, 'drop', 'wheat')" ondrop="onDrop(event, 'formImageUpload', 'import_file', '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id'], 'post', '10000', '')">
												<div style="font:italic normal bold 16px/150% 'メイリオ',sans-serif;color:silver;">
													<span style="font-size:12px;color:red;">※1つの画像でアップロードできるサイズは30Mｂまでです</span><br />
													アップロードするファイルをここに<br />ドラッグアンドドロップしてください<br />(複数選択の画像アップロード可能)
												</div>
												<center><div id="result" style="font:italic normal bold 16px/150% 'メイリオ',sans-serif;margin:10px;width:100%;"></div></center>
											</div>
										</div>
									</td>
								</tr>
							</table>
                        </div>
						<input type="hidden" name="edit_id" value="">
					</form>
                </div>
            </div>
        </div>

        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>アップロード画像</b>
					<span class="admin_default" style="margin-left:10px;">
						全件数：{{$total }} 件
						({{$currentPage}} / {{$lastPage}}㌻)
	
					</span>
				</div>
                <div class="panel-body">
					<!-- 画像が登録されていれば表示 -->
					@if( !empty($list_img) )
						<form id="formImgDel" class="form-horizontal" method="POST" action="/admin/member/master/img/upload/delete">
						{{ csrf_field() }}
						<table style="width:100%;" rules="rows">
							<tr>
								<td style="padding:5px; 5px 5px 5px;border:none;border-bottom:1px solid #aaa;color:black;font:bold 12px/120% 'メイリオ',sans-serif;" colspan="4">
									表示順：<a href="/admin/member/master/img/upload/created_at">画像登録日</a>&nbsp;&nbsp;&nbsp;<a href="/admin/member/master/img/upload/size">画像容量</a>
									<center>{{ $links }}</center>
									<input type="checkbox" id="del_all" name="del_all" value="1"> すべて選択(ON/OFF)
								</td>
							</tr>
						@foreach($list_img as $index => $lines)
							@if( $index % 4 == 0 )
								<tr>
							@endif
								<td style="padding:5px;border:none;border-bottom:1px dotted #aaa;color:black;font:bold 12px/120% 'メイリオ',sans-serif;">
									<div>
									<input type="checkbox" class="del" name="del_img[]" value="{{ $lines['img'] }}">
									<b><a href="{{ config('const.list_url_const')[Session::get('operation_select_db')] }}{{ $img_path }}/{{ $lines['img'] }}" data-lightbox="group" data-lightbox="roadtrip" data-title='&lt;img src="{{ config('const.list_url_const')[Session::get('operation_select_db')] }}{{ $img_path }}/{{ $lines['img'] }}"&gt;'><canvas src="{{ config('const.list_url_const')[Session::get('operation_select_db')] }}{{ $img_path }}/{{ $lines['img'] }}" id="img{{ $lines['img'] }}" class="img">{{ $lines['date'] }}<>{{ $lines['size'] }}</canvas></a></b>
									</div>
<!--
									<div style="font-size:0.6em;">
										登録：{{ $lines['date'] }}<br />容量：{{ $lines['size'] }}<br />
									</div>
-->
								</td>
							@if( $index % 4 == 3 )
								</tr>
							@endif
						@endforeach
						</table>
						<br />
						<div>
							<div style="text-align:center;">
								<button id="push_btn" type="submit" class="btn btn-primary">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;画像削除&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								</button>
							</div>
						</div>
					@else
						<div style="text-align:center;"><b>画像未設定</b></div>
					@endif
					<div style="color:black;font:bold 13px/130% 'メイリオ',sans-serif;">
						<hr>
						<b>
							※ 画像を使用する際には<br />
							&lt;img src="/images/upload_images/[image名]" alt="***" width="***" height="***" /&gt;<br />
							例）&lt;img src="/images/upload_images/1.jpg" alt="test" width="100" height="50" /&gt;<br />
							例）&lt;img src="{{ config('const.list_url_const')[Session::get('operation_select_db')] }}/images/upload_images/1.jpg" alt="test" width="100" height="50" /&gt;
						</b>
					</div>
					</form>
                </div>

            </div>
		</div>
    </div>
</div>

<link href="{{ asset('css/admin/lightbox.css') }}" rel="stylesheet" />

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/file_upload.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/ajax.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/lightbox.js') }}"></script>
<script type="text/javascript">
$(document).ready(function(){
    lightbox.option({
		'alwaysShowNavOnTouchDevices':true,
		'wrapAround': true
    });

	$('#img_copy').on('click', function(){
		//imgタグをコピー
		var copy_flg = execCopy($(this).text());

		//コピー範囲をアクティブにする処理
		var range = document.createRange();
		range.selectNodeContents(this);

		var selection = window.getSelection();

		//選択をすべてクリア
		selection.removeAllRanges();

		//新規の範囲を選択に指定
		selection.addRange(range);

		alert('タグをコピーしました');		
		return false;
	});

	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
		return false;
	});

	//削除のすべて選択のチェックをOn/Off
	$('#del_all').on('change', function() {
		$('.del').prop('checked', this.checked);
	});

	//画像削除ボタンを押下
	$('#push_btn').click(function(){
		//画像削除ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formImgDel', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.add_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false);
	});

});

window.addEventListener('load', function(){

	$('.img').each(function(){
/* リサイズのみVer
		var targetImg = document.getElementById(this.id);
		var orgWidth  = targetImg.width;  // 元の横幅を保存
		var orgHeight = targetImg.height; // 元の高さを保存
		targetImg.width = 150;  // 横幅を110pxにリサイズ
		targetImg.height = orgHeight * (targetImg.width / orgWidth);
*/
		/*
		 * リサイズ&テキスト合成
		 */
		var list_text = $(this).text().split('<>');
		var canvas = document.getElementById(this.id);

		var baseImg = new Image();
		baseImg.src = $(this).attr('src');
		baseImg.onload = function() {
			var orgWidth  = baseImg.width;  // 元の横幅を保存
			var orgHeight = baseImg.height; // 元の高さを保存

			baseImg.width = 180;  // 横幅を110pxにリサイズ
			baseImg.height = orgHeight * (baseImg.width / orgWidth);

			canvas.width = baseImg.width;
			canvas.height = baseImg.height;

			//キャンパス描画&フォント設定
			var ctx = canvas.getContext('2d');
			ctx.drawImage(baseImg, 0, 0, baseImg.width, baseImg.height);
			ctx.fillStyle = "rgb(250, 250, 0)";
			ctx.fillRect(0,0,49,21);
			ctx.fillStyle = 'rgb(0, 0, 0)';
			ctx.font = "8px メイリオ";
			ctx.fillText(list_text[0], 0, 10);
			ctx.fillText(list_text[1], 0, 20);
		}
	});
});

/*
 * マウスクリックでクリップボードにコピー
 */
function execCopy(string){
	var temp = document.createElement('div');

	temp.appendChild(document.createElement('pre')).textContent = string;

	var s = temp.style;
	s.position = 'fixed';
	s.left = '-100%';

	document.body.appendChild(temp);
	document.getSelection().selectAllChildren(temp);

	var result = document.execCommand('copy');

	document.body.removeChild(temp);

	// true なら実行できている falseなら失敗か対応していないか
	return result;
}

</script>

@endsection
