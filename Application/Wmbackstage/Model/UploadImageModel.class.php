<?php
/**
 * FILE_NAME :  UploadImageModel.class.php
 * 模块:Wmbackstage
 * 域名:union.yanqingkong.com
 *
 * 功能:文件上传操作模型
 *
 *
 * @copyright Copyright (c) 2017 – www.hongshu.com
 * @author liuzezhong@hongshu.com
 * @version 1.0 2017/3/14 10:14
 */

namespace Wmbackstage\Model;
use Think\Model;

class UploadImageModel extends Model {
    private $_uploadObj = '';
    const UPLOAD = 'Uploads';    //定义上传文件夹

    public function __construct() {
        $this->_uploadObj = new  \Think\Upload();
        $this->_uploadObj->rootPath = '../ad.mazhoudao.net/Uploads/';
        $this->_uploadObj->subName = date(Y) . '/' . date(m) .'/' . date(d);
    }

    public function imageUpload() {
        $res = $this->_uploadObj->upload();
        if($res) {
            return 'http://ad.mazhoudao.net/Uploads/' . $res['file']['savepath'] . $res['file']['savename'];
        }else{
            return false;
        }
    }

    public function upload() {     //针对编辑器的图片上传
        $res = $this->_uploadObj->upload();

        if($res) {
            return 'http://ad.mazhoudao.net/Uploads/' . $res['imgFile']['savepath'] . $res['imgFile']['savename'];
        }else{
            return false;
        }
    }
}
