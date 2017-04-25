<?php
/**
 * FILE_NAME :  WmuserController.class.php
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
use Think\Exception;
/**
 * WmuserController类
 *
 * 功能1：显示用户列表信息
 * 功能2：用户的下线用户管理
 * 功能3：切换用户的状态
 * 功能4：导出用户信息至EXCEL
 * 功能5：修改用户信息页面显示
 * 功能6：检查并保存用户提交的修改信息
 * 功能7：删除用户信息 *
 *
 * @author      liuzezhong <liuzezhong@hongshu.com>
 * @access      public
 * @abstract
 */
class WmuserController extends CommonController {


    /**
     * 功能：显示用户列表信息
     */
    public function index(){
        $data = array();
        try{
            // 1.1 过滤数据
            if(I('get.username','','trim,string')) {
                $data['username'] = I('get.username','','trim,string');
                $this->assign('input_username',$data['username']);
            }
            if(I('get.publicname','','trim,string')) {
                $data['public_name'] = I('get.publicname','','trim,string');
                $this->assign('input_publicname',$data['public_name']);

            }
            if(I('get.introducer',0,'intval')) {
                $data['introducer_id'] = I('get.introducer',0,'intval');
                $this->assign('input_introducer',$data['introducer_id']);
            }
            if(I('get.account_status',0,'intval')) {
                $account_status = I('get.account_status',0,'intval');
                if($account_status != -2) {
                    if($account_status == 2) {
                        $data['account_status'] = 0;
                        $data['bank_account'] = array('neq','');
                    }else {
                        $data['account_status'] = $account_status;
                    }
                }
                $this->assign('account_status',$account_status);
            }

            //1.2 获取当前页码
            $now_page = I('request.p',1,'intval');
            $page_size = I('request.pageSize',10,'intval');
            $page = $now_page ? $now_page : 1;
            //1.3 设置默认分页条数
            $pageSize = $page_size ? $page_size : 10;
            //1.4 数据库查询

            $user_admin = D("Wmadmin")->get_all_useradmin_limit($data,$page,$pageSize);
            $user_adminCount = D("Wmadmin")->get_count_useradmin($data);
            //1.5 实例化一个分页对象
            $res = new Wmpage($user_adminCount,$pageSize);
            //1.6 调用show方法前台显示页码
            $pageRes = $res->show();
            //1.7 数据处理
            foreach ($user_admin as $k => $item){
                if($item['user_status'] == 0)
                    $user_admin[$k]['user_status_zh'] = '冻结中';
                if($item['user_status'] == 1)
                    $user_admin[$k]['user_status_zh'] = '正常';
            }
            //1.8 将数据传递前台模板
            $this->assign(array(
                'user_adminCount' => $user_adminCount,
                'user_admins' => $user_admin,
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
     * 功能：用户的下线用户管理
     */
    public function underline(){
        $data = array();
        if($_GET) {
            try{
                // 1.1 过滤数据
                if(I('get.username','','trim,string')) {
                    $data['under_username'] = I('get.username','','trim,string');
                    $this->assign('input_username',$data['under_username']);
                    $user_admin = D('Wmadmin')->get_one_user_by_username($data['under_username']);
                    $data['introducer_id'] =  $user_admin['introducer_id'];
                }
                if(I('get.publicname','','trim,string')) {
                    $data['public_name'] = I('get.publicname','','trim,string');
                    $this->assign('input_publicname',$data['public_name']);
                }
                if(I('get.introducer',0,'intval')) {
                    $data['introducer_id'] = I('get.introducer',0,'intval');
                    $this->assign('input_introducer',$data['introducer_id']);
                }
                //1.2 获取当前页码
                $now_page = I('request.p',1,'intval');
                $page_size = I('request.pageSize',10,'intval');
                $page = $now_page ? $now_page : 1;
                //1.3 设置默认分页条数
                $pageSize = $page_size ? $page_size : 10;
                //1.4 数据库查询
                $user_admin = D("Wmadmin")->get_all_useradmin_limit($data,$page,$pageSize);
                $user_adminCount = D("Wmadmin")->get_count_useradmin($data);
                //1.5 实例化一个分页对象
                $res = new Wmpage($user_adminCount,$pageSize);
                //1.6 调用show方法前台显示页码
                $pageRes = $res->show();
                //1.7 数据处理

                foreach ($user_admin as $k => $item){
                    if($item['user_status'] == 0)
                        $user_admin[$k]['user_status_zh'] = '冻结中';
                    if($item['user_status'] == 1)
                        $user_admin[$k]['user_status_zh'] = '正常';
                }
                //1.8 将数据传递前台模板
                $this->assign(array(
                    'user_adminCount' => $user_adminCount,
                    'user_admins' => $user_admin,
                    'pageRes' => $pageRes,
                ));
            } catch (Exception $e) {
                $this->assign(array(
                    'status' => 0,
                    'message' => $e->getMessage(),
                ));
            }
        }
        $this->display();
    }

    /**
     * 功能：切换用户的状态
     */
    public function changetype(){
        //1.1 从POST中取得用户传递数据
        if(I('post.user_id',0,'intval'))
            $user_id = I('post.user_id',0,'intval');
        if(I('post.user_status',0,'intval'))
            $user_status = I('post.user_status',0,'intval');
        //1.2 数据封装
        $data['user_status'] = $user_status;
        //1.3 数据库操作
        try {
            //1.3.1 通过用户ID更新数据
            $res = D('Wmadmin')->update_user_by_id($user_id,$data);
            //1.3.2 判断更新结果
            if(!$res){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'message' => '状态修改失败！',
                ));
            }
            $this->ajaxReturn(array(
                'status' => 1,
                'message' => '状态修改成功！',
            ));

        } catch (Exception $e) {
            //1.3.3 异常处理
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
    }

    public function changetype_duigong(){
        //1.1 从POST中取得用户传递数据
        if(I('post.user_id',0,'intval'))
            $user_id = I('post.user_id',0,'intval');
        if(I('post.account_status',0,'intval'))
            $account_status = I('post.account_status',0,'intval');
        //1.2 数据封装
        $data['account_status'] = $account_status;
        //1.3 数据库操作
        try {
            //1.3.1 通过用户ID更新数据
            $res = D('Wmadmin')->update_user_by_id($user_id,$data);
            //1.3.2 判断更新结果
            if(!$res){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'message' => '状态修改失败！',
                ));
            }
            $this->ajaxReturn(array(
                'status' => 1,
                'message' => '状态修改成功！',
            ));

        } catch (Exception $e) {
            //1.3.3 异常处理
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
    }
    /**
     * 功能：导出用户信息至EXCEL
     */
    public function wmexp_user(){
        header('Content-Type: text/xls');
        header("Content-type:application/vnd.ms-excel;charset=utf-8");
        header("Content-Disposition: attachment; filename=". date("YmdHis", NOW_TIME) . ".xls");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header("Pragma: no-cache");
        $data = array();
        // 1.1 过滤数据
        if(I('get.username','','trim,string')) {
            $data['username'] = I('get.username','','trim,string');
        }
        if(I('get.publicname','','trim,string')) {
            $data['public_name'] = I('get.publicname','','trim,string');
        }
        if(I('get.introducer',0,'intval')) {
            $data['introducer_id'] = I('get.introducer',0,'intval');
        }
        try {
            //1.3.1 获取所有用户信息
            $xlsData = D('Wmadmin')->get_all_useradmin($data);
            //1.3.2 数据处理
            foreach ($xlsData as $k => $item) {
                if($item['user_status'] == 0)
                    $xlsData[$k]['user_status'] = '冻结中';
                else if($item['user_status'] == 1)
                    $xlsData[$k]['user_status'] = '正常';
                $xlsData[$k]['register_time'] = date('Y-m-d H:i:s', $item['register_time']);
            }
            //1.3.3 导出数据
            echo '<table border=1>';
            echo '<tr>';
            echo '<th>用户ID</th>';
            echo '<th>用户名</th>';
            echo '<th>公众号名</th>';
            echo '<th>公司名称</th>';
            echo '<th>渠道号</th>';
            echo '<th>用户状态</th>';
            echo '<th>支付宝姓名</th>';
            echo '<th>支付宝号码</th>';
            echo '<th>上线用户ID</th>';
            echo '<th>注册时间</th>';
            echo '</tr>';
            foreach ($xlsData as $k => $item) {
                unset($item['password']);
                echo '<tr>';
                foreach ($item as $m => $n) {
                    echo '<td>'.$n.'</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        } catch (Exception $e) {
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
    }

    /**
     * 功能：导出用户下线信息至EXCEL
     */
    public function wmexp_user_under(){
        header('Content-Type: text/xls');
        header("Content-type:application/vnd.ms-excel;charset=utf-8");
        header("Content-Disposition: attachment; filename=". date("YmdHis", NOW_TIME) . ".xls");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header("Pragma: no-cache");
        $data = array();
        // 1.1 过滤数据
        if(I('get.username','','trim,string')) {
            $data['under_username'] = I('get.username','','trim,string');
            $user_admin = D('Wmadmin')->get_one_user_by_username($data['under_username']);
            $data['introducer_id'] =  $user_admin['introducer_id'];
        }
        if(I('get.introducer',0,'intval')) {
            $data['introducer_id'] = I('get.introducer',0,'intval');
        }
        try {
            //1.3.1 获取所有用户信息
            $xlsData = D('Wmadmin')->get_all_useradmin($data);
            //1.3.2 数据处理
            foreach ($xlsData as $k => $item) {
                if($item['user_status'] == 0)
                    $xlsData[$k]['user_status'] = '冻结中';
                else if($item['user_status'] == 1)
                    $xlsData[$k]['user_status'] = '正常';
                $xlsData[$k]['register_time'] = date('Y-m-d H:i:s', $item['register_time']);
            }
            //1.3.3 导出数据
            echo '<table border=1>';
            echo '<tr>';
            echo '<th>用户ID</th>';
            echo '<th>用户名</th>';
            echo '<th>公众号名</th>';
            echo '<th>公司名称</th>';
            echo '<th>渠道号</th>';
            echo '<th>用户状态</th>';
            echo '<th>支付宝姓名</th>';
            echo '<th>支付宝号码</th>';
            echo '<th>上线用户ID</th>';
            echo '<th>注册时间</th>';
            echo '</tr>';
            foreach ($xlsData as $k => $item) {
                unset($item['password']);
                echo '<tr>';
                foreach ($item as $m => $n) {
                    echo '<td>'.$n.'</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        } catch (Exception $e) {
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
    }

    /**
     * 功能：修改用户信息页面，将用户信息填写至输入框
     */
    public function edit_user() {
        $user_id = I('get.id',0,'intval');
        try {
            $user = D('Wmadmin')->get_one_user_by_id($user_id);
            if(!$user) {
                $this->assign(array(
                    'status' => 0,
                    'message' => '用户不存在！',
                ));
            }
            $this->assign('user',$user);
        } catch (Exception $e) {
            $this->assign(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
        $this->display();
    }

    /**
     * 功能：检查并保存用户提交的修改信息
     */
    public function update_user_check() {
        $user_id = I('post.user_id',0,'intval');
        $data = I('post.');

        unset($data['user_id']);
        try {
            $data['update_time'] = time();
            $res = D('Wmadmin')->update_user_by_id($user_id,$data);
            if(!$res){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'message' => '用户信息更新失败！',
                ));
            }
            $this->ajaxReturn(array(
                'status' => 1,
                'message' => '更新用户信息成功！',
            ));
        } catch (Exception $e) {
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => $e->getMessage(),

            ));
        }
    }

    /**
     * 功能：删除用户信息
     */
    public function delete_user() {
        $user_id = I('post.id',0,'intval');
        try {

            // 删除业绩表关联数据
            $yeji = D('Wmachievement')->find_achievement_by_userid($user_id);
            $dele_yeji = array_column($yeji,'ach_id');
            if($dele_yeji) {
                $dele_yeji_s = D('Wmachievement')->delete_achievement_by_id($dele_yeji);
            }

            // 删除提现表关联数据
            $tixian = D('Wmwithdrawals')->find_wmwithdrawals_by_userid($user_id);
            $dele_tixian = array_column($tixian,'serial_number');
            if($dele_tixian) {
                $dele_tixian_s = D('Wmwithdrawals')->delete_wmwithdrawals_by_id($dele_tixian);
            }

            // 删除用户表数据
            $res = D('Wmadmin')->delete_one_useradmin_by_id($user_id);

            if(!$res){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'message' => '用户删除失败！',
                ));
            }
            $this->ajaxReturn(array(
                'status' => 1,
                'message' => '用户删除成功！',

            ));
        } catch (Exception $e) {
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => $e->getMessage(),

            ));
        }
    }
}