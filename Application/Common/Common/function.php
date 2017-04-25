<?php
/**
 * FILE_NAME: function.php
 * 模块: [请修改此处]
 * 域名: work.hongshu.com
 *
 * 功能: [请修改此处]
 *
 * @copyright Copyright (c) 2016 – www.hongshu.com
 * @author 果农
 * @version $Id: function.php 518 2017-01-24 07:44:48Z guonong $
 */
/**
 * php 7.0中不再支持set_magic_quotes_runtime函数
 */
if (!function_exists('set_magic_quotes_runtime')) {

    function set_magic_quotes_runtime($new_setting) {
        @ini_set("magic_quotes_runtime", $new_setting);
    }

}
	/**
	 * 获得相对btime的上月,本月,今天,昨天...的起始时间star_time和结束时间end_time
	 *
	 * @param unknown_type $btime
	 * @param unknown_type $stype=lastmonth|thismonth|lastday|thisday|lastweek|thisweek
	 * @param unknown_type $star_time
	 * @param unknown_type $end_time
	 */
	function mk_time_xiangdui($btime,$stype='lastmonth',&$star_time,&$end_time){
		if(empty($btime)){
    			$btime=time();
    		}
    		switch($stype){
    			case 'lastmonth':

    				//$btime=mktime(0,0,0,date('m',$btime)-1,date('d',$btime),date("Y",$btime));
    				$star_time=mktime(0,0,0,date('m',$btime)-1,1,date("Y",$btime));
    				$end_time=mktime(23,59,59,date('m',$btime),00,date("Y",$btime));

    				break;
    			case 'thismonth':
    				$star_time=mktime(0, 0 , 0,date("m",$btime),1,date("Y",$btime));
    				//$endtime=mktime(23,59,59,date("m",$btime),date("t",$btime),date("Y",$btime));
    				$end_time=mktime(23, 59, 59, date('m', $btime)+1, 00,date("Y",$btime));
    				//$star_time=mktime(0, 0, 0, date('m',$btime),   '1',  date('Y',$btime));
    				//$end_time=mktime(0, 0, 0, date('m',$btime),   '31',  date('Y',$btime));


    				break;
    			case 'lastday':

    				$star_time=mktime(0, 0, 0, date('m',$btime),  date('d',$btime)-1,  date('Y',$btime));
    				$end_time=mktime(24, 0, 0, date('m',$btime),  date('d',$btime)-1,  date('Y',$btime));
    				break;
    			case 'thisday':

    				$star_time=mktime(0, 0, 0, date('m',$btime),  date('d',$btime),  date('Y',$btime));
    				$end_time=mktime(24, 0, 0, date('m',$btime),  date('d',$btime),  date('Y',$btime));

    				break;
    			case 'thisweek':

    				$star_time=mktime(0, 0 , 0,date('m',$btime),date('d',$btime)-date("w",$btime)+1,date("Y",$btime));
    				$end_time=mktime(23,59,59,date('m',$btime),date('d',$btime)-date("w",$btime)+7,date("Y",$btime));

    				break;
    			case 'lastweek':

    				$star_time=mktime(0, 0 , 0,date('m',$btime),date('d',$btime)-date("w",$btime)+1-7,date("Y",$btime));
    				$end_time=mktime(23,59,59,date('m',$btime),date('d',$btime)-date("w",$btime)+7-7,date("Y",$btime));
    				break;
    		}

    	}

/**
 * XSS
 */
