/**
 * Created by yuban on 2017/3/6.
 */


$('.useradmin_table #useradmin-status').on('click',function () {      //切换状态
    var user_id = $(this).attr('attr-id');
    var status = $(this).attr('attr-status');

    if(status == 0) {
        user_status = 1;
    }
    if(status == 1) {
        user_status = 0;
    }
    postData = {
        'user_id' : user_id,
        'user_status' : user_status,
    };
    url = "/index.php?m=wmbackstage&c=wmuser&a=changetype";

    layer.open({
        type : 0,
        title : '请再次确定',
        btn : ['是','否'],
        icon : 3,
        closeBtn : 2,
        content : "是否切换用户状态",
        scrollbar : true,
        yes : function () {
            //执行跳转
            tochange(url,postData);   //抛送ajax请求
        }

    });

});

$('.useradmin_table #duigong-status').on('click',function () {      //切换状态
    var user_id = $(this).attr('attr-id');
    var status = $(this).attr('attr-status');

    postData = {
        'user_id' : user_id,
    };
    url = "/index.php?m=wmbackstage&c=wmuser&a=changetype_duigong";


    layer.open({
        type : 0,
        title : '请选择',
        btn : ['启用对公账户','驳回重新上传'],
        icon : 7,
        closeBtn : 2,
        content : "&nbsp;&nbsp;&nbsp;将对公账户的状态设置为：",
        scrollbar : true,
        yes : function () {
            //执行跳转
            postData = {
                'user_id' : user_id,
                'account_status' : 1,
            };
            tochange(url,postData);   //抛送ajax请求
        },
        btn2 : function () {
            postData = {
                'user_id' : user_id,
                'account_status' : -1,
            };
            tochange(url,postData);   //抛送ajax请求
        },

    });
});

$('#add_material').on('click',function () {
    var book_id = $('input[name = "bookid"]').val();
    var book_name = $('input[name = "bookname"]').val();
    var book_type = $('option:selected').val();
    var book_detail =$('textarea[name = "bookdetail"]').val();
    var afterurl = $('input[name = "afterurl"]').val();
    var file_up = $('input[name = "file_up"]').val();


    if(!book_id)
        return dialog.msg('请输入书号！');
    if(!book_name)
        return dialog.msg('请输入书名！');
    if(!book_type)
        return dialog.msg('请选择类型！');
    if(!book_detail)
        return dialog.msg('请输入素材详情！');
    if(!afterurl)
        return dialog.msg('请输入后续阅读地址！');

    var postData = {
        'book_id' : book_id,
        'book_name' : book_name,
        'book_type' : book_type,
        'book_detail' : book_detail,
        'afterurl' : afterurl,
        'file_upload' : file_up,
    };

    //1.5 定义POST链接地址和跳转页面地址
    var postUrl = '/index.php?m=wmbackstage&c=wmmatter&a=upload_check';

    $.post(postUrl,postData,function (result) {
        if(result.status == 0) {
            return dialog.error(result.message);
        }
        if(result.status == 1) {
            return dialog.success(result.message,'');
        }
    },'JSON');

});

$('#upload_bulletin').on('click',function () {
    //1.1 获取html中用户输入内容
    var title = $('input[name = "title"]').val();
    var detail = $('textarea[name = "detail"]').val();

    //1.2 验证获取数据的有效性
    if(!title)
        return dialog.msg('标题不能为空！');
    if(!detail)
        return dialog.msg('公告详情不能为空！');

    //1.3 数据封装
    var postData = {
        'title' : title,
        'detail' : detail,
    };

    //1.4 定义POST链接地址和跳转页面地址
    var postUrl = '/index.php?m=wmbackstage&c=wmnotice&a=upload_bulletin_check';
    var jumpUrl = '';

    //1.5 Ajax异步数据传输
    $.post(postUrl,postData,function (result) {
        if(result.status == 0) {
            return dialog.error(result.message);
        }
        if(result.status == 1) {
            return dialog.success(result.message,'');
        }
    },'JSON');
});

