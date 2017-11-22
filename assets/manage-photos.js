var ddManagePhotos = function() {
    //var siteUrl = $oCore.params('core.home');
    var numberOfPhotos = window.cm_dd_photo_data.count;
    var numberOfPhotosAllowed = window.cm_dd_photo_data.max;
    console.log(numberOfPhotos, numberOfPhotosAllowed);
    refreshUploadPhotoControlState();
    ajaxifyDeleteControls();


    var uploadPhotoForm = $('.dd-manage-photos input:file');
    var uploadPhotoFormContainer = uploadPhotoForm.closest('div.file-input-button');
    var uploadPhotoFormContainerBgImage = uploadPhotoFormContainer.css('background-image');

    uploadPhotoForm.fileupload({
        formData: {
            dd_id:  window.cm_dd_photo_data.ddId,
            action: 'upload'
        },
        forceIframeTransport: true,
        initialIframeSrc: 'upload-dd-Photo' //for fix on Firefox v49
    }).bind('fileuploadstart', function (e, data) {
        $Core.processing();
        $(".add-photo-form .error").html("<i class=\"fa fa-spinner fa-spin fa-1x fa-fw\"></i>");
        uploadPhotoForm.addClass("uploading");
        uploadPhotoFormContainer.css('background-image', 'none');
    }).bind('fileuploaddone', function (e, data) {
        //console.log(data.result.error); return;
        var response = JSON.parse($('pre', data.result).text())
        if (response.error) {
            var messages = '';
            for(var i in response.messages) {
                if (typeof response.messages[i] != 'string') {
                    continue;
                }
                messages += response.messages[i] + '</br>';
            }
            $(".add-photo-form .error").html(messages);
        }

        var $imageLi = getDisplayImageElement(response.image_url, response.id);
        $imageLi.hide();
        $('.add-photo-form').before($imageLi);
        $imageLi.slideDown(300);

        //makeCaptionsEditable($imageLi);
        ajaxifyDeleteControls($imageLi);
        increaseNumberOfPhotos();

        uploadPhotoForm.removeClass("uploading");
        $(".add-photo-form .error").html("");
        uploadPhotoFormContainer.css('background-image', uploadPhotoFormContainerBgImage);
    });

    function getDisplayImageElement(image_url, id) {

        var liElementInnerHtml =
            '<div class="picture">' +
            '<img style="max-height: 100%" src="' + image_url + '" />' +
            '</div>' +
            '<a title="Delete" class="item-controls delete" data-id="'+ id + '" data-dd-id="' + window.cm_dd_photo_data.ddId + '">' +
            '<i class="fa fa-remove"></i>' +
            '</a>';

        var $imageLi = $("<li>").html(liElementInnerHtml).prop('id', 'photo_' + id);
        return $imageLi;
    }

    function refreshUploadPhotoControlState() {
        $("li.add-photo-form").toggle(numberOfPhotosAllowed > numberOfPhotos);
    }

    function increaseNumberOfPhotos() {
        numberOfPhotos++;
        refreshUploadPhotoControlState();
    }

    function decreamentNumberOfPhotos() {
        numberOfPhotos--;
        refreshUploadPhotoControlState();
    }

    function ajaxifyDeleteControls(context) {
        context = context || "ul.pictures";
        $(".item-controls.delete", context).click(function (e) {
            e.preventDefault();
            var $parentLi = $(this).parents('li');

            var ddId = $(this).data('ddId');
            var id = $(this).data('id');

            $parentLi.animate({
                backgroundColor: '#dddddd'
            }, 300);

            $.fn.ajaxCall('digitaldownload.deleteImage', 'dd_id=' + ddId + '&id=' + id, false, 'POST', function(){
                $parentLi.slideUp(300, function () {
                    $parentLi.remove();
                    decreamentNumberOfPhotos();
                });
            });
            return false;
        });
    }
}
$Ready(ddManagePhotos);
ddManagePhotos();
