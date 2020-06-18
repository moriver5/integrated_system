@extends('layouts.app')

@section('content')
<br />
<br />
<div class="container">
    <div class="col">
        <div class="col-md-6 col-md-offset-3">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>メルマガ送信失敗リスト</b>

				</div>
				<div class="panel-body">
					<center>{{ $db_data->links() }}</center>
					<table border="1" align="center" width="98%">
						<tr>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:120px;">
								<b>配信日時</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:120px;">
								<b>件数</b>
							</td>
						</tr>
						@foreach($db_data as $lines)
							<tr>
								<td style="padding:5px;text-align:center;">
									{{ $lines->send_date }}
								</td>
								<td style="padding:5px;text-align:center;">
									<a href="{{ url('/admin/member/melmaga/mail/failed/list/emails') }}/{{ $db_data->currentPage() }}/{{ $lines->id }}">{{ $lines->count }}</a>
								</td>
							</tr>
						@endforeach
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
