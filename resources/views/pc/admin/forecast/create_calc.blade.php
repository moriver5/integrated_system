<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta name="robots" content="noindex,nofollow">
    <meta charset="utf-8">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache">
	<meta http-equiv="Expires" content="0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('const.html_admin_title') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/admin/app.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/admin/admin.css') }}" rel="stylesheet" />
	<link href="{{ asset('css/admin/jquery.datetimepicker.css') }}" rel="stylesheet" />
	
	<!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	
	<!-- jQuery Liblary -->
	<script src="{{ asset('js/admin/jquery.datetimepicker.full.min.js') }}"></script>
	<script>var aa = bb = cc = 0;</script>
</head>
<body>
<br />
<center>
<div class="container" style="width:100%;">
    <div class="col">
        <div class="col-md-12">
			<form name="form1" method="post" action="" style="margin: 0; padding: 0;">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<span style="float:left;">
						<input type="button" id="go_jra" value="JRAサイト" class="formbutton">
					</span>
					<b>フォーメーション組合せ数計算</b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;"><b>close</b></span>
					<div class="clear_btn mt20" style="float:right;margin-right:10px;">
						<input type="reset" name="umaclear" value="入力クリア" onClick="PAIRCHECK(this);" class="formbutton" style="float:right;">
					</div>
				</div>
                <div class="panel-body">
				<table border=1 align=center width="99%">
					<tr style="text-align:center;">
						<th class="admin_table">馬番</th>
						<td>1</td>
						<td>2</td>
						<td>3</td>
						<td>4</td>
						<td>5</td>
						<td>6</td>
						<td>7</td>
						<td>8</td>
						<td>9</td>
						<td>10</td>
						<td>11</td>
						<td>12</td>
						<td>13</td>
						<td>14</td>
						<td>15</td>
						<td>16</td>
						<td>17</td>
						<td>18</td>
					</tr>
					<tr style="text-align:center;">
						<td class="admin_table">当社おススメ馬</td>
						<td><input type="radio" name="num" value="1"></td>
						<td><input type="radio" name="num" value="2"></td>
						<td><input type="radio" name="num" value="3"></td>
						<td><input type="radio" name="num" value="4"></td>
						<td><input type="radio" name="num" value="5"></td>
						<td><input type="radio" name="num" value="6"></td>
						<td><input type="radio" name="num" value="7"></td>
						<td><input type="radio" name="num" value="8"></td>
						<td><input type="radio" name="num" value="9"></td>
						<td><input type="radio" name="num" value="10"></td>
						<td><input type="radio" name="num" value="11"></td>
						<td><input type="radio" name="num" value="12"></td>
						<td><input type="radio" name="num" value="13"></td>
						<td><input type="radio" name="num" value="14"></td>
						<td><input type="radio" name="num" value="15"></td>
						<td><input type="radio" name="num" value="16"></td>
						<td><input type="radio" name="num" value="17"></td>
						<td><input type="radio" name="num" value="18"></td>
					</tr>
				</table>
				</div>

                <div class="panel-body">
					<div id="form_list">
						<table border=1 align=center width="99%">
							<tr>
								<th rowspan="2" class="admin_table">&nbsp;</th>
								<th colspan="19" class="admin_table">枠番・馬番</th>
							</tr>
							<tr class="admin_table">
								<td>1</td>
								<td>2</td>
								<td>3</td>
								<td>4</td>
								<td>5</td>
								<td>6</td>
								<td>7</td>
								<td>8</td>
								<td>9</td>
								<td>10</td>
								<td>11</td>
								<td>12</td>
								<td>13</td>
								<td>14</td>
								<td>15</td>
								<td>16</td>
								<td>17</td>
								<td>18</td>
								<td>全通り</td>
							</tr>
							<tr style="text-align:center;">
								<td class="admin_table">1着・1頭目</td>
								<td><input type="checkbox" class="formation1" name="uma" value="1" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation1" name="uma" value="2" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation1" name="uma" value="3" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation1" name="uma" value="4" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation1" name="uma" value="5" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation1" name="uma" value="6" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation1" name="uma" value="7" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation1" name="uma" value="8" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation1" name="uma" value="9" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation1" name="uma" value="10" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation1" name="uma" value="11" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation1" name="uma" value="12" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation1" name="uma" value="13" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation1" name="uma" value="14" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation1" name="uma" value="15" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation1" name="uma" value="16" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation1" name="uma" value="17" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation1" name="uma" value="18" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation1" name="uma" value="99" onClick="PAIRCHECK(this);"></td>
							</tr>
							<tr style="text-align:center;">
								<td class="admin_table">2着・2頭目</td>
								<td><input type="checkbox" class="formation2" name="umb" value="1" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation2" name="umb" value="2" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation2" name="umb" value="3" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation2" name="umb" value="4" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation2" name="umb" value="5" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation2" name="umb" value="6" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation2" name="umb" value="7" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation2" name="umb" value="8" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation2" name="umb" value="9" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation2" name="umb" value="10" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation2" name="umb" value="11" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation2" name="umb" value="12" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation2" name="umb" value="13" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation2" name="umb" value="14" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation2" name="umb" value="15" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation2" name="umb" value="16" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation2" name="umb" value="17" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation2" name="umb" value="18" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation2" name="umb" value="99" onClick="PAIRCHECK(this);"></td>
							</tr>
							<tr style="text-align:center;">
								<td class="admin_table">3着・3頭目</td>
								<td><input type="checkbox" class="formation3" name="umc" value="1" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation3" name="umc" value="2" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation3" name="umc" value="3" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation3" name="umc" value="4" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation3" name="umc" value="5" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation3" name="umc" value="6" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation3" name="umc" value="7" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation3" name="umc" value="8" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation3" name="umc" value="9" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation3" name="umc" value="10" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation3" name="umc" value="11" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation3" name="umc" value="12" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation3" name="umc" value="13" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation3" name="umc" value="14" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation3" name="umc" value="15" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation3" name="umc" value="16" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation3" name="umc" value="17" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation3" name="umc" value="18" onClick="PAIRCHECK(this);"></td>
								<td><input type="checkbox" class="formation3" name="umc" value="99" onClick="PAIRCHECK(this);"></td>
							</tr>
							<tr style="text-align:center;">
								<td class="admin_table">同枠あり</td>
								<td><input type="checkbox" class="waku" name="dwk" value="1" onClick="PAIRCHECK(this);" checked></td>
								<td><input type="checkbox" class="waku" name="dwk" value="2" onClick="PAIRCHECK(this);" checked></td>
								<td><input type="checkbox" class="waku" name="dwk" value="3" onClick="PAIRCHECK(this);" checked></td>
								<td><input type="checkbox" class="waku" name="dwk" value="4" onClick="PAIRCHECK(this);" checked></td>
								<td><input type="checkbox" class="waku" name="dwk" value="5" onClick="PAIRCHECK(this);" checked></td>
								<td><input type="checkbox" class="waku" name="dwk" value="6" onClick="PAIRCHECK(this);" checked></td>
								<td><input type="checkbox" class="waku" name="dwk" value="7" onClick="PAIRCHECK(this);" checked></td>
								<td><input type="checkbox" class="waku" name="dwk" value="8" onClick="PAIRCHECK(this);" checked></td>
								<td colspan="11" style="text-align:left;">←チェックされた枠の同枠を組数に加算します。</td>
							</tr>
						</table>
						</form>
					</div>
					<br />
					<div id="form_result">
						<form name="form2" method="post" action="" onSubmit="return keisan(this.bet.value,this.kin.value);" style="margin: 0; padding: 0;">
						<table border=1 align=center width="100%">
							<tr style="text-align:center;">
								<td colspan="9" class="head" style="padding:5px;background:wheat;"> <b>フォーメーションの組合せ数</b></td>
							</tr>
							<tr align="center">
								<td style="padding:5px;">枠連</td>
								<td style="padding:5px;">馬連</td>
								<td style="padding:5px;">馬単</td>
								<td style="padding:5px;">３連複</td>
								<td style="padding:5px;">３連単</td>
							</tr>
							<tr>
								<td style="padding:5px;">
									<input type="text" id="wakuren" class="formtext" name="wakuren" size="4" readonly> 点&nbsp;&nbsp;<input type="button" name="wakuren_reflect" id="wakuren_reflect" value="反映" class="formbutton"><BR>
								</td>
								<td style="padding:5px;">
									<input type="text" id="umaren" class="formtext" name="umaren" size="4" readonly> 点&nbsp;&nbsp;<input type="button" name="umaren_reflect" id="umaren_reflect" value="反映" class="formbutton"><BR>
								</td>
								<td style="padding:5px;">
									<input type="text" id="umatan" class="formtext" name="umatan" size="4" readonly> 点&nbsp;&nbsp;<input type="button" name="umatan_reflect" id="umatan_reflect" value="反映" class="formbutton">
								</td>
								<td style="padding:5px;">
									<input type="text" id="sanfuku" class="formtext" name="sanfuku" size="4" readonly> 点&nbsp;&nbsp;<input type="button" name="sanfuku_reflect" id="sanfuku_reflect" value="反映" class="formbutton"><br>
								</td>
								<td style="padding:5px;">
									<input type="text" id="santan" class="formtext" name="santan" size="4" readonly> 点&nbsp;&nbsp;<input type="button" name="santan_reflect" id="santan_reflect" value="反映" class="formbutton"><br>
								</td>
							</tr>
							<tr align="center">
								<td colspan="9" style="padding:5px;background:wheat;"><b>BOXの組合せ数</b></td>
							</tr>
							<tr align="center">
								<td style="padding:5px;">枠連/馬連/ワイド</td>
								<td style="padding:5px;">馬単</td>
								<td style="padding:5px;">３連複</td>
								<td style="padding:5px;">３連単</td>
							</tr>
							<tr>
								<td style="padding:5px;">
									<input type="text" id="ren_box" class="formtext" name="renbox" size="4" readonly> 点&nbsp;&nbsp;<input type="button" name="renbox_reflect" id="renbox_reflect" value="反映" class="formbutton"><br>
								</td>
								<td style="padding:5px;">
									<input type="text" id="umatan_box" class="formtext" name="umatanbox" size="4" readonly> 点&nbsp;&nbsp;<input type="button" name="umatanbox_reflect" id="umatanbox_reflect" value="反映" class="formbutton"><br>
								</td>
								<td style="padding:5px;">
									<input type="text" id="renfuku_box" class="formtext" name="renfukubox" size="4" readonly> 点&nbsp;&nbsp;<input type="button" name="renfukubox_reflect" id="renfukubox_reflect" value="反映" class="formbutton"><br>
								</td>
								<td style="padding:5px;">
									<input type="text" id="rentan_box" class="formtext" name="rentanbox" size="4" readonly> 点&nbsp;&nbsp;<input type="button" name="rentanbox_reflect" id="rentanbox_reflect" value="反映" class="formbutton"><br>
								</td>
							</tr>
							<tr align="center">
								<td colspan="9" style="padding:5px;background:wheat;"><b>流しの組合せ数</b></td>
							</tr>
							<tr align="center">
								<td rowspan="2" style="padding:5px;">枠連/馬連/ワイド</td>
								<td colspan="2" style="padding:5px;">馬単</td>
								<td colspan="2" style="padding:5px;">３連複</td>
								<td colspan="2" style="padding:5px;">３連単</td>
								<td colspan="2" style="padding:5px;">３連単（マルチ）</td>
							</tr>
							<tr align="center">
								<td style="padding:5px;"></td>
								<td style="padding:5px;">マルチ</td>
								<td style="padding:5px;">軸1頭</td>
								<td style="padding:5px;">軸2頭</td>
								<td style="padding:5px;">軸1頭</td>
								<td style="padding:5px;">軸2頭</td>
								<td style="padding:5px;">軸1頭</td>
								<td style="padding:5px;">軸2頭</td>
							</tr>
							<tr align="center">
								<td style="padding:5px;">
									<input type="text" id="wide_wheel" class="formtext" name="wide_wheel" size="4" readonly> 点&nbsp;&nbsp;<input type="button" name="wide_wheel_reflect" id="wide_wheel_reflect" value="反映" class="formbutton"><BR>
								</td>
								<td style="padding:5px;">
									<input type="text" id="umatan_wheel" class="formtext" name="umatan_wheel" size="4" readonly> 点&nbsp;&nbsp;<input type="button" name="umatan_wheel_reflect" id="umatan_wheel_reflect" value="反映" class="formbutton"><BR>
								</td>
								<td style="padding:5px;">
									<input type="text" id="umatan_multi_wheel" class="formtext" name="umatan_multi_wheel" size="4" readonly> 点&nbsp;&nbsp;<input type="button" name="umatan_multi_wheel_reflect" id="umatan_multi_wheel_reflect" value="反映" class="formbutton"><BR>
								</td>
								<td style="padding:5px;">
									<input type="text" id="renfuku_wheel1" class="formtext" name="renfuku_wheel1" size="4" readonly> 点&nbsp;&nbsp;<input type="button" name="renfuku_wheel1_reflect" id="renfuku_wheel1_reflect" value="反映" class="formbutton"><BR>
								</td>
								<td style="padding:5px;">
									<input type="text" id="renfuku_wheel2" class="formtext" name="renfuku_wheel2" size="4" readonly> 点&nbsp;&nbsp;<input type="button" name="renfuku_wheel2_reflect" id="renfuku_wheel2_reflect" value="反映" class="formbutton"><BR>
								</td>
								<td style="padding:5px;">
									<input type="text" id="rentan_wheel1" class="formtext" name="rentan_wheel1" size="4" readonly> 点&nbsp;&nbsp;<input type="button" name="rentan_wheel1_reflect" id="rentan_wheel1_reflect" value="反映" class="formbutton"><BR>
								</td>
								<td style="padding:5px;">
									<input type="text" id="rentan_wheel2" class="formtext" name="rentan_wheel2" size="4" readonly> 点&nbsp;&nbsp;<input type="button" name="rentan_wheel2_reflect" id="rentan_wheel2_reflect" value="反映" class="formbutton"><BR>
								</td>
								<td style="padding:5px;">
									<input type="text" id="rentan_multi_wheel1" class="formtext" name="rentan_multi_wheel1" size="4" readonly> 点&nbsp;&nbsp;<input type="button" name="rentan_multi_wheel1_reflect" id="rentan_multi_wheel1_reflect" value="反映" class="formbutton"><BR>
								</td>
								<td style="padding:5px;">
									<input type="text" id="rentan_multi_wheel2" class="formtext" name="rentan_multi_wheel2" size="4" readonly> 点&nbsp;&nbsp;<input type="button" name="rentan_multi_wheel2_reflect" id="rentan_multi_wheel2_reflect" value="反映" class="formbutton"><BR>
								</td>
							</tr>
						</table>
						</form>
					</div>

                </div>
            </div>
        </div>
    </div>