function removeXSS($val) {
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i ++) {
        $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search [$i])) . ';?)/i', $search [$i], $val);
        $val = preg_replace('/(�{0,8}' . ord($search [$i]) . ';?)/', $search [$i], $val);
    }
    $ra1 = array(
        'javascript',
        'vbscript',
        'expression',
        'applet',
        'meta',
        'xml',
        'blink',
        'link',
        'style',
        'script',
        'embed',
        'object',
        'iframe',
        'frame',
        'frameset',
        'ilayer',
        'layer',
        'bgsound',
        'title',
        'base'
    );
    $ra2 = array(
        'onabort',
        'onactivate',
        'onafterprint',
        'onafterupdate',
        'onbeforeactivate',
        'onbeforecopy',
        'onbeforecut',
        'onbeforedeactivate',
        'onbeforeeditfocus',
        'onbeforepaste',
        'onbeforeprint',
        'onbeforeunload',
        'onbeforeupdate',
        'onblur',
        'onbounce',
        'oncellchange',
        'onchange',
        'onclick',
        'oncontextmenu',
        'oncontrolselect',
        'oncopy',
        'oncut',
        'ondataavailable',
        'ondatasetchanged',
        'ondatasetcomplete',
        'ondblclick',
        'ondeactivate',
        'ondrag',
        'ondragend',
        'ondragenter',
        'ondragleave',
        'ondragover',
        'ondragstart',
        'ondrop',
        'onerror',
        'onerrorupdate',
        'onfilterchange',
        'onfinish',
        'onfocus',
        'onfocusin',
        'onfocusout',
        'onhelp',
        'onkeydown',
        'onkeypress',
        'onkeyup',
        'onlayoutcomplete',
        'onload',
        'onlosecapture',
        'onmousedown',
        'onmouseenter',
        'onmouseleave',
        'onmousemove',
        'onmouseout',
        'onmouseover',
        'onmouseup',
        'onmousewheel',
        'onmove',
        'onmoveend',
        'onmovestart',
        'onpaste',
        'onpropertychange',
        'onreadystatechange',
        'onreset',
        'onresize',
        'onresizeend',
        'onresizestart',
        'onrowenter',
        'onrowexit',
        'onrowsdelete',
        'onrowsinserted',
        'onscroll',
        'onselect',
        'onselectionchange',
        'onselectstart',
        'onstart',
        'onstop',
        'onsubmit',
        'onunload'
    );
    $ra = array_merge($ra1, $ra2);
    $found = true;
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i ++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra [$i]); $j ++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(�{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra [$i] [$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra [$i], 0, 2) . '<x>' . substr($ra [$i], 2);
            $val = preg_replace($pattern, $replacement, $val);
            if ($val_before == $val) {
                $found = false;
            }
        }
    }
    return $val;
}

/**
 * 防注入
 */
function abacaAddslashes($var) {
    if (!get_magic_quotes_gpc()) {
        if (is_array($var)) {
            foreach ($var as $key => $val) {
                $var [$key] = abacaAddslashes($val);
            }
        } else {
            $var = addslashes($var);
        }
    }
    return $var;
}

/**
 * 生成随机字符串
 * 添加$addStr，可以自己指定字符串以适合文章内容添加随机水印
 */
function randomstr($length, $addStr = '') {
    $hash = '';
    $chars = '123456789abcdefghijklmnopqrstuvwxyz' . $addStr;
    $max = strlen($chars) - 1;
    // mt_srand((double)microtime() * 1000000);
    for ($i = 0; $i < $length; $i ++) {
        $hash .= $chars [mt_rand(0, $max)];
    }
    return $hash;
}

/**
 * 检测用户是否登录
 *
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login() {
    $uid = max(0, intval(session('uid')));
    return $uid;
}

/**
 * 数据签名认证
 *
 * @param array $data
 *        	被认证的数据
 * @return string 签名
 */
function data_auth_sign($data) {
    // 数据类型检测
    if (!is_array($data)) {
        $data = (array) $data;
    }
    ksort($data); // 排序
    $code = http_build_query($data); // url编码并生成query字符串
    $sign = sha1($code); // 生成签名
    return $sign;
}

/**
 * 友好的时间显示
 *
 * @param int $sTime
 *        	待显示的时间
 * @param string $type
 *        	类型. normal | mohu | full | ymd | other
 * @param string $alt
 *        	已失效
 * @return string
 */
function friendly_date($sTime, $type = 'normal', $alt = 'false') {
    if (!$sTime)
        return '';
    // sTime=源时间，cTime=当前时间，dTime=时间差
    if (strlen(intval($sTime)) != strlen($sTime)) {
        $sTime = strtotime($sTime);
    }
    $cTime = time();
    $dTime = $cTime - $sTime;
    $dDay = intval(date("z", $cTime)) - intval(date("z", $sTime));

    $dYear = intval(date("Y", $cTime)) - intval(date("Y", $sTime));
    // normal：n秒前，n分钟前，n小时前，日期
    if ($type == 'normal') {
        if ($dTime < 60) {
            if ($dTime < 10) {
                return '刚刚';
            } else {
                return intval(floor($dTime / 10) * 10) . "秒前";
            }
        } elseif ($dTime < 3600) {
            return intval($dTime / 60) . "分钟前";
        } elseif ($dYear == 0 && $dDay == 0) {
            return '今天' . date('H:i', $sTime);
        } elseif ($dYear == 0) {
            return date("m月d日 H:i", $sTime);
        } else {
            return date("Y-m-d H:i", $sTime);
        }
    } elseif ($type == 'mohu') {
        if ($dTime < 60) {
            return $dTime . "秒前";
        } elseif ($dTime < 3600) {
            return intval($dTime / 60) . "分钟前";
        } elseif ($dTime >= 3600 && $dDay == 0) {
            return intval($dTime / 3600) . "小时前";
        } elseif ($dDay > 0 && $dDay <= 7) {
            return intval($dDay) . "天前";
        } elseif ($dDay > 7 && $dDay <= 30) {
            return intval($dDay / 7) . '周前';
        } elseif ($dDay > 30) {
            return intval($dDay / 30) . '个月前';
        }
    } elseif ($type == 'full') {
        return date("Y-m-d , H:i:s", $sTime);
    } elseif ($type == 'ymd') {
        return date("Y-m-d", $sTime);
    } elseif ($type == 'md') {
        return date("m-d", $sTime);
    } else {
        if ($dTime < 60) {
            return $dTime . "秒前";
        } elseif ($dTime < 3600) {
            return intval($dTime / 60) . "分钟前";
        } elseif ($dTime >= 3600 && $dDay == 0) {
            return intval($dTime / 3600) . "小时前";
        } elseif ($dYear == 0) {
            return date("Y-m-d H:i:s", $sTime);
        } else {
            return date("Y-m-d H:i:s", $sTime);
        }
    }
}

