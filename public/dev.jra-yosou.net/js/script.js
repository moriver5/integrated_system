$(function(){ 
// pagetop
    var $pagetop = $('.btn_pagetop a');
    $pagetop.hide();
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $pagetop.fadeIn();
        }else {
            $pagetop.fadeOut();
        }
    });
    $pagetop.click(function () {
        $('body, html').animate({ scrollTop: 0 }, 500);
        return false;
    });

// inview(jquery.inview.min.js)
	$('.js_inview').on('inview', function(event, isInView) {
		if (isInView) {
            if($(this).attr('data-delay') != undefined){
                var delayVal = $(this).attr('data-delay');
                    $(this).delay(delayVal).queue(function(){
                        $(this).addClass('active');
                        $(this).dequeue();
                });
            }else{
                $(this).delay(300).queue(function(){
                    $(this).addClass('active');
                    $(this).dequeue();
                });
            }
		}
	});
});