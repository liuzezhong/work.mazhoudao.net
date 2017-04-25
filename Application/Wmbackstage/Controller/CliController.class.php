<?php
/**
 * Created by PhpStorm.
 * User: yuban
 * Date: 2017/4/14
 * Time: 15:36
 */

namespace Wmbackstage\Controller;


class CliController{
    public function daoru_yeji() {

        if (php_sapi_name() != 'cli') {
            exit("error");
        }

        $rdate = I('get.rdate','','trim,string');
        if(!$rdate) {
            // 如果没有则默认是当天
            $rdate = date('Y-m-d',time());
        } else if($rdate == 'prev') {
            // 如果参数是prev则为前一天
            $rdate = date("Y-m-d",strtotime("-1 day"));
        }
        // 通过接口取得渠道业绩
        $channel_array = $this->get_channel($rdate);
        // 所有有业绩的渠道ID
        $c_numbers = array_column($channel_array,'qudao_id');
        // 取得所有用户
        $all_users = D('Wmadmin')->get_all_useradmin();
        // 输出用户渠道业绩
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
                        $channels[$k]['re_time'] = $rdate;     //日期
                        $channels[$k]['divided_amount'] = $n['ref_okmoney'] * $item['proportion'] ;  //分成金额
                        $channels[$k]['acc_explain'] = $rdate . '渠道业绩';
                        $channels[$k]['underline_number'] = 0;
                        $channels[$k]['underline_money'] = 0;
                        print $item['username'] .',' .'分成金额：'. $n['ref_okmoney'] * $item['proportion'] . '元,' . $rdate;
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
                $channels[$k]['re_time'] = $rdate;     //日期
                $channels[$k]['divided_amount'] = 0 ;  //分成金额
                $channels[$k]['acc_explain'] = $rdate . '渠道业绩';
                $channels[$k]['underline_number'] = 0;
                $channels[$k]['underline_money'] = 0;
                print $item['username'] .',' . 0 . $rdate;
            }

            $channels[$k]['re_time'] = strtotime($channels[$k]['re_time']);
            $channels[$k]['proportion'] = 1;
            $cz_res = D('Wmachievement')->get_one_achievement_by_data($channels[$k]['user_id'],$channels[$k]['re_time']);
            if($cz_res) {
                //如果存在的话，更新数据
                $user_id_data = $channels[$k]['user_id'];
                $re_time_data = $channels[$k]['re_time'];
                unset($channels[$k]['user_id']);
                unset($channels[$k]['re_time']);
                $res[$k] = D('Wmachievement')->update_one_achievement($user_id_data,$re_time_data,$channels[$k]);
                if($res)
                    print ' 渠道业绩数据更新成功！' . "\n";
                else if(!$res)
                    print ' 渠道业绩数据更新失败！' . "\n";
            } else {
                //如果不存在的话，保存数据
                $res[$k] = D('Wmachievement')->add_one_achievement($channels[$k]);
                if($res)
                    print ' 渠道业绩数据新增成功！' . "\n";
                else if(!$res)
                    print ' 渠道业绩数据新增失败！' . "\n";
            }

        }
        $time = date('Y-m-d H:i:s',time()) ;
        print "\n\n" . $time . '渠道业绩数据汇总成功！' . "\n\n";
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