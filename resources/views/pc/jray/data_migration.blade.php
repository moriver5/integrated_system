@include('layouts.header')
@extends('layouts.entry_base')
@section('entry')
<main>
<h1 class="ttl_01">データ移行</h1>
<section>
<div class="cont">
	<center>
	@if( isset($finished_flg) )
	<p class="btn_red_02 size_S">データ移行完了済</p>
	<form name="login" id="login" action="{!! url($login_url) !!}" method="post">
		{{csrf_field()}}
		<p class="btn_red_02 size_S">
		<input type="submit" value="新サイトへログイン" />
		<input type="hidden" name="login_id" value="{{ $login_id }}" />
		<input type="hidden" name="password" value="{{ $password }}" />
		</p>
	</form>
	@else

		@if( !empty($error) && $error == 1 )
		<b><font color="red">データ移行に失敗しました。再度、お試しください。<br />
		何度も失敗する場合は下記までご連絡ください。
		<p class="btn_red_02 size_S"><a href="mailto:help@jra-yosou.jp">お問合せ</a></p>
		</font></b>
		@endif

	<form name="migration" id="migration" action="{!! url($data_migration_url) !!}" method="post">
		{{csrf_field()}}
		@if( !empty($mb_mail_address) && !empty($pc_mail_address) )
		<table>
			<tr>
				<td colspan="2">
					<p class="btn_red_02 size_S">新サイトへデータ移行するにあたり、メールアドレスが統合されます。<br />下記から使用するメールアドレスを選択してください。<br />メールアドレスはログイン後の会員情報から変更可能です。</p>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td><input type="radio" name="dm_mail_address" value="{{ $mb_mail_address }}"><br /></td>
				<td>{{ $mb_mail_address }}</td>
			</tr>
			<tr>
				<td><input type="radio" name="dm_mail_address" value="{{ $pc_mail_address }}"><br /></td>
				<td>{{ $pc_mail_address }}</td>
			</tr>
		</table>
		@endif
		<p class="btn_red_02 size_S">
		<button type="submit" id="push_migration" class="btn btn-primary">新サイトへデータ移行する</button>
		<input type="hidden" name="access_key" value="{{ $access_key }}" />
		<input type="hidden" name="dm_mail_status" value="{{ $dm_mail_status }}" />
		</p>
	</form>
	@endif
	</center>
</div>
</section>
</main>

<script type="text/javascript">
var sub_win;
$(document).ready(function(){
	$('#migration').submit(function(event){
		if( $('[name="dm_mail_status"]').val() == 1 ){
			if( $('[name="dm_mail_address"]:checked').val() === undefined ){
				alert('{{ __('messages.dialog_none_mail_address_msg') }}');
				return false;
			}
		}
	});
});
</script>
@endsection