$('#set_pay_success').on('click',function () {
    push = {};
    postData = {};
    $("input[name='status_checkbox']:checked").each(function  (i) {
        push[i] = $(this).val();
    });

    postData['push'] = push;
    postData['type'] = 1;

    url = "/index.php?m=wmbackstage&c=wmwithdrawals&a=set_paymentdata_check";

    layer.open({
        type : 0,
        title : '请再次确定',
        btn : ['是','否'],
        icon : 3,
        closeBtn : 2,
        content : "是否将状态改为【已提现】！",
        scrollbar : true,
        yes : function () {
            //执行跳转
            tochange(url,postData);   //抛送ajax请求
        }

    });

});

$('#set_pay_failed').on('click',function () {
    push = {};
    postData = {};
    $("input[name='status_checkbox']:checked").each(function  (i) {
        push[i] = $(this).val();
    });
    if(push.length === 0)
        return dialog.error('至少勾选一条提现记录！');
    postData['push'] = push;
    postData['type'] = -1;


    url = "/index.php?m=wmbackstage&c=wmwithdrawals&a=set_paymentdata_check";

    layer.open({
        type : 0,
        title : '请再次确定',
        btn : ['是','否'],
        icon : 3,
        closeBtn : 2,
        content : "是否将状态改为【提现失败】！",
        scrollbar : true,
        yes : function () {
            //执行跳转
            tochange(url,postData);   //抛送ajax请求
        }

    });

});




/**
 * 功能：checkbox全选或全不选
 */
$("#check_all").click(function(){

    var test = $("input[name='status_checkbox']:checked").val();
    if(!test) {
        $("input[name='status_checkbox']").attr("checked","true");

    }else if(test) {
        $("input[name='status_checkbox']").removeAttr("checked");
    }



});

$("#save_paymentdata").click(function () {
    postData = {};

    $('.add_pay_form').each(function (i) {
        postData[i] = $(this).serializeArray();
    });

    url = "/index.php?m=wmbackstage&c=wmchannel&a=entry_paymentdata_check";
    layer.open({
        type : 0,
        title : '请再次确定',
        btn : ['是','否'],
        icon : 3,
        closeBtn : 2,
        content : "是否保存记录！",
        scrollbar : true,
        yes : function () {
            //执行跳转
            tochange(url,postData);   //抛送ajax请求
        }

    });

});

$('#textarea_pay_button').click(function () {
    var text_data = $('textarea[name = "textarea_pay"]').val();
    var postData = {
        'text_data' : text_data,
    };

    var url = '/index.php?m=wmbackstage&c=wmwithdrawals&a=set_paymentdata';
    $.post(url,postData,function (result) {
        if(result.status == 0) {
            return dialog.error(result.message);
        }
        if(result.status == 1) {
            return dialog.success(result.message,'');
        }
    },'JSON');

});


$('.wm_table_center #updata_list').click(function () {
    var id = $(this).attr('attr-id');
    var url = SCOPE.edit_url + '&id='+id;
    window.location.href = url; //url跳转
});

$('.wm_table_center #delete_list').click(function () {
    var id = $(this).attr('attr-id');
    var message =  $(this).attr('attr-message');

    var url = SCOPE.delete_list_url;
    data = {};
    data['id'] = id;
    layer.open({
        type : 0 ,
        title : '是否提交？',
        btn : ['确定','取消'],
        icon : 3,
        closeBtn : 2,
        content : "是否确定"+message,
        scrollbar : true,
        yes : function () {
            //执行相关跳转
            $.post(url,data,function (result) {
                if(result.status == 0) {
                    return dialog.error(result.message);
                }
                if(result.status == 1) {
                    return dialog.success(result.message,'');
                }
            },'JSON');
        }
    });
});


function tochange(url,$postData) {
    $.post(url,postData,function (result) {
        if(result.status == 0) {
            return dialog.error(result.message);
        }
        if(result.status == 1) {
            return dialog.success(result.message,'');
        }
    },'JSON');
}

