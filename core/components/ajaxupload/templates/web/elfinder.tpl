<script type="text/javascript">
    /* <![CDATA[ */
    function fileCopier(files, options) {
        var settings = $.extend({
            debug: false,
            element: 'file-uploader{$params.uid}',
            uploadAction: '{$params.uploadAction}',
            uid: '{$params.uid}',
            dragText: '{$_lang.dropArea}',
            uploadButtonText: '{$_lang.uploadButton}',
            cancelButtonText: '{$_lang.cancel}',
            failUploadText: '{$_lang.failed}',
            deleteText: '{$_lang.deleteButton}',
            clearText: '{$_lang.clearButton}',
            hideShowDropArea: true,
            thumbX: '{$params.thumbX}px',
            thumbY: '{$params.thumbY}px',
            allowedExtensions: [{$params.allowedExtensionsString}],
            sizeLimit: {$params.sizeLimit},
            maxFiles: {$params.maxFiles},
            messages: {
                typeError: "{$_lang.typeError}",
                sizeError: "{$_lang.sizeError}",
                minSizeError: "{$_lang.minSizeError}",
                emptyError: "{$_lang.emptyError}",
                onLeave: "{$_lang.onLeave}"
            }
        }, options);

        var element = $(settings.element);
        var imageList = element.find('.file-uploader-images');

        $.each(files, function (index, file) {
            $.ajax({
                type: 'POST',
                url: uploadAction,
                uid: uid,
                data: {
                    elfinder: 1,
                    elfinderfile: file
                },
                success: function () {
                    var fileid = uploadAnswer.fileid;
                    if (uploadAnswer.success) {
                        var imageWrap = $('<div>').addClass('image-wrap').addClass('image' + fileid);
                        var deleteButton = $(settings.deleteTemplate).click(function () {
                            $.get(settings.uploadAction, {
                                'delete': fileid
                            }, function (deleteAnswer) {
                                if (deleteAnswer.success) {
                                    imageWrap.fadeOut(function () {
                                        $(this).remove();
                                    });
                                    if (settings.debug) {
                                        alert(JSON.stringify(deleteAnswer) + '\nImage ' + fileid + ' deleted.');
                                    }
                                }
                            }, 'json');
                        });
                        var image = $('<img>').attr({
                            src: uploadAnswer.filename
                        }).css({
                            'width': settings.thumbX,
                            'height': settings.thumbY,
                            'position': 'relative'
                        }).after(deleteButton);

                        imageList.append(imageWrap.append(image));
                        // element.find('.qq-upload-list li').eq(fileid).hide();
                        if (settings.debug) {
                            alert('ID:' + fileid + '\nResponse:' + JSON.stringify(uploadAnswer));
                        }
                    }
                },
                dataType: 'json'
            });
        });
    }
    /* ]]> */
</script>