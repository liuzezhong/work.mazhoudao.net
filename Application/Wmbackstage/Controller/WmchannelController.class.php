<?php
/**
 * FILE_NAME :  WmchannelController.class.php
 * 模块:Wmbackstage
 * 域名:union.yanqingkong.com
 *
 * 功能:用户业绩信息控制器
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
 * 功能1：用户业绩信息数据显示
 * 功能2：用户的下线用户管理
 * 功能3：每天渠道业绩录入
 * 功能4：检查并保存每天渠道业绩录入数据
 * 功能5：每月下线业绩录入
 * 功能6：导出用户业绩信息至EXCEL
 *
 * @author      liuzezhong <liuzezhong@hongshu.com>
 * @access      public
 * @abstract
 */
class WmchannelController extends CommonController {


    /**
     * 功能：用户业绩信息数据显示
     */
    public function index() {
        $data = array();
        try {
            //1.1 如果有数据则获取GET数据
            if(I('get.userid',0,'intval')) {
                $data['user_id'] = I('get.userid',0,'intval');
                $this->assign('input_userid',$data['user_id']);
            }
            if(I('get.cnumber','','trim,string')) {
                $data['cnumber'] = I('get.cnumber','','trim,string');
                $this->assign('input_cnumber',$data['cnumber']);
                $user_info = D('Wmadmin')->get_one_user_by_cnumber($data['cnumber']);
                $data['user_id'] = $user_info['user_id'];
            }
            if(I('get.q_date','','trim,string')) {
                $data['q_date'] = I('get.q_date','','trim,string');
                $this->assign('input_date',$data['q_date']);
                $data['begin_date'] = strtotime($data['q_date']);
                $data['end_date'] = $data['begin_date'] + 86399;
            }
            if(I('get.status',-2,'intval')) {
                $data['status'] = I('get.status',-2,'intval');
                if($data['status'] == 2) {
                    $this->assign(array(
                        'status' => 2,
                        'status_zh' => '未提现'
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
            $achievement = D("Wmachievement")->get_all_achievement_limit( $data,$page,$pageSize);
            $achievementCount = D("Wmachievement")->get_count_achievement( $data);
            //1.5 实例化一个分页对象
            $res = new Wmpage($achievementCount,$pageSize);
            //1.6 调用show方法前台显示页码
            $pageRes = $res->show();
            //1.7 处理数据
            foreach ($achievement as $k => $item) {
                $c_number = D('Wmadmin')->get_one_user_by_id($item['user_id']);
                $achievement[$k]['c_number'] = $c_number['c_number'];   //写入渠道号
                $achievement[$k]['username'] = $c_number['username'];   //写入用户名
                $achievement[$k]['success_p'] = sprintf("%.2f", $item['recharge_s']/$item['recharge']*100) . '%';
                if($item['pay_status'] == 0) {
                    $achievement[$k]['status_zh'] = '未提现';
                }else {
                    $achievement[$k]['status_zh'] = '已提现';
                }
            }
            //1.8 将数据传递前台模板
            $this->assign(array(
                'achievementCount' => $achievementCount,
                'achievements' => $achievement,
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
     * 功能：每天渠道业绩录入
     */
    public function entry_paymentdata(){
        $data = array();
        try {
            //1.1 如果有数据则获取GET数据
            if(I('get.q_date','','trim,string')) {
                $data['q_date'] = I('get.q_date','','trim,string');
                $this->assign('input_date',$data['q_date']);
                $data['begin_date'] = strtotime($data['q_date']);
                $data['end_date'] = $data['begin_date'] + 86399;
                $channel_array = $this->get_channel($data['q_date']);
                $c_numbers = array_column($channel_array,'qudao_id');
                $all_users = D('Wmadmin')->get_all_useradmin();
                foreach($all_users as $k => $item) {
                    if(in_array($item['c_number'],$c_numbers)) {
                        foreach ($channel_array as $m => $n) {
                            if($n['qudao_id'] ==  $item['c_number']) {
                                $channels[$k]['user_id'] = $item['user_id'];  //根据渠道号获取用户ID
                                $channels[$k]['username'] = $item['username'];  //用户名称
                                $channels[$k]['public_name'] = $item['public_name'];  //公众号
                                $channels[$k]['c_number'] = $item['c_number'];   //渠道号
                                $channels[$k]['register'] = $n['ref_users'];  // 注册用户数
                                $channels[$k]['recharge'] = $n['ref_paylogs'];   //产生订单
                                $channels[$k]['recharge_s'] = $n['ref_okpaylogs'];  //产生的成功订单数
                                $channels[$k]['commission'] = $n['ref_okmoney'];    //产生的成功订单金额
                                $channels[$k]['re_time'] = $data['begin_date'];     //日期
                                $channels[$k]['divided_amount'] = $n['ref_okmoney'] * $item['proportion'] ;  //分成金额
                                $channels[$k]['acc_explain'] = $data['q_date'] . '渠道业绩';
                                $channels[$k]['underline_number'] = 0;
                                $channels[$k]['underline_money'] = 0;
                            }
                        }
                    } else {
                        $channels[$k]['user_id'] = $item['user_id'];  //根据渠道号获取用户ID
                        $channels[$k]['username'] = $item['username'];  //用户名称
                        $channels[$k]['public_name'] = $item['public_name'];  //公众号
                        $channels[$k]['c_number'] = $item['c_number'];   //渠道号
                        $channels[$k]['register'] = 0;  // 注册用户数
                        $channels[$k]['recharge'] = 0;   //产生订单
                        $channels[$k]['recharge_s'] = 0;  //产生的成功订单数
                        $channels[$k]['commission'] = 0;    //产生的成功订单金额
                        $channels[$k]['re_time'] = $data['begin_date'];     //日期
                        $channels[$k]['divided_amount'] = 0 ;  //分成金额
                        $channels[$k]['acc_explain'] = $data['q_date'] . '渠道业绩';
                        $channels[$k]['underline_number'] = 0;
                        $channels[$k]['underline_money'] = 0;
                    }

                }
                $this->assign(array(
                    'achievements' => $channels,
                ));
            }

        } catch (Exception $e) {
            $this->assign(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
        $this->display();
    }

    /**
     * 功能：检查并保存每月渠道业绩录入数据
     */
    public function entry_paymentdata_check() {
        try {
            $data = I('post.');

            foreach ($data as $k => $item) {
                foreach ($item as $p => $m) {
                    $saveData[$m['name']] = $m['value'];
                }
                // 将时间转换为时间戳
                $saveData['re_time'] = strtotime($saveData['re_time']);

                if($saveData['ach_id'] == 1) {
                    $saveData['proportion'] = 1;   // 渠道业绩
                }else if($saveData['ach_id'] == 2) {
                    $saveData['proportion'] = 2;   // 下线业绩
                }
                unset($saveData['ach_id']);
                // 根据用户ID和日期查找业绩信息是否存在
                $cz_res = D('Wmachievement')->get_one_achievement_by_data($saveData['user_id'],$saveData['re_time']);
                if($cz_res) {
                    //如果存在的话，更新数据
                    $user_id_data = $saveData['user_id'];
                    $re_time_data = $saveData['re_time'];
                    unset($saveData['user_id']);
                    unset($saveData['re_time']);
                    $res[$k] = D('Wmachievement')->update_one_achievement($user_id_data,$re_time_data,$saveData);
                } else {
                    //如果不存在的话，保存数据
                    $res[$k] = D('Wmachievement')->add_one_achievement($saveData);
                }

            }
            if(!$res) {
                $this->ajaxReturn(array(
                    'status' => 0,
                    'message' => '操作失败！',
                ));
            }
            $this->ajaxReturn(array(
                'status' => 1,
                'message' => '保存成功！',
            ));
        } catch (Exception $e) {
            $this->ajaxReturn(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }

    }

    /**
     * 功能:每月下线业绩录入
     */
    public function entry_underlinedata(){
        $data = array();
        try {
            //1.1 如果有数据则获取GET数据
            if(I('get.q_date','','trim,string')) {
                $data['q_date'] = I('get.q_date','','trim,string');
                $this->assign('input_date',$data['q_date']);
                $data['begin_date'] = strtotime($data['q_date']);
                $begin_date = $data['begin_date'];
                $input_begain = date("Y-m-d H:i:s",$begin_date);   //用户输入月份开始时间日期格式
                $input_end_time = strtotime("$input_begain +1 month");   //用户输入月份结束时间unix时间戳格式
                $now_time = time();
                $xxyj = C('XXYJ_MONTH');  //时间范围

                // 如果选择的月份超过本月及以后的月份，不执行程序，返回错误信息
                if(substr($data['q_date'],-2) >= date('m',$now_time)) {
                    $this->assign(array(
                        'status' => 1,
                    ));
                } else {
                    //1.1 求出所有有下线的用户
                    $have_underline_users = D('Wmadmin')->get_all_have_underline_users_by_time($input_end_time);
                    //1.2 获得有下线用户的用户ID
                    $underline_users_userid = '';
                    foreach ($have_underline_users as $k => $i) {
                        $underline_users_userid = $underline_users_userid . $i['user_id'] . ',';
                    }
                    //1.3 删除字符串末端的空白字符
                    $underline_users_userid = rtrim($underline_users_userid, ',');

                    if(!empty($underline_users_userid)){

                        $Model = M();
                        $underline_users_achievement = $Model->query('SELECT u.user_id, introducer_id, divided_amount
                    FROM  `wis_wms_user` AS u
                    LEFT JOIN wis_wms_achievement AS y ON u.user_id = y.user_id
                    WHERE u.introducer_id
                    IN (' . $underline_users_userid . ')
                        AND u.register_time > UNIX_TIMESTAMP(DATE_SUB(FROM_UNIXTIME(' . $input_end_time . '),INTERVAL ' . $xxyj . '. MONTH))
    
                    ');

                        //1.5 数据处理
                        foreach ($have_underline_users as $m => $n) {
                            $money = 0;
                            $count = 0;
                            foreach ($underline_users_achievement as $p => $q) {
                                if ($n['user_id'] == $q['introducer_id']) {
                                    $money = $money + $q['divided_amount'];
                                    $count ++ ;
                                }
                            }
                            $have_underline_users[$m]['underline_number'] = $count;
                            $have_underline_users[$m]['underline_money'] = $money;
                            $have_underline_users[$m]['divided_amount'] = $money * 0.8;
                            $have_underline_users[$m]['acc_explain'] = substr($data['q_date'], -2) . '月下线业绩';
                        }

                    }
                    //1.4 数据库查询

                    $useful_user = array_column($have_underline_users,'user_id');
                    $all_users = D('Wmadmin')->get_all_user();

                    foreach ($all_users as $kk => $ii) {
                        if(in_array($ii['user_id'],$useful_user)) {
                            foreach ($have_underline_users as $kkk => $iii) {
                                if($iii['user_id'] == $ii['user_id']) {
                                    $all_users[$kk]['underline_number'] = $iii['underline_number'];
                                    $all_users[$kk]['underline_money'] = $iii['underline_money'];
                                    $all_users[$kk]['divided_amount'] = $iii['divided_amount'];
                                    $all_users[$kk]['acc_explain'] = $iii['acc_explain'];
                                }
                            }
                        } else {
                            $all_users[$kk]['underline_number'] = 0;
                            $all_users[$kk]['underline_money'] = 0;
                            $all_users[$kk]['divided_amount'] = 0;
                            $all_users[$kk]['acc_explain'] = substr($data['q_date'], -2) . '月下线业绩';
                        }
                    }
                    $this->assign(array(
                        'achievements' => $all_users,
                    ));
                }
            }


        } catch (Exception $e) {
            $this->assign(array(
                'status' => 0,
                'message' => $e->getMessage(),
            ));
        }
        $this->display();
    }

    /**
     * 功能：导出用户业绩信息至EXCEL
     */
    public function wmexp_channel(){
        header('Content-Type: text/xls');
        header("Content-type:application/vnd.ms-excel;charset=utf-8");
        header("Content-Disposition: attachment; filename=". date("YmdHis", NOW_TIME) . ".xls");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header("Pragma: no-cache");
        $data = array();
        //1.1 如果有数据则获取GET数据
        if(I('get.userid',0,'intval')) {
            $data['user_id'] = I('get.userid',0,'intval');
        }
        if(I('get.cnumber','','trim,string')) {
            $data['cnumber'] = I('get.cnumber','','trim,string');
            $user_info = D('Wmadmin')->get_one_user_by_cnumber($data['cnumber']);
            $data['user_id'] = $user_info['user_id'];
        }
        if(I('get.q_date','','trim,string')) {
            $data['q_date'] = I('get.q_date','','trim,string');
            $data['begin_date'] = strtotime($data['q_date']);
            $data['end_date'] = $data['begin_date'] + 86399;
        }
        try {
            //1.3.1 获取所有用户业绩信息
            $xlsData = D('Wmachievement')->get_all_achievement_by_data($data);
            //1.3.2 数据处理
            $new_xlsData = array();
            foreach ($xlsData as $k => $item) {
                $c_number = D('Wmadmin')->get_one_user_by_id($item['user_id']);
                $new_xlsData[$k][0] = date('Y-m-d', $item['re_time']);
                $new_xlsData[$k][1] = $item['ach_id'];
                $new_xlsData[$k][2] = $item['user_id'];
                $new_xlsData[$k][3] = $c_number['username'];
                $new_xlsData[$k][4] = $c_number['c_number'];
                $new_xlsData[$k][5] = $item['underline_number'];
                $new_xlsData[$k][6] = $item['register'];
                $new_xlsData[$k][7] = $item['recharge'];
                $new_xlsData[$k][8] = $item['recharge_s'];
                $new_xlsData[$k][9] = sprintf("%.2f", $item['recharge_s']/$item['recharge']*100) . '%';
                $new_xlsData[$k][10] = $item['commission'];
                $new_xlsData[$k][11] = $item['divided_amount'];
                if($item['pay_status'] == 1) {
                    $new_xlsData[$k][12] = '已提现';
                }else {
                    $new_xlsData[$k][12] = '未提现';
                }
                $new_xlsData[$k][13] = $item['acc_explain'];
            }
            //1.3.3 导出数据
            echo '<table border=1>';
            echo '<tr>';
            echo '<th>日期</th>';
            echo '<th>业绩ID号</th>';
            echo '<th>用户ID号</th>';
            echo '<th>用户名</th>';
            echo '<th>渠道号</th>';
            echo '<th>发展下线人数</th>';
            echo '<th>渠道注册人数</th>';
            echo '<th>充值订单数</th>';
            echo '<th>有效订单数</th>';
            echo '<th>订单成功率</th>';
            echo '<th>用户充值金额</th>';
            echo '<th>渠道分成金额</th>';
            echo '<th>提现状态</th>';
            echo '<th>业绩说明</th>';
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

    /**
     * 功能：根据日期调用接口获取当日渠道销售数据
     * @param $date
     * @return mixed
     */
    function get_channel($date)
    {
        $unionid = C('unionid');   //联盟标识
        $postUrl = C('postUrl');       //post地址
        $secret_key = C('secret_key');   //传递过来的密钥
        try {
            //1.1 封装数据
            $postData = array(
                'unionid' => $unionid,
                'func' => 'getpayloginfo',   //函数名称
                'timestamp' => time(),   //请求发起的时间戳
                'date' => $date,         //日期 如:’2017-01-01’
            );
            //1.2 根据键对数组进行升序排序
            ksort($postData);
            //1.3 根据方法计算公钥
            $key = '';
            foreach ($postData as $k => $item) {
                $key = $key . $k . '=' . $item . '&';
            }
            $key = $key . $secret_key;
            $postData['sign'] = strtolower(md5($key));  //公钥
            //1.4 发送POST请求
            $res = curlData($postUrl, $postData);
            //1.5 将数据进行JSON解析
            $result = json_decode($res, true);
            //1.6 判断结果，0为正常输出,1为失败
            if ($result['status'] == 0) {
                //1.6.1 获取数据
                $channel_array = $result['result'];
                //1.6.2 返回数据
                return $channel_array;

            } else if ($result['status'] == 1) {
                // 1.6.3 POST请求失败抛出失败原因
                throw_exception('渠道号获取失败，失败原因：' . $result['message']);
            }
        } catch (Exception $e) {
            throw_exception($e->getMessage());
        }
    }
}