$('#updata_bulletin').on('click',function () {
    //1.1 获取html中用户输入内容
    var id = $('input[name = "id"]').val();
    var title = $('input[name = "title"]').val();
    var detail = $('textarea[name = "detail"]').val();

    //1.2 验证获取数据的有效性
    if(!title)
        return dialog.msg('标题不能为空！');
    if(!detail)
        return dialog.msg('公告详情不能为空！');

    //1.3 数据封装
    var postData = {
        'id' : id,
        'title' : title,
        'detail' : detail,
    };

    //1.4 定义POST链接地址和跳转页面地址
    var postUrl = '/index.php?m=wmbackstage&c=wmnotice&a=updata_bulletin_check';
    var jumpUrl = '/index.php?m=wmbackstage&c=wmnotice';

    //1.5 Ajax异步数据传输
    $.post(postUrl,postData,function (result) {
        if(result.status == 0) {
            return dialog.error(result.message);
        }
        if(result.status == 1) {
            return dialog.success(result.message,jumpUrl);
        }
    },'JSON');
});

$('#update_material').on('click',function () {
    var matter_id = $('input[name = "matter_id"]').val();
    var book_id = $('input[name = "bookid"]').val();
    var book_name = $('input[name = "bookname"]').val();
    var book_type = $('option:selected').val();
    var book_detail =$('textarea[name = "bookdetail"]').val();
    var afterurl = $('input[name = "afterurl"]').val();
    var file_up = $('input[name = "file_up"]').val();


    if(!book_id)
        return dialog.msg('请输入书号！');
    if(!book_name)
        return dialog.msg('请输入书名！');
    if(!book_type)
        return dialog.msg('请选择类型！');
    if(!book_detail)
        return dialog.msg('请输入素材详情！');
    if(!afterurl)
        return dialog.msg('请输入后续阅读地址！');

    var postData = {
        'matter_id' : matter_id,
        'book_id' : book_id,
        'book_name' : book_name,
        'book_type' : book_type,
        'book_detail' : book_detail,
        'afterurl' : afterurl,
        'file_upload' : file_up,
    };

    //1.5 定义POST链接地址和跳转页面地址
    var postUrl = '/index.php?m=wmbackstage&c=wmmatter&a=update_material_check';
    var jumpUrl = '/index.php?m=wmbackstage&c=wmmatter';
    $.post(postUrl,postData,function (result) {
        if(result.status == 0) {
            return dialog.error(result.message);
        }
        if(result.status == 1) {
            return dialog.success(result.message,jumpUrl);
        }
    },'JSON');

});


$('#update_user').on('click',function () {
    /*var data = $("#user-form").serializeArray();   //获取form表单数据*/
    var user_id = $('input[name = "user_id"]').val();
    var username = $('input[name = "username"]').val();
    var user_type = $('#user_type').val();
    var c_number = $('input[name = "c_number"]').val();
    var public_name = $('input[name = "public_name"]').val();
    var proportion = $('input[name = "proportion"]').val();
    var column_name = $('input[name = "column_name"]').val();
    var alipay_number = $('input[name = "alipay_number"]').val();
    var introducer_id = $('input[name = "introducer_id"]').val();
    var company_name = $('input[name = "company_name"]').val();
    var bank = $('input[name = "bank"]').val();
    var bank_account = $('input[name = "bank_account"]').val();
    var business = $('input[name = "business"]').val();
    var certificate = $('input[name = "certificate"]').val();



    var postData = {
        'user_id' : user_id,
        'username' : username,
        'user_type' : user_type,
        'c_number' : c_number,
        'public_name' : public_name,
        'proportion' : proportion,
        'column_name' : column_name,
        'alipay_number' : alipay_number,
        'introducer_id' : introducer_id,
        'company_name' : company_name,
        'bank' : bank,
        'bank_account' : bank_account,
        'business' : business,
        'certificate' : certificate,
    };
    /*$(data).each(function () {
        postData[this.name] = this.value;
    });*/
    //console.log(postData);
    var postUrl = '/index.php?m=wmbackstage&c=wmuser&a=update_user_check';
    var jumpUrl = '/index.php?m=wmbackstage&c=wmuser';
    //将获取到的数据post给服务器

    $.post(postUrl,postData,function (result) {
        if(result.status == 1) {
            return dialog.success(result.message,jumpUrl);//成功
        }else if(result.status == 0) {
            return dialog.error(result.message);//失败
        }
    },"JSON");

});


