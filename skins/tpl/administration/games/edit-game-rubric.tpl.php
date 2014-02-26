<?php
use classes\render;
use classes\url;
?>
<script type="text/javascript" src="/skins/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="/skins/js/tinymce/jquery.tinymce.min.js"></script>
<style>
    #header-games input{width: 774px;}
    #announce-games textarea{width: 774px;}
    .search-index input {width: 948px;}
</style>

<h1><a href="<?= Url::Action("index", "administration.news") ?>">Новости</a>
    -> <?=$data["id"] > 0 ? "Редактирование" : "Создание" ?></h1>
<form action="<?= Url::Action("edit", "administration.news") ?>" method="POST">
    <?=Render::Hidden($data["id"], "id")?>
    <table>
        <tr>
            <td>
                <div class="field">
                    <?=Render::LabelDatePicker(date("d.m.Y", strtotime($data["date"])), "date", "Дата", true)?>
                </div>
            </td>
            <td>
                <div class="field fill" id="header-games">
                    <?=Render::LabelEdit($data['header'], "header", "Заголовок", true)?>
                </div>
            </td>
        </tr>
    </table>
    <br>
    <div class="field">
        <?=Render::LabelTextArea($data['text'], "text", "")?>
    </div>

    <div class="field fill search-index" style="width: 100%; margin-right: 2%;">
        <?=Render::LabelEdit($data['title'], "title", "Заголовок страницы")?>
    </div>
    <div class="field fill search-index" style="width: 100%; margin-right: 2%;">
        <?=Render::LabelEdit($data['description'], "description", "Описание страницы")?>
    </div>
    <div class="field fill search-index" style="width: 32%">
        <?=Render::LabelEdit($data['keywords'], "keywords", "Ключевые слова")?>
    </div>
    <br>

    <div style="height: 50px; width: 100%">
        <input type="submit" value="Сохранить" class="right">
    </div>
</form>

<script type="text/javascript">
    $(function () {

        tinymce.init({
            selector: "#text",
            language : "ru",
            plugins: [
                "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "table contextmenu directionality emoticons template textcolor paste textcolor"
            ],
            toolbar1: "newdocument | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
            toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | inserttime preview | forecolor backcolor",
            toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | visualchars visualblocks nonbreaking template pagebreak restoredraft",
            menubar: false,

            toolbar_items_size: 'small',
            relative_urls: false,
            style_formats: [
                {title: 'Bold text', inline: 'b'},
                {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                {title: 'Example 1', inline: 'span', classes: 'example1'},
                {title: 'Example 2', inline: 'span', classes: 'example2'},
                {title: 'Table styles'},
                {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
            ],

            templates: [
                {title: 'Test template 1', content: 'Test 1'},
                {title: 'Test template 2', content: 'Test 2'}
            ]
        });
    });
    $(document).ready(function () {
        $('#file').bind('change', function () {
            execUpload(false, 'file', 'info');
        })
        $('#file-others').bind('change', function () {
            execUpload(true, 'file-others', 'info-others');
        })
        function progressHandlingFunction(e) {
            if (e.lengthComputable) {
                var percentComplete = parseInt((e.loaded / e.total) * 100);
                $('.progress_bar').animate({width: percentComplete + "%"}, 10);
            }
        }
        function execUpload(param, id, result){
            var data = new FormData();
            var error = '';
            jQuery.each($('#'+id)[0].files, function (i, file) {
                data.append('file-' + i, file);
            });

            if (error != '') {
                $('#'+result).html(error);
            } else {
                if(param == true)
                    $.ajax({url: "/administration/news/upload", type: 'POST', data: {"multi-load":true}})
                $.ajax({
                    url: "/administration/news/upload",
                    type: 'POST',
                    xhr: function () {
                        var myXhr = $.ajaxSettings.xhr();
                        $("#"+result).before('<div class="progress_container"><div class="progress_bar tip"></div></div>');
                        $(".progress_container").css("margin","10px 0");
                        if (myXhr.upload) {
                            myXhr.upload.addEventListener('progress', progressHandlingFunction, false);
                        }
                        return myXhr;

                    },
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {},
                    success: function (data) {
                        $(".progress_container").remove();
                        var resJson = $.parseJSON(data);
                        if(typeof resJson.error == 'undefined')
                        {
                            if(param != true){
                                add_image_to_editor(resJson.filename);
                                $("form").append("<input type='hidden' id='img-main' name='img-main' value='"+data+"'>");
                                $("#main-photo-upload-btn").hide();
                                $("#main-photo-delete-btn").show();
                            } else {
                                $("form").append("<input type='hidden' id='add-images' name='add-images' value='"+data+"'>");
                                var resHtml = '';
                                for(var i = 0; i < resJson.big.length; i++){
                                    resHtml += "<div class='left'><img style='width:75px; padding-right: 10px;' src='/"+resJson.small[i]+"'></div>";
                                }
                                $("#"+result).append(resHtml);

                            }
                        }
                        else
                            $("#"+result).html("<b style='color: red'>"+resJson.error+"</b>");
                    },
                    error: errorHandler = function () {
                        $(".progress_container").remove();
                        $('#'+result).html('Ошибка загрузки файлов');
                    }
                });

            }

        }
        function add_image_to_editor(image){
            var content = "<img class=\"left\" id=\"main_image\" src=\"" + image + "\">";
            $(tinyMCE.activeEditor.getBody()).prepend(content);

        }
    });
    function deleteMainPhoto(){
        $("#main-photo-upload-btn").show();
        $("#main-photo-delete-btn").hide();
        $("#img-main").remove();
        $(tinyMCE.activeEditor.getBody()).find("#main_image").remove();
    }

</script>