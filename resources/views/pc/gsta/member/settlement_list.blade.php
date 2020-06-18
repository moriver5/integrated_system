@extends('layouts.member_base')
@section('member_content')
	<div class="main_column_inner17 float_l pad_t25">
		<img src="/images/ti026.gif" alt="商品購入" width="610" height="25" /><br />
        <div id="main_column_inner17_box" class="align_c clearfix" align="center">
			<div class="info_middle bg013 clearfix" align="center"><br />
			<div class="info_middle_inner align_l">
			<div class="info_box_p"><p class="text_w pad_t5" align="left">
		</div>
		</div>
        <div id="pay_outline" align="center">
			<div class="point_box" align="center">
				<div class="">
					<img src="/images/pay_img_howto_pay.gif" alt="お支払い方法のご案内" width="577" /><br />
				</div>
				<!--middle　開始 -->
                <div class="pay_img_howtopay_middle_bg align_c text_w" align="center">
                    <div class="pay_howtopay_step">
						<img src="/images/pay_img_howto_pay_step.jpg" alt="商品予約/購入の簡単3STEP" width="200" height="681" />
						<div class="text_red pad_t5 pad_b10 lh15" align="left">
							※　お客様の個人情報を守るため<br />
							SSL(暗号化)通信を導入しております。</div>
						</div>
						<!--お支払方法　開始 -->
						<div class="pay_howtopay mar_b10 pad_b20 text_w">
							<img src="/images/pay_img_howto_pay_title.jpg" alt="" />
							<div class="bank">
								<img src="/images/hrkin305.gif" alt="" width="305" /><br />
								<a href="settlement.php?m=b" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image29','','/images/bt_point_bank_on.jpg',1)">
									<img src="/images/bt_point_bank_off.jpg" width="305" height="77" border="0" id="Image29" />
								</a>
							</div>
							<p class="pad_t5 pad_l20 pad_r20" align="left">お振込み手数料は、お客様のご負担となります。ご了承下さい。午後3時以降にお手続き頂いた場合のご入金確認は翌銀行営業日となります事をご了承下さい。</p>
							<div class="pad_t20"><img src="/images/hrkin305.gif" alt="" width="305" /><br />
								<a href="settlement.php?m=c" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image30','','/images/bt_point_credit_on.jpg',1)">
									<img src="/images/bt_point_credit_off.jpg" width="305" height="77" border="0" id="Image30" />
								</a>
							</div>
							<p class="pad_t5 pad_l20 pad_r20" align="left">各社クレジットカードにて商品の購入が24時間可能です。<br />
								VISA,JCB,MasterCardのマークが付いているカードがご利用になれます。
							</p>
							<div class="pad_t3">
								<img src="/images/icon_point_visa.gif" alt="VISA" width="100" height="28" />&nbsp;
								<img src="/images/icon_point_jcb.gif" alt="JCB" width="101" height="28" />&nbsp;
								<img src="/images/icon_point_mastercard.gif" alt="MasterCard" width="101" height="28" />
							</div>
							<div class="pad_t20">
								<img src="/images/hrkin305.gif" alt="" width="305" /><br />
								<a href="settlement.php?m=n" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image31','','/images/bt_point_netbank_on.jpg',1)">
									<img src="/images/bt_point_netbank_off.jpg" width="305" height="77" border="0" id="Image31" />
								</a>
							</div>
							<p class="pad_t5 pad_l20 pad_r20" align="left">ジャパンネット銀行口座、住信SBIネット銀行口座からお振り込みのお客様は、
								<span style="color:#FF0000;">休日でも24時間</span>
								お申し込み手続きと同時にお振り込みが完了します。<br />
								その他の銀行の場合、午後3時以降にお手続き頂いた場合のご入金確認は翌銀行営業日となります事をご了承下さい。
							</p>
							<div class="pad_t3 pad_b15">
								<img src="/images/icon_point_japannet.gif" alt="ジャパンネット銀行" width="99" height="28" />&nbsp;
								<img src="/images/icon_point_sbi.gif" alt="SBI" width="100" height="28" />
							</div>
						</div>
						<!--お支払方法　終了 -->
					</div>
					<!--middle　終了 -->
                </div>
            </div>
        <br />
        <br />
	</div>
@endsection