/**
 * 检测验证码
 *
 * @param integer $id
 *        	验证码ID
 * @return boolean 检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1) {
    $verify = new \HS\Verify ();
    return $verify->check($code, $id);
}

/**
 * 编码/解码
 *
 * @param string $string
 *        	要编码/解码的字符串
 * @param string $operation
 *        	操作类型（DECODE/ENCODE）
 * @param string $key
 *        	密码串
 * @param number $expiry
 *        	有效期
 * @return string
 */
function uc_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    $ckey_length = 4; // note 随机密钥长度 取值 0-32;
    // note 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
    // note 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
    // note 当此值为 0 时，则不产生随机密钥

    $key = md5($key); // ? $key : UC_KEY
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), - $ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i ++) {
        $rndkey [$i] = ord($cryptkey [$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i ++) {
        $j = ($j + $box [$i] + $rndkey [$i]) % 256;
        $tmp = $box [$i];
        $box [$i] = $box [$j];
        $box [$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i ++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box [$a]) % 256;
        $tmp = $box [$a];
        $box [$a] = $box [$j];
        $box [$j] = $tmp;
        $result .= chr(ord($string [$i]) ^ ($box [($box [$a] + $box [$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

/**
 * 文件上传
 *
 * @param array $files
 *        	要上传的文件列表（通常是$_FILES数组）
 * @param array $setting
 *        	文件上传配置
 * @param string $driver
 *        	上传驱动名称
 * @param array $config
 *        	上传驱动配置
 * @return array 文件上传成功后的信息
 */
function upload($files, $setting, $driver = 'Local') {
    /* 上传文件 */
    $config = C("UPLOAD_{$driver}_CONFIG");
    $Uploader = new Think\Upload($setting, $driver, $config);
    $info = $Uploader->upload($files);
    if ($info) { // 文件上传成功，记录文件信息
        foreach ($info as $key => &$value) {
            /* 已经存在文件记录 */
            if (isset($value ['id']) && is_numeric($value ['id'])) {
                continue;
            }
            /* 记录文件信息 */
            $value ['path'] = $value ['savepath'] . $value ['savename']; // 在模板里的url路径
        }
        return $info; // 文件上传成功
    } else {
        $info = array(
            'status' => 0,
            'error'  => $Uploader->getError()
        );
        return $info;
    }
}

/**
 * 格式化输出信息
 *
 * @param 字符串/数组 $array
 *        	要输出的信息
 * @param 逻辑值 $exit
 *        	是否需要退出
 */
function pre($array, $exit = false) {
    if ($array) {
        if (is_string($array)) {
            echo '<br>';
            echo htmlspecialchars($array);
            echo '<br>';
        } else {
            echo "<div style='font-size:12px;line-height:14px;text-align:left;color:#000;background-color:#fff;'><pre>";
            print_r($array);
            echo "</pre></div>";
        }
    }
    if ($exit) {
        E('程序调试断点！', 222);
    }
}

function checkPriv($needpriv, $usrInfo = array()) {
    if (!$usrInfo) {
        $usrInfo = session();
    }
    $priv = D("Pri");

    if ($usrInfo['priv'] == '') {
        return false;
    }

    $PRIV = cached_priv();

    if (!isset($PRIV[$needpriv])) {
        // echo 22;
        return false;
    }
    foreach ($PRIV[$needpriv] as $k => $v) {
        // echo $v;
        // echo "<br />";
        if ($v && $priv->allowedPopedom($usrInfo['priv'], $v)) {
            // echo 7;
            return true;
        }
    }

    return false;
}

/**
 * 获取缓存的key，与cachemap进行映射
 */
function getCacheKey($name) {
    if (C('cache_prefix') && stristr($name, C('cache_prefix'))) {
        return $name;
    }
    $key = C('mcconfig.prefix') . $name;
    $arr = array();
    if (strpos($name, '#') > 0) {
        $arr = C('cachemap.' . substr($name, 0, strpos($name, '#') + 1));
    } else {
        $arr = C('cachemap.' . $name);
    }
    if (is_array($arr)) {
        $openmap = $arr[1];
        if ($openmap == 1) {
            if (strpos($name, '#') > 0) {
                $key = $arr[0] . substr($name, strpos($name, '#') + 1);
            } else {
                $key = $arr[0];
            }
        } else {
            //原始key
            $key = C('cache_prefix') . $name;
        }
    } else {
        //原始key
        $key = C('cache_prefix') . $name;
    }
    return $key;
}

/**
 * 从memcached获得Priv缓存，如不存在则从数据库中读入权限组并返回
 *
 * @param unknown_type $flush
 * @return unknown
 */
function cached_priv($flush = false) {
    $cacheid_key = "txtxiaoshuo" . '_privinfo';
    if (class_exists('redis')) {
        $redis = new \Think\Cache\Driver\Redis();
        $PRIV = $redis->get($cacheid_key);

        if ($flush || !is_array($PRIV)) {

            unset($PRIV);
            $redis = new \Think\Cache\Driver\Redis();
            $redis->rm("txtxiaoshuo" . '_PRIV');
            // $M_mmtt->del($CONFIG['prefix'].'_PRIV');
            $tmparray = M('popedom_info')->select();
            // $tmparray=$M_db->fetch_arrays("SELECT * FROM wis_popedom_info");

            foreach ($tmparray as $k => $v) {
                $PRIV[$v['popedomname']][] = $v['popedomcode'];
            }

            $redis->set($cacheid_key, $PRIV);
        }
    } else {
        $PRIV = S($cacheid_key);
        if (!$PRIV) {
            $PRIV = array();
            $tmparray = M('popedom_info')->select();
            foreach ($tmparray as $k => $v) {
                $PRIV[$v['popedomname']][] = $v['popedomcode'];
            }
            S($cacheid_key, $PRIV);
        }
    }
    return $PRIV;
}

/**
 * This file is part of the array_column library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey (http://benramsey.com)
 * @license http://opensource.org/licenses/MIT MIT
 */
if (!function_exists('array_column')) {

    /**
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values.
     * @param mixed $columnKey The column of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     *                        the returned array. This value may be the integer key
     *                        of the column, or it may be the string key name.
     * @return array
     */
    function array_column($input = null, $columnKey = null, $indexKey = null) {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();
        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }
        if (!is_array($params[0])) {
            trigger_error(
                    'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given', E_USER_WARNING
            );
            return null;
        }
        if (!is_int($params[1]) && !is_float($params[1]) && !is_string($params[1]) && $params[1] !== null && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        if (isset($params[2]) && !is_int($params[2]) && !is_float($params[2]) && !is_string($params[2]) && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;
        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }
        $resultArray = array();
        foreach ($paramsInput as $row) {
            $key = $value = null;
            $keySet = $valueSet = false;
            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }
            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }
            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }
        }
        return $resultArray;
    }

}

/**
 * 发送POST请求
 */
function curlData($url, $data, $cookie = '') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if (stripos($url, 'https://') !== false) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $header = array();
    if ($cookie) {
        if (is_array($cookie)) {
            $tmp = $cookie;
            $cookie = '';
            foreach ($tmp as $k => $v) {
                $cookie .= $k . '=' . $v . '; ';
            }
        }
        $header[] = 'Cookie: ' . $cookie;
    }
    if ($header) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($ch);

    curl_close($ch);
    return $response;
}

/**
 * 定义了一系列用于简化数组操作的函数
 *
 * @copyright Copyright (c) 2005 - 2006 FleaPHP.org (www.fleaphp.org)
 * @author 廖宇雷 dualface@gmail.com
 * @package Core
 * @version $Id: function.php 518 2017-01-24 07:44:48Z guonong $
 */

/**
 * 从数组中删除空白的元素（包括只有空白字符的元素）
 *
 * @param array $arr
 * @param boolean $trim
 */
function array_remove_empty(& $arr, $trim = true) {
    foreach ($arr as $key => $value) {
        if (is_array($value)) {
            array_remove_empty($arr[$key]);
        } else {
            $value = trim($value);
            if ($value == '') {
                unset($arr[$key]);
            } elseif ($trim) {
                $arr[$key] = $value;
            }
        }
    }
}

/**
 * 从一个二维数组中返回指定键的所有值
 *
 * @param array $arr
 * @param string $col
 *
 * @return array
 */
function array_col_values(& $arr, $col) {
    $ret = array();
    foreach ($arr as $row) {
        if (isset($row[$col])) {
            $ret[] = $row[$col];
        }
    }
    return $ret;
}

/**
 * 将一个二维数组按照指定字段的值分组
 *
 * @param array $arr
 * @param string $keyField
 *
 * @return array
 */
function array_group_by(& $arr, $keyField) {
    $ret = array();
    foreach ($arr as $row) {
        $key = $row[$keyField];
        $ret[$key][] = $row;
    }
    return $ret;
}

/**
 * 将一个平面的二维数组按照指定的字段转换为树状结构
 *
 * 当 $returnReferences 参数为 true 时，返回结果的 tree 字段为树，refs 字段则为节点引用。
 * 利用返回的节点引用，可以很方便的获取包含以任意节点为根的子树。
 *
 * @param array $arr 原始数据
 * @param string $fid 节点ID字段名
 * @param string $fparent 节点父ID字段名
 * @param string $fchildrens 保存子节点的字段名
 * @param boolean $returnReferences 是否在返回结果中包含节点引用
 *
 * return array
 */
function array_to_tree($arr, $fid, $fparent = 'parent_id', $fchildrens = 'childrens', $returnReferences = false) {
    $pkvRefs = array();
    foreach ($arr as $offset => $row) {
        $pkvRefs[$row[$fid]] = & $arr[$offset];
    }

    $tree = array();
    foreach ($arr as $offset => $row) {
        $parentId = $row[$fparent];
        if ($parentId) {
            if (!isset($pkvRefs[$parentId])) {
                continue;
            }
            $parent = & $pkvRefs[$parentId];
            $parent[$fchildrens][] = & $arr[$offset];
        } else {
            $tree[] = & $arr[$offset];
        }
    }
    if ($returnReferences) {
        return array('tree' => $tree, 'refs' => $pkvRefs);
    } else {
        return $tree;
    }
}

/**
 * 将树转换为平面的数组
 *
 * @param array $node
 * @param string $fchildrens
 *
 * @return array
 */
function tree_to_array(& $node, $fchildrens = 'childrens') {
    $ret = array();
    if (isset($node[$fchildrens]) && is_array($node[$fchildrens])) {
        foreach ($node[$fchildrens] as $child) {
            $ret = array_merge($ret, tree_to_array($child, $fchildrens));
        }
        unset($node[$fchildrens]);
        $ret[] = $node;
    } else {
        $ret[] = $node;
    }
    return $ret;
}

/**
 * 根据指定的键值对数组排序
 *
 * @param array $array 要排序的数组
 * @param string $keyname 键值名称
 * @param int $sortDirection 排序方向
 *
 * @return array
 */
function array_column_sort($array, $keyname, $sortDirection = SORT_ASC) {
    return array_sortby_multifields($array, array($keyname => $sortDirection));
}

/**
 * 将一个二维数组按照指定列进行排序，类似 SQL 语句中的 ORDER BY
 *
 * @param array $rowset
 * @param array $args
 */
function array_sortby_multifields($rowset, $args) {
    $sortArray = array();
    $sortRule = '';
    foreach ($args as $sortField => $sortDir) {
        foreach ($rowset as $offset => $row) {
            $sortArray[$sortField][$offset] = $row[$sortField];
        }
        $sortRule .= '$sortArray[\'' . $sortField . '\'], ' . $sortDir . ', ';
    }
    if (empty($sortArray) || empty($sortRule)) {
        return $rowset;
    }
    eval('array_multisort(' . $sortRule . '$rowset);');
    return $rowset;
}

function getSite($id = 0, $type='name') {
    $siteids = C('siteids');
    $data = array();
    if($id) {
        if(is_numeric($id)) {
            $data = $siteids[$id];
        } else {
            foreach($siteids as $v) {
                if($v['name'] == $id) {
                    $data = $v;
                    break;
                }
            }
        }
        return $data[$type];
    } else {
        return $siteids;
    }
}

function showKind($status,$data) {
    header('Content-type:application/json;charset=UTF-8');
    if($status == 0) {
        exit(json_encode(array('error'=>0,'url'=>$data)));
    }
    exit(json_encode(array('error'=>1,'message'=>'上传失败')));
}
