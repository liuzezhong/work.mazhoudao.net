<?php
/**
 * FILE_NAME :  WmmatterController.class.php
 * 模块:Wmbackstage
 * 域名:union.yanqingkong.com
 *
 * 功能:素材信息控制器
 *
 *
 * @copyright Copyright (c) 2017 – www.hongshu.com
 * @author liuzezhong@hongshu.com
 * @version 1.0 2017/3/21 14:06
 */

namespace Wmbackstage\Controller;
use Think\Exception;
/**
 * WmmatterController类
 *
 * 功能1：素材列表显示
 * 功能2：上传素材页面
 * 功能3：对上传素材的内容进行检查并处理
 * 功能4：修改素材信息页面
 * 功能5：检查并更新素材修改信息
 * 功能6：删除素材信息
 *
 * @author      liuzezhong <liuzezhong@hongshu.com>
 * @access      public
 * @abstract
 */
class WmmatterController extends CommonController {

    /**
     * 功能：素材列表显示
     */
    public function index() {
        $data = array();
        try {
            //1.1 获取GET数据并回显
            if(I('get.bookname','','trim,string')) {
                $data['bookname'] = I('get.bookname','','trim,string');
                $this->assign('input_bookname',$data['bookname']);
            }
            if(I('get.bookid',0,'intval')) {
                $data['bookid'] = I('get.bookid',0,'intval');
                $this->assign('input_bookid',$data['bookid']);
            }
            //1.2 判断当前页码
            $now_page = I('request.p',1,'intval');
            $page_size = I('request.pageSize',10,'intval');
            $page = $now_page ? $now_page : 1;
            //1.3 设置默认分页条数
            $pageSize = $page_size ? $page_size : 10;
            //1.4 数据库查询
            $matter = D("Wmmatter")->get_all_matter_limit($data,$page,$pageSize);
            $matter_count = D("Wmmatter")->get_count_matter($data);
            //1.5 实例化一个分页对象
            $res = new Wmpage($matter_count,$pageSize);
            //1.6 调用show方法前台显示页码
            $pageRes = $res->show();
            //1.7 数据处理
            $i = 1;
            foreach ($matter as $k => $item) {
                $matter[$k]['type_name'] = C('BOOK_TYPE')[$item['type_id']];
                $matter[$k]['number'] = $i++;
            }
            //1.8 将数据传递给模板
            $this->assign(array(
                'matters' => $matter,
                'matterCount' => $matter_count,
                'pageRes' => $pageRes,
            ));
        } catch (Exception $e) {
            $this->assign(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
        $this->display();
    }

    /**
     * 功能：上传素材页面
     */
    public function upload() {
        try {
            //1.1 取得素材类型数据
            $matter_type = C('BOOK_TYPE');
            //1.2 将数据传递给前台模板
            $this->assign(array(
                'matterTypes' => $matter_type,
            ));
        } catch (Exception $e) {
            $this->assign(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
        $this->display();
    }

    /**
     * 功能：对上传素材的内容进行检查并处理
     */
    public function upload_check() {
        //1.1 获取数据
        $bid = I('post.book_id',0,'intval');
        $catename = I('post.book_name','','trim,string');
        $type_id = I('post.book_type',0,'intval');
        $matter_detail = I('post.book_detail','','trim,string');
        $after_url = I('post.afterurl','','trim,string');
        $upload_file = I('post.file_upload','','trim,string');
        //1.2 检查数据
        if(!$bid){
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '书号不能为空！',
            ));
        }
        if(!$catename){
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '书名不能为空！',
            ));
        }
        if(!$type_id){
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '请选择类型！',
            ));
        }
        if(!$matter_detail){
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '素材详情不能为空！',
            ));
        }
        if(!$after_url){
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '后续地址不能为空！',
            ));
        }
        //1.3 封装数据
        $data = array(
            'bid' => $bid,
            'catename' => $catename,
            'type_id' => $type_id,
            'matter_detail' => $matter_detail,
            'after_url' => $after_url,
            'upload_file' => $upload_file,
            'upload_time' => time(),
        );
        //1.4 写入数据库
        try {
            //1.4.1 新增一条素材记录
            $res = D('Wmmatter')->add_one_matter($data);
            //1.4.2 判断结果
            if(!$res) {
                $this->ajaxReturn(array(
                    'status' => 0,
                    'message' => '素材上传失败！',
                ));
            }
            $this->ajaxReturn(array(
                'status' => 1,
                'message' => '素材上传成功！',
            ));
        } catch (Exception $e) {
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
    }

    /**
     * 功能：修改素材信息页面
     */
    public function edit_material() {
        $material_id = I('get.id',0,'intval');
        try {
            $material = D('Wmmatter')->get_one_matter_by_id($material_id);
            $matter_type = C('BOOK_TYPE');
            //1.2 将数据传递给前台模板

            if(!$material) {
                $this->assign(array(
                    'status' => 0,
                    'message' => '数据不存在！',

                ));
            }
            $type_name = C('BOOK_TYPE')[$material['type_id']];
            $this->assign(array(
                'matterTypes' => $matter_type,
                'material' => $material,
                'typename' => $type_name,
            ));
        } catch (Exception $e) {
            $this->assign(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
        $this->display();
    }

    /**
     * 功能：检查并更新素材修改信息
     */
    public function update_material_check() {
        //1.1 获取数据
        $matter_id = I('post.matter_id',0,'intval');
        $bid = I('post.book_id',0,'intval');
        $catename = I('post.book_name','','trim,string');
        $type_id = I('post.book_type',0,'intval');
        $matter_detail = I('post.book_detail','','trim,string');
        $after_url = I('post.afterurl','','trim,string');
        $upload_file = I('post.file_upload','','trim,string');
        //1.2 检查数据
        if(!$bid){
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '书号不能为空！',
            ));
        }
        if(!$catename){
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '书名不能为空！',
            ));
        }
        if(!$type_id){
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '请选择类型！',
            ));
        }
        if(!$matter_detail){
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '素材详情不能为空！',
            ));
        }
        if(!$after_url){
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '后续地址不能为空！',
            ));
        }
        //1.3 封装数据
        $data = array(
            'bid' => $bid,
            'catename' => $catename,
            'type_id' => $type_id,
            'matter_detail' => $matter_detail,
            'after_url' => $after_url,
            'upload_file' => $upload_file,
            'upload_time' => time(),
        );
        //1.4 写入数据库
        try {
            //1.4.1 新增一条素材记录
            $res = D('Wmmatter')->update_one_matter_by_id($matter_id,$data);
            //1.4.2 判断结果
            if(!$res) {
                $this->ajaxReturn(array(
                    'status' => 0,
                    'message' => '素材修改失败！',
                ));
            }
            $this->ajaxReturn(array(
                'status' => 1,
                'message' => '素材修改成功！',
            ));
        } catch (Exception $e) {
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
    }

    /**
     * 功能：删除素材信息
     */
    public function delete_material() {
        $material_id = I('post.id',0,'intval');
        try {
            $old_res = D('Wmmatter')->get_one_matter_by_id($material_id);
            $upload_file = $old_res['upload_file'];
            //$upload_file = 'http://ad.mazhoudao.net/Uploads/2017/04/13/58eef86746baa.docx';
            $file = '..' . substr($upload_file,6);

            if (!unlink($file)) {
                $message = '附件文件删除失败';
            }


            $res = D('Wmmatter')->delete_one_matter_by_id($material_id);
            if(!$res){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'message' => '素材删除失败！' . $message,
                ));
            }
            $this->ajaxReturn(array(
                'status' => 1,
                'message' => '素材删除成功！' . $message,
            ));
        } catch (Exception $e) {
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
    }
}