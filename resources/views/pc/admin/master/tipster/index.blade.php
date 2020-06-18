@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading" style="background:wheat;"><b>予想師設定</b></div>
				<div class="panel-body">
                    <form id="formTipsters" class="form-horizontal" method="POST" action="/admin/member/master/tipster/setting/send">
						{{ csrf_field() }}
						<center>
							<!-- タブの中身 -->
							<div>
								<div class="form-group" style="align:center;">
									{{ $db_data->links() }}
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td style="padding:1px 3px;width:30px;">ID</td>
											<td style="padding:1px 0px;width:35px;">３つ星予想師</td>
											<td style="padding:1px 0px;width:35px;">名前</td>
											<td style="padding:1px 0px;width:30px;">有効</td>
										</tr>
										@foreach($db_data as $index => $lines)
										<tr class="slt_group" id="slt_group{{ $lines->id }}" style="text-align:center;">
											<td>{{ $lines->id }}</td>
											<td>
												@if( $lines->is_star )
													〇
												@else
													－
												@endif
											</td>
											<td><a href="/admin/member/master/tipster/setting/edit/{{ $lines->id }}" target="_blank">{{ $lines->name }}</a></td>
											<td>
												@if( $lines->disp_flg )
													〇
												@else
													－
												@endif
											</td>
										</tr>
										@endforeach
									</table>
								</div>
								<a href="/admin/member/master/tipster/setting/create" class="btn btn-primary" target="_blank">新規作成</a>
							</div>
						</center>
					</form>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
