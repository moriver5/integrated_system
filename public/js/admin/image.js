
function resizeImage(id, image_url, scale = 0.5){
    //バナー画像の縮小
    var scale = scale;
    var canvas = document.getElementById(id);
    if( canvas != null ){
        var ctx = canvas.getContext('2d');
        var image = new Image();
        var reader = new FileReader();
        image.crossOrigin = "Anonymous";
        image.onload = function(event){
            if( ( this.width < 1000 ) ){
                var dstWidth = this.width * scale;
                var dstHeight = this.height * scale;

            //横幅1000以上なら強制的に90%縮小
            }else{
                var dstWidth = this.width * 0.1;
                var dstHeight = this.height * 0.1;                    
            }
            canvas.width = dstWidth;
            canvas.height = dstHeight;
            ctx.drawImage(this, 0, 0, this.width, this.height, 0, 0, dstWidth, dstHeight);
            $("#" + id).attr('src', canvas.toDataURL());
        }
        image.src = image_url;
    }
}