</div>
</center>

<script language="JavaScript">
	var first	 = {};
	var second	 = {};
	var third	 = {};
	var waku	 = {};
$(document).ready(function(){


	$('#go_jra').on('click', function(){
		window.open('http://www.jra.go.jp/','_blank');
		
		return false;
	});

	//１着・１頭目
	$('.formation1').on('click', function(){
		if( $(this).prop('checked') ){
			if( $(this).val() == 99 ){
				for(i=1;i<=18;i++){
					first[i] = "("+i+")";
				}
			}else{
				first[$(this).val()] = "("+$(this).val()+")";
			}
		}else{
			if( $(this).val() == 99 ){
				for(i=1;i<=18;i++){
					delete first[i];
				}
			}else{
				delete first[$(this).val()];
			}
		}
	});

	//２着・２頭目
	$('.formation2').on('click', function(){
		if( $(this).prop('checked') ){
			if( $(this).val() == 99 ){
				for(i=1;i<=18;i++){
					second[i] = "("+i+")";
				}
			}else{
				second[$(this).val()] = "("+$(this).val()+")";
			}
		}else{
			if( $(this).val() == 99 ){
				for(i=1;i<=18;i++){
					delete second[i];
				}
			}else{
				delete second[$(this).val()];
			}
		}
	});

	//３着・３頭目
	$('.formation3').on('click', function(){
		if( $(this).prop('checked') ){
			if( $(this).val() == 99 ){
				for(i=1;i<=18;i++){
					third[i] = "("+i+")";
				}
			}else{
				third[$(this).val()] = "("+$(this).val()+")";
			}
		}else{
			if( $(this).val() == 99 ){
				for(i=1;i<=18;i++){
					delete third[i];
				}
			}else{
				delete third[$(this).val()];
			}
		}	
	});

	//同枠あり
	$('.waku').on('click', function(){
		if( $(this).prop('checked') ){
			waku[$(this).val()] = "("+$(this).val()+")";
		}else{
			delete waku[$(this).val()];
		}
	});

	//枠連を内容に反映
	$('#wakuren_reflect').on('click', function(){
		var formation_msg = getFormationMsg('', 'wakuren', '(枠連フォーメーション)', first, second, third);

		reflectFormation(formation_msg);
	});

	//馬連を内容に反映
	$('#umaren_reflect').on('click', function(){
		var formation_msg = getFormationMsg('', 'umaren', '(馬連フォーメーション)',　first, second, third);

		reflectFormation(formation_msg);
	});

	//馬単を内容に反映
	$('#umatan_reflect').on('click', function(){
		var formation_msg = getFormationMsg('', 'umatan', '(馬単フォーメーション)',　first, second, third);

		reflectFormation(formation_msg);
	});

	//３連複を内容に反映
	$('#sanfuku_reflect').on('click', function(){
		var formation_msg = getFormationMsg('', 'sanfuku', '(３連複フォーメーション)',　first, second, third);

		reflectFormation(formation_msg);
	});

	//３連単を内容に反映
	$('#santan_reflect').on('click', function(){
		var formation_msg = getFormationMsg('', 'santan', '(３連単フォーメーション)',　first, second, third);

		reflectFormation(formation_msg);
	});

	//を内容に反映
	$('#renbox_reflect').on('click', function(){
		var formation_msg = getFormationMsg('box', 'ren_box', '(枠連/馬連/ワイド)',　first, second, third);

		reflectFormation(formation_msg);
	});

	//を内容に反映
	$('#umatanbox_reflect').on('click', function(){
		var formation_msg = getFormationMsg('box', 'umatan_box', '(馬単BOX)',　first, second, third);

		reflectFormation(formation_msg);
	});

	//を内容に反映
	$('#renfukubox_reflect').on('click', function(){
		var formation_msg = getFormationMsg('box', 'renfuku_box', '(３連複BOX)',　first, second, third);

		reflectFormation(formation_msg);
	});

	//を内容に反映
	$('#rentanbox_reflect').on('click', function(){
		var formation_msg = getFormationMsg('box', 'rentan_box', '(３連単BOX)',　first, second, third);

		reflectFormation(formation_msg);
	});

	//を内容に反映
	$('#wide_wheel_reflect').on('click', function(){
		var formation_msg = getFormationMsg('wheel', 'wide_wheel', '(枠連・馬連・ワイド流し)',　first, second, third);

		reflectFormation(formation_msg);
	});

	//を内容に反映
	$('#umatan_wheel_reflect').on('click', function(){
		var formation_msg = getFormationMsg('wheel', 'umatan_wheel', '(馬単流し)',　first, second, third);

		reflectFormation(formation_msg);
	});

	//を内容に反映
	$('#umatan_multi_wheel_reflect').on('click', function(){
		var formation_msg = getFormationMsg('umatan_multi_wheel', 'umatan_multi_wheel', '(馬単マルチ流し)',　first, second, third);

		reflectFormation(formation_msg);
	});

	//を内容に反映
	$('#renfuku_wheel1_reflect').on('click', function(){
		var formation_msg = getFormationMsg('renfuku_wheel1', 'renfuku_wheel1', '(３連複軸１頭流し)',　first, second, third);

		reflectFormation(formation_msg);
	});

	//を内容に反映
	$('#renfuku_wheel2_reflect').on('click', function(){
		var formation_msg = getFormationMsg('renfuku_wheel2', 'renfuku_wheel2', '(３連複軸２頭流し)',　first, second, third);

		reflectFormation(formation_msg);
	});

	//を内容に反映
	$('#rentan_wheel1_reflect').on('click', function(){
		var formation_msg = getFormationMsg('rentan_wheel1', 'rentan_wheel1', '(３連単軸１頭流し)',　first, second, third);

		reflectFormation(formation_msg);
	});

	//を内容に反映
	$('#rentan_wheel2_reflect').on('click', function(){
		var formation_msg = getFormationMsg('wheel', 'rentan_wheel2', '(３連単軸２頭流し)',　first, second, third);

		reflectFormation(formation_msg);
	});

	//を内容に反映
	$('#rentan_multi_wheel1_reflect').on('click', function(){
		var formation_msg = getFormationMsg('wheel', 'rentan_multi_wheel1', '(３連単マルチ軸１頭流し)',　first, second, third);

		reflectFormation(formation_msg);
	});

	//を内容に反映
	$('#rentan_multi_wheel2_reflect').on('click', function(){
		var formation_msg = getFormationMsg('wheel', 'rentan_multi_wheel2', '(３連単マルチ軸２頭流し)',　first, second, third);

		reflectFormation(formation_msg);
	});

	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
		return false;
	});

});
<!--