$('.wm_table_center #paymentdata_achieve').on('click',function () {      //切换状态
    var serial_number = $(this).attr('attr-id');
    $(".pay_achieve_body").html(' ');
    postData = {
        'serial_number' : serial_number,
    };

    url = "/index.php?m=wmbackstage&c=wmwithdrawals&a=user_paymentdata_achieve";
    $.post(url,postData,function (result) {
        for(var i in result.achieve) {
            for(var j in result.achieve[i]) {
                if(result.achieve[i][j] == null)
                    result.achieve[i][j] = 0;

            }

            $(".pay_achieve_body").append(
                '<tr class="odd gradeX">' +
                '<td class="wm_table_center " id="re_time">'+result.achieve[i]['re_time']+'</td>' +
                '<td class="wm_table_center" id="user_id">'+result.achieve[i]['user_id']+'</td>' +
                '<td class="wm_table_center" id="username">'+result.achieve[i]['username']+'</td>' +
                '<td class="wm_table_center" id="c_name">'+result.achieve[i]['c_number']+'</td>' +
                '<td class="wm_table_center" id="underline_number">'+result.achieve[i]['underline_number']+'</td>' +
                '<td class="wm_table_center" id="register">'+result.achieve[i]['register']+'</td>' +
                '<td class="wm_table_center" id="recharge">'+result.achieve[i]['recharge']+'</td>' +
                '<td class="wm_table_center" id="recharge_s">'+result.achieve[i]['recharge_s']+'</td> ' +
                '<td class="wm_table_center" id="success_p">'+result.achieve[i]['success_p']+'</td>' +
                '<td class="wm_table_center" id="commission">'+result.achieve[i]['commission']+'</td>' +
                '<td class="wm_table_center" id="divided_amount">'+result.achieve[i]['divided_amount']+'</td>' +
                '<td class="wm_table_center" id="acc_explain">'+result.achieve[i]['acc_explain']+'</td>' +
                '</tr>'

                );
        }
    },"JSON");

});


$('#wm-login').on('click',function () {      //切换状态
    var username = $('input[name = "username"]').val();
    var password = $('input[name = "password"]').val();

    if(!username) {
        return dialog.msg('请输入用户名！');
    }
    if(!username) {
        return dialog.msg('请输入密码！');
    }
    var postUrl = "/index.php?m=wmbackstage&c=login&a=verify_login";
    var jumpUrl ="/index.php?m=wmbackstage&c=wmuser&a=index";
    var postData = {
        'username' : username,
        'password' : password,
    }

    $.post(postUrl,postData,function (result) {
        if(result.status == 1) {
            return dialog.success(result.message,jumpUrl);//成功
        }else if(result.status == 0) {
            return dialog.error(result.message);//失败
        }
    },"JSON");

});

$('.wm_table_center #change-fapiao').on('click',function () {
    var serial_number = $(this).attr('attr-id');
    var url = '/index.php?m=wmbackstage&c=wmwithdrawals&a=change_fapiao_status';

    layer.open({
        type : 0,
        title : '请选择',
        btn : ['通过审核','审核不通过'],
        icon : 7,
        closeBtn : 2,
        content : "&nbsp;&nbsp;&nbsp;将发票审核的状态设置为：",
        scrollbar : true,
        yes : function () {
            //执行跳转
            var data = {
                'fapiao_status' : 1,
                'serial_number' : serial_number,
            }
            $.post(url,data,function (result) {
                if(result.status == 0) {
                    return dialog.error(result.message);
                }
                if(result.status == 1) {
                    return dialog.success(result.message,'');
                }
            },'JSON');
        },
        btn2 : function () {
            var data = {
                'fapiao_status' : -1,
                'serial_number' : serial_number,
            }
            $.post(url,data,function (result) {
                if(result.status == 0) {
                    return dialog.error(result.message);
                }
                if(result.status == 1) {
                    return dialog.success(result.message,'');
                }
            },'JSON');
        },

    });

});

$('#upload-huidan').click(function () {
    var huidan = $('input[name = "fapiao-chongxin"]').val();
    var serial_number = $('input[name = "tixian-id"]').val();
    var data = {
        'huidan' : huidan,
        'serial_number' : serial_number,
        'pay_status' : 1,
        'post_status' : 1,
    }
    var url = '/index.php?m=wmbackstage&c=wmwithdrawals&a=upload_huidan';
    $.post(url,data,function (result) {
        if(result.status == 0) {
            return dialog.error(result.message);
        }
        if(result.status == 1) {
            return dialog.success(result.message,'');
        }
    },'JSON');
});