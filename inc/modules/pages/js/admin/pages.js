function insertEditor(type)
{
    var editor = $('.editor');

    if(type == 'wysiwyg')
    {
        if($('.markItUp').length)
        {
            editor.markItUpRemove();
        }

        editor.summernote(
        {
            lang: '{$lang.name}',
            height: 335,
            callbacks:
            {
                onInit: function()
                {
                    $('.note-codable').keyup(function()
                    {
                        editor.val($(this).val());
                    });
                },
                onImageUpload: function(files)
                {
                    sendFile(files[0], this);
                },
                onChange: function()
                {
                    editor.parents('form').trigger('checkform.areYouSure');
                }
            }
        });        
    }
    else
    {
        if($('.note-editor').length)
        {
            editor.each(function()
            {
                var isEmpty = $(this).summernote('isEmpty');
                $(this).summernote('destroy');
                if(isEmpty)
                    $(this).html('');
            });
        }

        var checkbox = $('input[name="markdown"]');
        editor.each(function()
        {
            var currentEditor = $(this);
            currentEditor.markItUp(checkbox.is(':checked') ? markItUp_markdown : markItUp_html).highlight();

            if($('.editor').data('editor') == 'html')
            {
                checkbox.change(function()
                {
                    currentEditor.markItUpRemove();
                    if (checkbox.is(':checked'))
                        currentEditor.markItUp(markItUp_markdown).highlight();
                    else
                        currentEditor.markItUp(markItUp_html).highlight();
                });
            }
        });
    }
}

function sendFile(file, editor)
{
    var formData = new FormData();
    formData.append('file', file);

    var fileData = URL.createObjectURL(file);
    $(editor).summernote('insertImage', fileData, function ($image)
    {
        $.ajax({
            xhr: function()
            {
                var xhr = new window.XMLHttpRequest();

                $('input[type="submit"]').prop('disabled', true);
                var progress = $('.progress:first').clone();
                progress = (progress.fadeIn()).appendTo($('.progress-wrapper'));

                xhr.upload.addEventListener("progress", function(evt)
                {
                    if(evt.lengthComputable)
                    {
                        var percentComplete = evt.loaded / evt.total;
                        percentComplete = parseInt(percentComplete * 100);
                        progress.children().css('width', percentComplete + '%');

                        if(percentComplete === 100)
                        {
                            progress.fadeOut();
                            progress.remove();
                            $('input[type="submit"]').prop('disabled', false);
                        }
                    }
                }, false);

                return xhr;
            },
            url: '{?=url([ADMIN, "pages", "editorUpload"])?}',
            data: formData,
            type: 'POST',
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data)
            {
                if(data.status == 'success')
                {
                    $image.remove();
                    $(editor).summernote('insertImage', data.result);
                }
                else if(data.status == 'failure')
                {
                    $image.remove();
                    bootbox.alert(data.result);
                }
            }
        });
    });
}

function markdown()
{
    var checkbox = $('input[name="markdown"]');
    if($('.editor').data('editor') == 'wysiwyg')
    {
        checkbox.change(function()
        {
            if($(this).is(':checked'))
                insertEditor('html');
            else
                insertEditor('wysiwyg');
        });

        if(checkbox.is(':checked'))
            insertEditor('html');
        else
            insertEditor('wysiwyg');
    }
    else
        insertEditor('html');    
}

$(document).ready(function()
{
    markdown();
    $('form').areYouSure( {'message':'{$lang.general.unsaved_warning}'} );
});