function getFormationMsg(type, id_name, formation, first, second, third){
	//当社おススメ
	var uma_num = $('[name="num"]:checked').val();
	if( uma_num == undefined ){
		uma_num = '';
	}

	//1着・1頭目
	var disp_first = Object.values(first).join('・');

	//2着・2頭目
	var disp_second = Object.values(second).join('・');

	//3着・3頭目
	var disp_third = Object.values(third).join('・');

	//合計点数
	var total = document.getElementById(id_name).value;

	var formation_msg = '';

	//画面表示のためフォーマット
	if( type == 'box' ){
		formation_msg = "■当社お薦め馬■\n"
					+"◎("+uma_num+")\n"
					+"-----------------\n"
					+"■買い目■\n"
					+formation
					+"\n"+disp_first+"・"+disp_second+"・"+disp_third
					+"\n"
					+"≪計"+total+"点≫\n"
					+"-----------------\n"
					+"各1点につき100円～500円投資ご推奨!!(無理のないご投資額でお願い致します。)\n"
					+"-----------------\n";	
	
	//馬単マルチ流し
	}else if( type == 'umatan_multi_wheel' ){
		formation_msg = "■当社お薦め馬■\n"
					+"◎("+uma_num+")\n"
					+"-----------------\n"
					+"■買い目■\n"
					+formation+"\n"
					+disp_first+"⇒"+disp_second+"\n"
					+disp_second+"⇒"+disp_first+"\n"
					+"≪計"+total+"点≫\n"
					+"-----------------\n"
					+"各1点につき100円～500円投資ご推奨!!(無理のないご投資額でお願い致します。)\n"
					+"-----------------\n";
	//３連複軸１頭流し
	}else if( type == "renfuku_wheel1" ){
		const disp_comb = getCombMsg(first, second, third, '');
		formation_msg = "■当社お薦め馬■\n"
					+"◎("+uma_num+")\n"
					+"-----------------\n"
					+"■買い目■\n"
					+formation+"\n"
					+disp_comb+"\n"
					+"≪計"+total+"点≫\n"
					+"-----------------\n"
					+"各1点につき100円～500円投資ご推奨!!(無理のないご投資額でお願い致します。)\n"
					+"-----------------\n";

	//３連複軸２頭流し
	}else if( type == "renfuku_wheel2" ){
		const disp_comb = getCombMsg(first, second, third, '');
		formation_msg = "■当社お薦め馬■\n"
					+"◎("+uma_num+")\n"
					+"-----------------\n"
					+"■買い目■\n"
					+formation+"\n"
					+disp_comb+"\n"
					+"≪計"+total+"点≫\n"
					+"-----------------\n"
					+"各1点につき100円～500円投資ご推奨!!(無理のないご投資額でお願い致します。)\n"
					+"-----------------\n";

	//３連単軸１頭流し
	}else if( type == "rentan_wheel1" ){
		const disp_comb = getCombMsg(first, second, third, 'rentan');
		formation_msg = "■当社お薦め馬■\n"
					+"◎("+uma_num+")\n"
					+"-----------------\n"
					+"■買い目■\n"
					+formation+"\n"
					+disp_comb+"\n"
					+"≪計"+total+"点≫\n"
					+"-----------------\n"
					+"各1点につき100円～500円投資ご推奨!!(無理のないご投資額でお願い致します。)\n"
					+"-----------------\n";

	}else if( type == 'wheel' ){
		formation_msg = "■当社お薦め馬■\n"
					+"◎("+uma_num+")\n"
					+"-----------------\n"
					+"■買い目■\n"
					+formation
					+"\n"+disp_first
					+"⇒"+disp_second+"・"+disp_third
					+"\n"
					+"≪計"+total+"点≫\n"
					+"-----------------\n"
					+"各1点につき100円～500円投資ご推奨!!(無理のないご投資額でお願い致します。)\n"
					+"-----------------\n";
	}else{
	
		formation_msg = "■当社お薦め馬■\n"
					+"◎("+uma_num+")\n"
					+"-----------------\n"
					+"■買い目■\n"
					+formation
					+"\n１着"+disp_first
					+"\n２着"+disp_second
					+"\n３着"+disp_third
					+"\n"
					+"≪計"+total+"点≫\n"
					+"-----------------\n"
					+"各1点につき100円～500円投資ご推奨!!(無理のないご投資額でお願い致します。)\n"
					+"-----------------\n";
	}

	return formation_msg;
}

