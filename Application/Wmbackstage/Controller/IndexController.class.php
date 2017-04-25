<?php
/**
 * FILE_NAME :  IndexController.class.php
 * 模块:Wmbackstage
 * 域名:union.yanqingkong.com
 *
 * 功能:用户菜单页面控制器
 *
 *
 * @copyright Copyright (c) 2017 – www.hongshu.com
 * @author liuzezhong@hongshu.com
 * @version 1.0 2017/3/21 14:06
 */

namespace Wmbackstage\Controller;


class IndexController extends CommonController {


    public function index() {
        redirect(U('wmbackstage/wmuser/index'));
    }

    /**
     * 功能：文件异步上传
     */
    public function ajaxUploadImage() {
        $upload = D("UploadImage");
        $res = $upload->imageUpload();

        if($res===false) {
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '上传失败！'

            ));
        }else{
            $this->ajaxReturn(array(
                'status' => 1,
                'message' => '上传成功！',
                'data' => $res,
            ));

        }
    }

    public function kindupload() {
        $upload = D("UploadImage");
        $res = $upload->upload();

        if($res===false) {
            return showKind(1,"上传失败");
        }else{
            return showKind(0,$res);
        }
    }

}