<?php
/**
 * FILE_NAME :  WmwithdrawalsController.class.php
 * 模块:Wmbackstage
 * 域名:union.yanqingkong.com
 *
 * 功能:提现信息控制器
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
 * 功能1：用户提现记录列表显示
 * 功能2：用户提现详情数据显示
 * 功能3：用户提现状态管理
 * 功能4：修改用户提现状态
 * 功能5：将用户提现信息导出至EXCEL
 *
 * @author      liuzezhong <liuzezhong@hongshu.com>
 * @access      public
 * @abstract
 */
class WmwithdrawalsController extends CommonController {


    /**
     * 功能：用户提现记录列表显示
     */
    public function index(){
        $data = array();
        try {
            //1.1 如果有数据则获取GET数据
            if(I('get.userid',0,'intval')) {
                $data['user_id'] = I('get.userid',0,'intval');
                $this->assign('input_userid',$data['user_id']);  //提交过来得数据回显
            }
            if(I('get.username','','trim,string')) {
                $data['username'] = I('get.username','','trim,string');
                $this->assign('input_username',$data['username']);
                $user_info = D('Wmadmin')->get_one_user_by_username($data['username']);   //根据用户名查找ID
                $data['user_id'] = $user_info['user_id'];
            }
            if(I('get.tixian_type',0,'intval')) {
                $tixian_type = I('get.tixian_type',0,'intval');
                if($tixian_type == 2)
                    $data['tixian_type'] = 0;
                else
                    $data['tixian_type'] = $tixian_type;
                $this->assign('tixian_type',$tixian_type);
            }
            if(I('get.begin_date','','trim,string')) {
                $data['begin_date'] = I('get.begin_date','','trim,string');
                $this->assign('input_begin_date',$data['begin_date']);
                $data['begin_date'] = strtotime($data['begin_date']);
                if(I('get.end_date','','trim,string')){
                    $this->assign('input_end_date',I('get.end_date','','trim,string'));    //开始时间为凌晨
                    $data['end_date'] = strtotime(I('get.end_date','','trim,string')) + 86399;    //结束时间为23:59:59
                }
                else
                    $data['end_date'] = time();
            }
            if(I('get.status',-2,'intval')) {
                $data['status'] = I('get.status',-2,'intval');
                if($data['status'] == 2) {
                    $this->assign(array(
                        'status' => 2,
                        'status_zh' => '正在提现'
                    ));
                }else if($data['status'] == 1) {
                    $this->assign(array(
                        'status' => $data['status'],
                        'status_zh' => '已提现'
                    ));
                }else if($data['status'] == -1) {
                    $this->assign(array(
                        'status' => $data['status'],
                        'status_zh' => '提现失败'
                    ));
                }
            }
            //1.2 获取当前页码
            $now_page = I('request.p',1,'intval');
            $page_size = I('request.pageSize',10,'intval');
            $page = $now_page ? $now_page : 1;
            //1.3 设置默认分页条数
            $pageSize = $page_size ? $page_size : 10;
            //1.4 数据库查询
            $withdrawals = D("Wmwithdrawals")->get_all_withdrawals_limit($data,$page,$pageSize);
            $withdrawalsCount = D("Wmwithdrawals")->get_count_withdrawals($data);
            //1.5 实例化一个分页对象
            $res = new Wmpage($withdrawalsCount,$pageSize);
            //1.6 调用show方法前台显示页码
            $pageRes = $res->show();
            //1.7 数据处理
            $i = 1;   //列表序号
            foreach ($withdrawals as $k => $drawals) {
                $withdrawals[$k]['id'] = $i++;
                if($drawals['pay_status'] == 0) {
                    $withdrawals[$k]['status'] = '正在提现';
                }else if($drawals['pay_status'] == 1) {
                    $withdrawals[$k]['status'] = '已提现';
                }else {
                    $withdrawals[$k]['status'] = '提现失败';
                }
                $userinfo = D('Wmadmin')->get_one_user_by_id($drawals['user_id']);
                $withdrawals[$k]['username'] = $userinfo['username'];  //用户名
                $withdrawals[$k]['c_number'] = $userinfo['c_number'];   //渠道号
                $withdrawals[$k]['real_name'] = $userinfo['column_name'];
            }
            $this->assign(array(
                'withdrawals' => $withdrawals,
                'withdrawalsCount' => $withdrawalsCount,
                'pageRes' => $pageRes,
                'userinfo' => $userinfo,
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
     * 功能：用户提现详情数据显示
     */
    public function user_paymentdata_achieve() {
        $serial_number = I('post.serial_number',0,'intval');
        try {
            $achieve = D("Wmachievement")->get_all_achievement_by_serial_number($serial_number);
            foreach($achieve as $k =>$item) {
                $c_number = D('Wmadmin')->get_one_user_by_id($item['user_id']);
                $achieve[$k]['c_number'] = $c_number['c_number'];
                $achieve[$k]['username'] = $c_number['username'];
                $achieve[$k]['success_p'] = sprintf("%.2f", $item['recharge_s']/$item['recharge']*100) . '%';
                $achieve[$k]['re_time'] = date('Y-m-d', $achieve[$k]['re_time']);
            }
            $this->ajaxReturn(array(
                'achieve' => $achieve,
            ));
        } catch (Exception $e) {
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
    }

    /**
     * 功能：用户提现状态管理
     */
    public function set_paymentdata(){
        //1.1 获取POST数据
        $real_array = array();
        $style = 0 ;
        if($_POST) {
            $text_array = I('post.textarea_pay','','string');
            //1.2 将字符串以\n分割成数组
            $data_array = explode("\n",$text_array);
            //1.3 释放第一行中文和最后一行空格
            unset($data_array[0]);
            unset($data_array[count($data_array)]);
            //1.4 遍历数组
            foreach ($data_array as $k => $item) {
                //1.4.1 将数组按\t来分割成新的数组
                $pay_array = explode("\t",$item);
                $user_array[$k] = $pay_array;
                //1.4.2 数据库查找数据
                $real_array[$k] = D("Wmwithdrawals")->get_one_withdrawal_by_serialnumber(intval($pay_array[1]));
            }
            //1.5 数据处理
            foreach ($real_array as $k => $drawals) {
                //1.5.1 获取用户信息
                $userinfo = D('Wmadmin')->get_one_user_by_id($drawals['user_id']);
                $real_array[$k]['username'] = $userinfo['username'];  //用户名
                $real_array[$k]['real_name'] = $userinfo['column_name'];
                //1.5.2 设置提现状态
                if($user_array[$k][8] == '正在提现')
                    $user_array[$k][20] = 0;
                else if($user_array[$k][8] == '已提现')
                    $user_array[$k][20] = 1;
                else if($user_array[$k][8] == '提现失败')
                    $user_array[$k][20] = -1;
                if($user_array[$k][2] != $real_array[$k]['user_id'])
                    $real_array[$k]['user_id'] = '<span data-toggle="tooltip" data-placement="top" title="用户ID号应为：' . $drawals['user_id'] . '">' . '<font style="color: red;">'. $user_array[$k][2] . '</font>' . '</span>';
                if($user_array[$k][5] != $real_array[$k]['pay_money'])
                    $real_array[$k]['pay_money'] = '<span data-toggle="tooltip" data-placement="top" title="提现金额应为：' . $drawals['pay_money'] .'元' . '">' . '<font style="color: red;">'. $user_array[$k][5] . '</font>' . '</span>';
                if($user_array[$k][7] != $real_array[$k]['pay_account'])
                    $real_array[$k]['pay_account'] = '<span data-toggle="tooltip" data-placement="top" title="支付宝账户应为：' . $drawals['pay_account'] . '">' . '<font style="color: red;">'. $user_array[$k][7] . '</font>' . '</span>';
                if($user_array[$k][20] != $real_array[$k]['pay_status']) {
                    if($drawals['pay_status'] == 0)
                        $real_array[$k]['status_zh'] = '<span data-toggle="tooltip" data-placement="top" title="提现状态应为：正在提现">' . '<font style="color: red;">'. $user_array[$k][8] .'</font></span>';
                    else if($drawals['pay_status'] == 1)
                        $real_array[$k]['status_zh'] = '<span data-toggle="tooltip" data-placement="top" title="提现状态应为：已提现">' . '<font style="color: red;">'. $user_array[$k][8] .'</font></span>';
                    else if($drawals['pay_status'] == -1)
                        $real_array[$k]['status_zh'] = '<span data-toggle="tooltip" data-placement="top" title="提现状态应为：提现失败">' . '<font style="color: red;">'. $user_array[$k][8] .'</font></span>';
                }else {
                    if($drawals['pay_status'] == 0)
                        $real_array[$k]['status_zh'] = '正在提现';
                    else if($drawals['pay_status'] == 1)
                        $real_array[$k]['status_zh'] = '已提现';
                    else if($drawals['pay_status'] == -1)
                        $real_array[$k]['status_zh'] = '提现失败';
                }
                if($user_array[$k][6] != $userinfo['column_name'])
                    $real_array[$k]['real_name'] = '<span data-toggle="tooltip" data-placement="top" title="支付宝用户名应为：' . $userinfo['column_name'] . '">' . '<font style="color: red;">'. $user_array[$k][6] . '</font>' . '</span>';
                if($user_array[$k][3] != $userinfo['username'])
                    $real_array[$k]['username'] = '<span data-toggle="tooltip" data-placement="top" title="用户名应为：' . $userinfo['username'] . '">' . '<font style="color: red;">'. $user_array[$k][3] . '</font>' . '</span>';
            }
            // 前台样式显示标志
            if($real_array)
                $style = 1;
            else $style = 0;
        }
        $this->assign(array(
            'withdrawals' => $real_array,
            'text_array' => $text_array,
            'style' => $style,
        ));
        $this->display();
    }

    /**
     * 功能：修改用户提现状态
     */
    public function set_paymentdata_check() {
        $payment_id_array = I('post.push');   //数组
        $payment_type = I('post.type',0,'');
        if(!$payment_id_array)
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '至少勾选一条提现记录！',
            ));
        try {
            $withdrawals = D("Wmwithdrawals")->update_withdrawals_status_in($payment_type,$payment_id_array);
            if(!$withdrawals) {
                $this->ajaxReturn(array(
                    'status' => 0,
                    'message' => '提现状态修改失败！',
                ));
            }
            $this->ajaxReturn(array(
                'status' => 1,
                'message' => '提现状态修改成功！',
            ));
        } catch (Exception $e) {
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }

    }

    /**
     * 功能：将用户提现信息导出至EXCEL
     */
    public function wmexp_withdrawals(){
        header('Content-Type: text/xls');
        header("Content-type:application/vnd.ms-excel;charset=utf-8");
        header("Content-Disposition: attachment; filename=". date("YmdHis", NOW_TIME) . ".xls");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header("Pragma: no-cache");
        $data = array();
        if(I('get.userid',0,'intval')) {
            $data['user_id'] = I('get.userid',0,'intval');
        }
        if(I('get.username','','trim,string')) {
            $data['username'] = I('get.username','','trim,string');
            $user_info = D('Wmadmin')->get_one_user_by_username($data['username']);   //根据用户名查找ID
            $data['user_id'] = $user_info['user_id'];
        }
        if(I('get.begin_date','','trim,string')) {
            $data['begin_date'] = I('get.begin_date','','trim,string');
            $data['begin_date'] = strtotime($data['begin_date']);
            if(I('get.end_date','','trim,string')){
                $this->assign('input_end_date',I('get.end_date','','trim,string'));    //开始时间为凌晨
                $data['end_date'] = strtotime(I('get.end_date','','trim,string')) + 86399;    //结束时间为23:59:59
            }
            else
                $data['end_date'] = time();
        }
        if(I('get.status',-2,'intval')) {
            $data['status'] = I('get.status',-2,'intval');
        }
        try {
            $xlsData = D('Wmwithdrawals')->get_all_withdrawals($data);
            foreach ($xlsData as $k => $item) {
                $userinfo = D('Wmadmin')->get_one_user_by_id($item['user_id']);
                $new_xlsData[$k][0] = date('Y-m-d', $item['pay_time']);
                $new_xlsData[$k][1] = $item['serial_number'];
                $new_xlsData[$k][2] = $item['user_id'];
                $new_xlsData[$k][3] = $userinfo['username'];
                $new_xlsData[$k][4] = $userinfo['c_number'];
                $new_xlsData[$k][5] = $item['pay_money'];
                $new_xlsData[$k][6] = $userinfo['column_name'];
                $new_xlsData[$k][7] = $item['pay_account'];
                if($item['pay_status'] == 0) {
                    $new_xlsData[$k][8] = '正在提现';
                }else if($item['pay_status'] == 1) {
                    $new_xlsData[$k][8] =  '已提现';
                }else {
                    $new_xlsData[$k][8] =  '提现失败';
                }
                $new_xlsData[$k][9] = $item['pay_reason'];
                $new_xlsData[$k][10] = $item['details_number'];
            }
            echo '<table border=1>';
            echo '<tr>';
            echo '<th>日期</th>';
            echo '<th>提现记录号</th>';
            echo '<th>用户ID号</th>';
            echo '<th>用户名</th>';
            echo '<th>渠道号</th>';
            echo '<th>提现金额(元)</th>';
            echo '<th>支付宝实名</th>';
            echo '<th>提现账号</th>';
            echo '<th>提现状态</th>';
            echo '<th>提现说明</th>';
            echo '<th>交易详情</th>';
            echo '</tr>';
            foreach ($new_xlsData as $k => $item) {
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

    public function change_fapiao_status() {
        $fapiao_status = I('post.fapiao_status',0,'intval');
        $serial_number = I('post.serial_number',0,'intval');
        $data = array(
            'fapiao_status' => $fapiao_status,
        );
        $res = D('Wmwithdrawals')->update_fapiao_status($serial_number,$data);
        if(!$res) {
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '发票审核状态修改失败！',
            ));
        }
        $this->ajaxReturn(array(
            'status' => 1,
            'message' => '发票审核状态修改成功！',
        ));

    }

    public function upload_huidan() {
        $huidan = I('post.huidan','','trim,string');
        $serial_number = I('post.serial_number',0,'intval');
        $pay_status = I('post.pay_status',0,'intval');
        $details_number = I('post.details_number','','trim,string');
        $post_status = I('post.post_status',0,'intval');
        if($post_status == 1) {
            if(!$huidan) {
                $this->ajaxReturn(array(
                    'status' => 0,
                    'message' => '汇款回单上传失败！',
                ));
            }
        } else if($post_status == 2) {
            if(!$details_number) {
                $this->ajaxReturn(array(
                    'status' => 0,
                    'message' => '请输入支付宝交易单号！',
                ));
            }
        }

        $data = array(
            'huidan' => $huidan,
            'pay_status' => $pay_status,
            'details_number' => $details_number,
        );

        // 如果存在回单，删除原文件
        $old_res = D('Wmwithdrawals')->get_one_withdrawal_by_serialnumber($serial_number);
        if($old_res['huidan']) {
            $old_huidan = $old_res['huidan'];
            $file = '../ad.mazhoudao.net/' . substr($old_huidan,23);
            if (!unlink($file)) {
                $message = '原文件删除失败';
            }
        }

        $res = D('Wmwithdrawals')->update_fapiao_status($serial_number,$data);
        if(!$res) {
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => '提现状态修改失败！',
            ));
        }
        $this->ajaxReturn(array(
            'status' => 1,
            'message' => '提现状态修改成功！',
        ));
    }

}