function getCombMsg(first, second, third, mode){
	let disp_comb = '';
	Object.values(first).forEach(function(fvalue){
		Object.values(second).forEach(function(svalue){
			if( Object.values(third) != '' ){
				Object.values(third).forEach(function(tvalue){
					if( mode == 'rentan' ){
						if( svalue == tvalue ){
							return;
						}else{
							disp_comb += fvalue+"・"+svalue+"・"+tvalue+"\n";						
						}
					}else{
						disp_comb += fvalue+"・"+svalue+"・"+tvalue+"\n";
					}
				});
			}else{
				disp_comb += fvalue+"・"+svalue+"\n";
			}
		});
	});
	return disp_comb;
}

function reflectFormation(formation_msg){
	//出力文言設定画面
	//テキストエリアのオブジェクト取得
	var dom = window.opener.document.getElementById('detail');

	//テキストエリアのフォーカスの位置を取得
	var focus_pos = dom.selectionStart;
	
	//テキストエリア内の文字列全体の長さを取得
	var sentence_length = dom.value.length;
	
	//テキストエリア内の文字列先頭からフォーカス位置までの文字列を取得
	var fowward = dom.value.substr(0, focus_pos);

	//テキストエリア内のフォーカス位置から最後までの文字列を取得
	var backward = dom.value.substr(focus_pos, sentence_length);

	//テキストエリア内のフォーカス位置に変換表のキーを追加
	dom.value = fowward + formation_msg + backward;
	
	//テキストエリア内のフォーカス位置をキー追加後に設定
	dom.selectionStart = focus_pos + formation_msg.length;
	dom.selectionEnd = focus_pos + formation_msg.length;
	dom.focus();
}

