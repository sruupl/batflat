function insertEditor(type) {
    var editor = $('.editor');

    if (type == 'wysiwyg') {
        if ($('.markItUp').length) {
            editor.markItUpRemove();
        }

        editor.summernote({
            lang: '{$lang.name}',
            height: 270,
            callbacks: {
                onInit: function() {
                    $('.note-codable').keyup(function() {
                        editor.val($(this).val());
                    });
                },
                onImageUpload: function(files) {
                    sendFile(files[0], this);
                },
                onChange: function() {
                    editor.parents('form').trigger('checkform.areYouSure');
                }
            }
        });

        $('.note-group-select-from-files').remove();
    } else {
        if ($('.note-editor').length) {
            editor.each(function() {
                var isEmpty = $(this).summernote('isEmpty');
                $(this).summernote('destroy');
                if (isEmpty)
                    $(this).html('');
            });
        }

        editor.each(function() {
            $(this).markItUp(markItUp_html).highlight();
        });
    }
}

function sendFile(file, editor) {
    var formData = new FormData();
    formData.append('file', file);

    var fileData = URL.createObjectURL(file);
    $(editor).summernote('insertImage', fileData, function($image) {
        $.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();

                $('input[type="submit"]').prop('disabled', true);
                var progress = $('.progress:first').clone();
                progress = (progress.fadeIn()).appendTo($('.progress-wrapper'));

                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        percentComplete = parseInt(percentComplete * 100);
                        progress.children().css('width', percentComplete + '%');

                        if (percentComplete === 100) {
                            progress.fadeOut();
                            progress.remove();
                            $('input[type="submit"]').prop('disabled', false);
                        }
                    }
                }, false);

                return xhr;
            },
            url: '{?=url([ADMIN, "blog", "editorUpload"])?}',
            data: formData,
            type: 'POST',
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    $image.remove();
                    $(editor).summernote('insertImage', data.result);
                } else if (data.status == 'failure') {
                    $image.remove();
                    bootbox.alert(data.result);
                }
            }
        });
    });
}

function select_editor() {
    if ($('.editor').data('editor') == 'wysiwyg') {
        insertEditor('wysiwyg');
    } else
        insertEditor('html');
}

$(document).ready(function() {
    select_editor();

    $("#toggle-form label").click(function() {
        $("#toggle-form .textarea").slideToggle("slow");
    });

    $('form').areYouSure({ 'message': '{$lang.general.unsaved_warning}' });
});