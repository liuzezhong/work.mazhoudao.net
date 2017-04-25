/**
 * Created by liuzezhong on 2016/11/8.
 */

/**
 * 图片上传功能
 */
$(function() {
    $('#file_upload').uploadify({
        'swf'      : '/Public/plugins/uploadify/uploadify.swf',
        'uploader' : '/index.php?m=wmbackstage&c=index&a=ajaxUploadImage',
        'buttonText': '上传文件',
        'fileTypeDesc': 'Upload Files',
        'fileObjName' : 'file',

        //允许上传的文件后缀
        'fileTypeExts': '*.doc;*.docx;*.gif; *.jpg; *.png',
        'onUploadSuccess' : function(file,data,response) {
            // response true ,false
            if(response) {
                var obj = JSON.parse(data); //由JSON字符串转换为JSON对象

                $('#' + file.id).find('.data').html('上传完毕');
                var info = file.name + '上传成功！';

                $("#upload_org_code_img").html(info);
                $("#file_upload_image").attr('value',obj.data);
                $("#upload_org_code_img").show();
            }else{
                alert('上传失败!');
            }
        },
    });

    $('#file_upload4').uploadify({
        'swf'      : '/Public/plugins/uploadify/uploadify.swf',
        'uploader' : '/index.php?m=wmbackstage&c=index&a=ajaxUploadImage',
        'buttonText': '上传汇款回单',
        'fileTypeDesc': 'Image Files',
        'fileObjName' : 'file',

        //允许上传的文件后缀
        'fileTypeExts': '*.gif; *.jpg; *.png',
        'onUploadSuccess' : function(file,data,response) {
            // response true ,false
            if(response) {
                var obj = JSON.parse(data); //由JSON字符串转换为JSON对象

                $('#' + file.id).find('.data').html(' 上传完毕');

                $("#upload_org_code_img4").attr("src",obj.data);
                $("#file_upload_image4").attr('value',obj.data);
                $("#upload_org_code_img4").show();
            }else{
                alert('上传失败');
            }
        },
    });

    $('#file_upload3').uploadify({
        'swf'      : '/Public/plugins/uploadify/uploadify.swf',
        'uploader' : '/index.php?m=wmbackstage&c=index&a=ajaxUploadImage',
        'buttonText': '重新上传营业执照',
        'fileTypeDesc': 'Image Files',
        'fileObjName' : 'file',

        //允许上传的文件后缀
        'fileTypeExts': '*.gif; *.jpg; *.png',
        'onUploadSuccess' : function(file,data,response) {
            // response true ,false
            if(response) {
                var obj = JSON.parse(data); //由JSON字符串转换为JSON对象

                $('#' + file.id).find('.data').html(' 上传完毕');

                $("#upload_org_code_img3").attr("src",obj.data);
                $("#file_upload_image3").attr('value',obj.data);

                $("#upload_org_code_img3").show();
                $("#upload_org_code_img_hide3").hide();
            }else{
                alert('上传失败');
            }
        },
    });

    $('#file_upload2').uploadify({
        'swf'      : '/Public/plugins/uploadify/uploadify.swf',
        'uploader' : '/index.php?m=wmbackstage&c=index&a=ajaxUploadImage',
        'buttonText': '重新上传开户许可证',
        'fileTypeDesc': 'Image Files',
        'fileObjName' : 'file',

        //允许上传的文件后缀
        'fileTypeExts': '*.gif; *.jpg; *.png',
        'onUploadSuccess' : function(file,data,response) {
            // response true ,false
            if(response) {
                var obj = JSON.parse(data); //由JSON字符串转换为JSON对象

                $('#' + file.id).find('.data').html(' 上传完毕');

                $("#upload_org_code_img2").attr("src",obj.data);
                $("#file_upload_image2").attr('value',obj.data);

                $("#upload_org_code_img2").show();
                $("#upload_org_code_img_hide2").hide();
            }else{
                alert('上传失败');
            }
        },
    });
});