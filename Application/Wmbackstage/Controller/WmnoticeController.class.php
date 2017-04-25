<?php
/**
 * FILE_NAME :  WmnoticeController.class.php
 * 模块:Wmbackstage
 * 域名:union.yanqingkong.com
 *
 * 功能:公告信息控制器
 *
 *
 * @copyright Copyright (c) 2017 – www.hongshu.com
 * @author liuzezhong@hongshu.com
 * @version 1.0 2017/3/21 14:06
 */

namespace Wmbackstage\Controller;
use Think\Exception;
/**
 * WmnoticeController类
 *
 * 功能1：显示公告列表
 * 功能2：公告上传页面显示
 * 功能3：新增公告数据检测
 * 功能4：修改公告信息
 * 功能5：检查并保存上传数据
 * 功能6：删除公告记录
 *
 * @author      liuzezhong <liuzezhong@hongshu.com>
 * @access      public
 * @abstract
 */
class WmnoticeController extends CommonController {


    /**
     * 功能：显示公告列表
     */
    public function index() {
        $data = array();
        try {
            //1.1 获取GET数据
            if(I('get.notice_title','','trim,string')) {
                $data['notice_title'] = I('get.notice_title','','trim,string');
                $this->assign('input_notice_title',$data['notice_title']);
            }
            if(I('get.notice_time','','trim,string')) {
                $data['notice_time'] = I('get.notice_time','','trim,string');
                $data['begin_date'] = strtotime($data['notice_time']);
                $data['end_date'] = $data['begin_date'] + 86399;
                $this->assign('input_notice_name',$data['notice_time']);
            }
            //1.1 判断当前页码
            $now_page = I('request.p',1,'intval');
            $page_size = I('request.pageSize',10,'intval');
            $page = $now_page ? $now_page : 1;
            //1.2 设置默认分页条数
            $pageSize = $page_size ? $page_size : 10;
            //1.3 数据库查询
            $announcement = D("Wmnotice")->get_all_announcement_limit($data,$page,$pageSize);
            $announcementCount = D("Wmnotice")->get_count_announcement($data);
            //1.4 实例化一个分页对象
            $res = new Wmpage($announcementCount,$pageSize);
            //1.5 调用show方法前台显示页码
            $pageRes = $res->show();
            //1.6 获取
            $this->assign(array(
                'notices' => $announcement,
                'pageRes' => $pageRes,
            ));
            //1.6 数据传递到前台模板
        } catch (Exception $e) {
            $this->assign(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
        $this->display();
    }

    /**
     * 功能：公告上传页面显示
     */
    public function upload_bulletin() {
        $this->display();
    }

    /**
     * 功能：新增公告数据检测
     */
    public function upload_bulletin_check() {
        $title = I('post.title','','trim,string');
        $detail = I('post.detail','','');

        if(!$title){
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '公告标题不能为空！',
            ));
        }
        if(!$detail){
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '公告内容不能为空！',
            ));
        }
        //1.2 数据封装
        $data = array(
            'notice_title' => $title,
            'notice_content' => $detail,
            'notice_time' => time(),
        );
        //1.3 将数据写入数据库
        try {
            $res = D('Wmnotice')->add_notice($data);
            if(!$res){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'message' => '公告发布失败！',
                ));
            }
            $this->ajaxReturn(array(
                'status' => 1,
                'message' => '公告发布成功！',
            ));
        } catch (Exception $e) {
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
    }

    /**
     * 功能：修改公告信息
     */
    public function edit_bulletin(){
        $notice_id = I('get.id',0,'intval');
        try {
            $notice = D('Wmnotice')->get_one_notice_by_id($notice_id);
            if(!notice) {
                $this->assign(array(
                    'status' => 0,
                    'message' => '公告内容不存在！',
                ));
            }
            $this->assign('notice',$notice);
        } catch (Exception $e) {
            $this->assign(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
        $this->display();
    }

    /**
     * 功能：检查并保存上传数据
     */
    public function updata_bulletin_check(){
        $id = I('post.id',0,'intval');
        $title = I('post.title','','trim,string');
        $detail = I('post.detail','','trim,string');
        if(!$id){
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '公告ID不存在！',
            ));
        }
        if(!$title){
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '公告标题不能为空！',
            ));
        }
        if(!$detail){
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '公告内容不能为空！',
            ));
        }
        //1.2 数据封装
        $data = array(
            'notice_title' => $title,
            'notice_content' => $detail,
            'notice_time' => time(),
        );
        //1.3 将数据写入数据库
        try {
            $res = D('Wmnotice')->update_one_notice_by_id($id,$data);
            if(!$res){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'message' => '公告修改失败！',
                ));
            }
            $this->ajaxReturn(array(
                'status' => 1,
                'message' => '公告修改成功！',
            ));
        } catch (Exception $e) {
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
    }

    /**
     * 功能：删除公告记录
     */
    public function delete_bulletin() {
        $notice_id = I('post.id',0,'intval');
        try {
            $res = D('Wmnotice')->del_one_notice_by_id($notice_id);
            if(!$res){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'message' => '公告删除失败！',
                ));
            }
            $this->ajaxReturn(array(
                'status' => 1,
                'message' => '公告删除成功！',
            ));
        } catch (Exception $e) {
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
    }

}