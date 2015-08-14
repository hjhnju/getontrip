/*
  图片裁剪  
  fyy 2015.7.21
 */
$(document).ready(function() {
    // Create variables (in this scope) to hold the API and image size
    var jcrop_api,
        boundx,
        boundy,

        // Grab some information about the preview pane
        $preview = $('#preview-pane'),
        $pcnt = $('#preview-pane .preview-container'),
        $pimg = $('#preview-pane .preview-container img'),

        xsize = $pcnt.width(),
        ysize = $pcnt.height(),
        ratio = 133 / 93;

  

    $('#crop-img').click(function(event) { 
        var image = $('#image').val(),
        //hash =image.split('.')[0],
        url = '/pic/' + image;

        $('#target').attr('src', url);
        $('#target').attr('data-image', image);
        $('#jcrop-preview').attr('src', url).css('width', '250px');
  
    
        $('#target').Jcrop({ 
        	imgUrl:url,
            aspectRatio: ratio,
            onChange: updatePreview,
            onSelect: updatePreview
        }, function() {
            // Use the API to get the real image size
            var bounds = this.getBounds();
            boundx = bounds[0];
            boundy = bounds[1];
            // Store the API in the jcrop_api variable
            jcrop_api = this;
            jcrop_api.setSelect([0, 0, boundx, boundx * ratio]);
            // Move the preview into the jcrop container for css positioning
            $preview.appendTo(jcrop_api.ui.holder);
         
        });
        setTimeout(function() {
            //打开模态框
            $('#corpModal').modal(); 
        }, 0);	

    });

    //点击确定
    $('#corp-btn').click(function(event) {
        $.ajax({
            "url": "/admin/topicapi/cropPic",
            "data": {
                id: $('#id').val(),
                image: $('#target').attr('data-image'),
                x: $('#x1').val(),
                y: $('#y1').val(),
                width: $('#w').val(),
                height: $('#h').val()
            },
            "async": false,
            "error": function(e) {
                alert("服务器未正常响应，请重试");
            },
            "success": function(response) {
                if (response.status != 0) {
                    alert(response.statusInfo);
                } else {
                    var data = response.data;
                    $('#image').val(data.image);
                    $('#imageView').html('<img src="/pic/' + data.hash + '_190_140.jpg" alt=""/>');
                    $('#imageView').removeClass('imageView');
                    //手工关闭模态框
                    $('#corpModal').modal('hide');
                }
            }
        });
    });

    /*    $('.corp-labels input').blur(function(event) {
        	jcrop_api.setSelect([$('#x1').val(), $('#y1').val(), $('#w').val(), $('#w').val() * ratio]);
        });*/

    function updatePreview(c) {
        if (parseInt(c.w) > 0) {
            var rx = xsize / c.w;
            var ry = ysize / c.h;

            $pimg.css({
                width: Math.round(rx * boundx) + 'px',
                height: Math.round(ry * boundy) + 'px',
                marginLeft: '-' + Math.round(rx * c.x) + 'px',
                marginTop: '-' + Math.round(ry * c.y) + 'px'
            });
            $('#x1').val(c.x);
            $('#y1').val(c.y);
            //$('#x2').val(c.x2);
            //$('#y2').val(c.y2);
            $('#w').val(c.w);
            $('#h').val(c.h);
        }
    };
});
