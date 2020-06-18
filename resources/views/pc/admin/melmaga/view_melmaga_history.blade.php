@extends('layouts.app')

@section('content')
<br />
<br />
<div class="container">
    <div class="col">
        <div class="col-md-7 col-md-offset-3">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>配信先設定</b>
				</div>
				<div class="panel-body">
					<table border="1" align="center" width="98%">
						<tr>
							<td class="admin_table" style="width:60px;">
								<b>抽出項目</b>
							</td>
							<td style="width:200px;padding:5px;">
								@if( isset($items->search_value) )
								{{ $items->search_item }}
									@if( !empty($items->search_value) )
									{{$items->search_value}}
									@endif
									@if( !empty($items->search_type) )
									{{$items->search_type}}
									@endif
								@else
									@if( !empty($items) )
									なし
									@endif
								@endif
							</td>
						</tr>
						<tr>
							<td class="admin_table" style="width:60px;">
								<b>グループ</b>
							</td>
							<td style="width:40px;padding:5px;">
								@if( isset($items->groups) )
								@foreach($groups as $id => $name)
									@if( in_array($id, explode(",",$items->groups)))
										【{{ $name }}】
									@endif
								@endforeach
								@else
									@if( !empty($items) )
									なし
									@endif
								@endif
							</td>
						</tr>
						<tr>
							<td class="admin_table" style="width:60px;">
								<b>登録状態</b>
							</td>
							<td style="width:40px;padding:5px;">
								@if( !empty($items->status) )
								{{ $items->status }}
								@else
									@if( !empty($session) )
									なし
									@endif
								@endif
							</td>
						</tr>
						<tr>
							<td class="admin_table" style="width:60px;">
								<b>未決済注文</b>
							</td>
							<td style="width:40px;padding:5px;">
								@if( !empty($items->settlement) )
								{{ $items->settlement }}
								@else
									関係なく
								@endif
							</td>
						</tr>
<!--
						<tr>
							<td class="admin_table" style="width:60px;">
								<b>送信端末</b>
							</td>
							<td style="width:40px;padding:5px;">
							</td>
						</tr>
-->
						<tr>
							<td class="admin_table" style="width:60px;">
								<b>登録日時</b>
							</td>
							<td style="width:40px;padding:5px;">
								@if( !empty($items->start_regdate) )
									{{ $items->start_regdate }} ~ 
								@endif
								@if( !empty($items->end_regdate) )
									{{ $items->end_regdate }}
								@endif
							</td>
						</tr>
					</table>
				</div>
			</div>

			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>メルマガ配信履歴</b>
				</div>
				<div class="panel-body">
					<table border="1" align="center" width="98%">
						@if( !empty($email) )
						<tr>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>送信先アドレス</b>
							</td>
						</tr>
						<tr>
							<td style="padding:5px;text-align:center;">
								{{ $email }}
							</td>
						</tr>
						@endif
						<tr>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>件名</b>
							</td>
						</tr>
						<tr>
							<td style="padding:5px;text-align:center;">
								{{ $db_data->subject }}
							</td>
						</tr>
						<tr>
							<td colspan="2" style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>内容</b>
								@if( !empty($db_data->html_body) )
								(HTML形式)
								@else
								(テキスト形式)
								@endif
							</td>
						</tr>
						<tr>
							<td colspan="2" style="padding:5px;">
								@if( !empty($db_data->html_body) )
								{!! $db_data->html_body !!}
								@else
								{!! preg_replace("/\n/","<br />",htmlspecialchars($db_data->text_body)) !!}
								@endif
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
var search_win;
$(document).ready(function(){
	//検索設定ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/client/search/setting', 'convert_table', 'width=700, height=655');
		return false;
	});

	//新規作成ボタン押下
	$('#create').on('click', function(){
		search_win = window.open('/admin/member/client/create', 'create', 'width=1000, height=655');
		return false;
	});
});
</script>

@endsection
