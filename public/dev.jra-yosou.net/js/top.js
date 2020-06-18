$(function () {
// slide (slick.min.js)
	$('.slick_slider').slick({
		autoplay: true,
		arrows  : false,
		dots    : true,
		pauseOnHover:false,
		slidesToShow: 5,
		slidesToScroll: 1,
		centerMode  :true,
		centerPadding:'10%',
		responsive: [
        {
			breakpoint: 1399,
			settings: {
				slidesToShow: 3
			}
		 },
         {
			breakpoint: 999,
			settings: {
				slidesToShow: 1
			}
         }
        ]
	});
// 詳細アコーディオン
	$('.list_qa > dt').click(function(){
		$(this).next('dd').slideToggle();
        $(this).toggleClass('open');
	});
// 満足度調査 日付
    var now = new Date();
    now.setDate(now.getDate() - 1); //前日を設定
    
    var dateY = now.getFullYear();
    var dateM = now.getMonth() + 1;
    var dateD = now.getDate();
    $('.js_date').html(dateY + '年' + dateM + '月' + dateD + '日');
});