function PAIRCHECK(No){
	err = SELNO = 0 ;
	ZZ = Z1 = Z2 = "";
	NoNAME = (No.name).toString(10);
	NoVALUE = (No.value).toString(16) -1;

	if(NoNAME == "umaclear"){
		No = "";
		aa = bb = cc = 0;
		ab = bc = ac = abc = oa = ob = oc = oab = obc = oac = oabc = wa = wb = wk = wab = total = 0;
		first = {};
		second = {};
		third = {};
		waku = {};
	}else{
		if(NoVALUE != 98){
			if(NoNAME == 'uma'){
				if(document.form1.uma[NoVALUE].checked == true){aa++;}
				else{aa--;if(document.form1.uma[18].checked){document.form1.uma[18].checked = false;}
			}}
			if(NoNAME == 'umb'){
				if(document.form1.umb[NoVALUE].checked == true){bb++;}
				else{bb--;if(document.form1.umb[18].checked){document.form1.umb[18].checked = false;}
			}}
			if(NoNAME == 'umc'){
				if(document.form1.umc[NoVALUE].checked == true){cc++;}
				else{cc--;if(document.form1.umc[18].checked){document.form1.umc[18].checked = false;}
			}}
		}else{
			if(NoNAME == 'uma'){
				if(document.form1.uma[18].checked == true){
					for(i=0;i<18;i++){
						if(!document.form1.uma[i].checked){
							document.form1.uma[i].checked = true;aa++;
						}
					}
				}else{
					for(i=0;i<18;i++){
						document.form1.uma[i].checked = false;aa--;
					}
				}
			}
			if(NoNAME == 'umb'){
				if(document.form1.umb[18].checked == true){
					for(i=0;i<18;i++){
						if(!document.form1.umb[i].checked){
							document.form1.umb[i].checked = true;bb++;
						}
					}
				}else{
					for(i=0;i<18;i++){
						document.form1.umb[i].checked = false;bb--;
					}
				}
			}
			if(NoNAME == 'umc'){
				if(document.form1.umc[18].checked == true){
					for(i=0;i<18;i++){
						if(!document.form1.umc[i].checked){
							document.form1.umc[i].checked = true;cc++;
						}
					}
				}else{
					for(i=0;i<18;i++){
						document.form1.umc[i].checked = false;cc--;
					}
				}
			}
		}

		ab = bc = ac = abc = oa = ob = oc = oab = obc = oac = oabc = wa = wb = wk = wab = 0;
		for(i=0;i<18;i++){
			ab += document.form1.uma[i].checked * document.form1.umb[i].checked
			bc += document.form1.umb[i].checked * document.form1.umc[i].checked
			ac += document.form1.uma[i].checked * document.form1.umc[i].checked
			abc += document.form1.uma[i].checked * document.form1.umb[i].checked * document.form1.umc[i].checked
		}
		for(i=0;i<8;i++){
			if(document.form1.uma[i].checked){wa++}
			if(document.form1.umb[i].checked){wb++}
			wk += document.form1.uma[i].checked * document.form1.umb[i].checked * document.form1.dwk[i].checked
			wab += document.form1.uma[i].checked * document.form1.umb[i].checked
		}
	}
	oa = aa - ab - ac + abc
	ob = bb - ab - bc + abc
	oc = cc - bc - ac + abc
	oab = ab - abc
	obc = bc - abc
	oac = ac - abc
	oabc = abc
	total = aa + bb + cc

	/*	フォーメーション組数計算	*/
	document.form2.wide_wheel.value = total									//枠連・馬連・ワイド流し
	document.form2.umatan_wheel.value = total								//馬単流し
	document.form2.umatan_multi_wheel.value = total * 2						//馬単マルチ流し
	document.form2.renfuku_wheel1.value = (total * (total - 1)) / 2			//３連複軸１頭流し
	document.form2.renfuku_wheel2.value = total								//３連複軸２頭流し
	document.form2.rentan_wheel1.value = (bb*cc)							//３連単軸１頭流し
	document.form2.rentan_wheel2.value = cc									//３連単軸２頭流し
	document.form2.rentan_multi_wheel1.value = (total * (total - 1)) * 3	//３連単マルチ軸１頭流し
	document.form2.rentan_multi_wheel2.value = total	* 6					//３連単マルチ軸２頭流し

	document.form2.renbox.value = (total*(total - 1))/2						//枠連・馬連・ワイドBOX
	document.form2.umatanbox.value = total*(total - 1)						//馬単BOX
	document.form2.renfukubox.value = (total*(total - 1)*(total - 2)) / 6	//３連複BOX
	document.form2.rentanbox.value = (total*(total - 1)*(total - 2))		//３連単BOX
	document.form2.wakuren.value = wa * wb - wab - wab * (wab-1) / 2 + wk	//枠連
	document.form2.umaren.value = aa * bb - ab - ab * (ab-1) / 2			//馬連ワイド
	document.form2.umatan.value = aa * bb - ab								//馬単
	document.form2.sanfuku.value = oa*
		((oab+ob)*(obc+oc+oac+oabc)+obc*(oc+oac)+oabc*(obc+oc+oac)+obc*(obc-1)/2+oabc*(oabc-1)/2)
		+oab*(ob*(obc+oc+oac+oabc)+obc*(oc+oac+oabc)+oabc*(oc+oac)+obc*(obc-1)/2+oabc*(oabc-1)/2)
		+oab*(oab-1)/2*(obc+oc+oac+oabc)+oac*(ob*(obc+oc+oabc)+obc*(oc+oabc)+oc*(oab+oabc)+obc
		*(obc-1)/2+oabc*(oabc-1)/2)+oac*(oac-1)/2*(oab+ob+obc+oabc)+oabc*(ob*(obc+oc)+obc*oc+obc*(obc-1)/2)
		+oabc*(oabc-1)/2*(ob+obc+oc)+oabc*(oabc-1)*(oabc-2)/6;				//３連複
	document.form2.santan.value = aa * bb * cc - (cc * ab + bb * ac + aa *bc) + abc *2;//３連単

}
// -->
</script>
</html>