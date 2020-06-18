@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-15 col-md-offset-1">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>データ移行失敗一覧</b>
				</div>

				<div class="panel-body">
					<span class="admin_default" style="margin-left:10px;">
						全件数：{{$total }} 件
						({{$currentPage}} / {{$lastPage}}㌻)
					</span>
					<center>{{ $links }}</center>
					<table border="1" align="center" width="99%">
						<tr>
							<td class="admin_table" style="width:7%;">
								<b>ID</b>
							</td>
							<td class="admin_table" style="width:7%;">
								<b>ログインID</b>
							</td>
							<td class="admin_table" style="width:33%;">
								<b>メールアドレス</b>
							</td>
							<td class="admin_table" style="width:5%;">
								<b>状況</b>
							</td>
							<td class="admin_table" style="width:5%;">
								<b>退会</b>
							</td>
							<td class="admin_table" style="width:4%;">
								<b>停止</b>
							</td>
							<td class="admin_table" style="width:11%;">
								<b>登録日時</b>
							</td>
							<td class="admin_table" style="width:11%;">
								<b>最終アクセス日時</b>
							</td>
							<td class="admin_table" style="width:17%;">
								<b>メモ</b>
							</td>
						</tr>
						@if( !empty($db_data) )
							@foreach($db_data as $ad_cd => $lines)
								<tr>
									<td style="padding:2px;text-align:center;">
										{{ $lines->client_id }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->login_id }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->email }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ config('const.disp_regist_status')[$lines->status] }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->is_quit ? "退会済":"" }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->disable ? "停止":"" }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->reg_date }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->last_access_date }}
									</td>
									<td style="padding:2px;text-align:left;">
										@php  
											$listData = explode(",",$lines->memo);
											if( count($listData) > 0 ){
												foreach($listData as $msg){
													list($column, $err) = explode(":", $msg);
													if( $column == 'login_id' ){
														if( $err == 'Unique' ){
															echo "ログインID重複<br>";
														}elseif( $err == 'none' ){
															echo "ログインIDなし<br>";
														}
													}
													if( $column == 'email' || $column == 'pc_email'){
														if( $err == 'Unique' ){
															echo "メールアドレス重複<br>";
														}elseif( $err == 'none' ){
															echo "メールアドレスなし<br>";
														}
													}
												}
											}else{
												echo $lines->memo;
											}
										@endphp
									</td>
								</tr>
							@endforeach
						@endif
					</table>
				</div>
			</div>	
		</div>	
	</div>	

</div>

@endsection
