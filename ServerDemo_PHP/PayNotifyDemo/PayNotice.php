<?php
error_reporting(0);

/**
 * 支付通知验签demo
 * 传输过程一律使用UTF-8编码
 */
define(LOG_FILE, './game.server.notify.log');
file_put_contents(LOG_FILE, "#" . date('Y-m-d H:i:s') . "\n#AnySDK支付通知HTTP原文:\n" . get_http_raw() . "\n");

checkAnySDKSever();
$privateKey = "696064B29E9A0B7DDBD6FCB88F34A555";
$params = $_POST;
//注意$_POST数据如果服务器没有自动处理urldecode，请做一次urldecode(参考rfc1738标准)处理
//foreach ($params as $key => $value) {
//        $params[$key] = urldecode($value);
//}

if (checkSign($params, $privateKey)) {
        checkAmount($params);
        // @todo 验证成功，游戏服务器处理逻辑
        echo "ok";
} else {
        //@todo
        echo "Wrong signature.";
}
exit;


/**
 * anysdk 支付通知白名单判断
 */
function checkAnySDKSever() {
        $AnySDKServerIps = array('211.151.20.126', '211.151.20.127');
        $remoteIp = $_SERVER['REMOTE_ADDR'];
        if (!in_array($remoteIp, $AnySDKServerIps)) {
                echo "remote address is illegal.";
                exit;
        }
}

/**
 * 检测道具金额与实际金额是否一致，开发者根据实际情况自己实现判断方式
 * @param type $params
 */
function checkAmount($params) {
        if (getProductAmount($params['product_id']) != $params['amount']) {
                echo 'Purchase is illegal. order_id:' . $params['order_id'];
                exit;
        }
}

/**
 * 获取道具在服务器上的金额
 * @param type $productId 
 * @return int 单位元
 */
function getProductAmount($productId) {
        //get amount by productId
        return 1;
}

/**
 * 验签
 * @param array $data 接收到的所有请求参数数组，通过$_POST可以获得。注意data数据如果服务器没有自动解析，请做一次urldecode(参考rfc1738标准)处理
 * @param array $privateKey AnySDK分配的游戏privateKey
 * @return bool
 */
function checkSign($data, $privateKey) {
        if (empty($data) || !isset($data['sign']) || empty($privateKey)) {
                return false;
        }
        $sign = $data['sign'];
        $_sign = getSign($data, $privateKey);
        if ($_sign != $sign) {
                return false;
        }
        return true;
}

/**
 * 计算签名
 * @param array $data
 * @param string $privateKey
 * @return string
 */
function getSign($data, $privateKey) {
        file_put_contents(LOG_FILE, "#\n#原始数组:\n" . print_r($data, true) . "\n", FILE_APPEND);

        //sign 不参与签名
        unset($data['sign']);

        //数组按key升序排序        
        ksort($data);
        file_put_contents(LOG_FILE, "#\n#参数数组按key升序:\n" . print_r($data, true) . "\n", FILE_APPEND);

        //将数组中的值不加任何分隔符合并成字符串
        $string = implode('', $data);
        file_put_contents(LOG_FILE, "#\n#将数组值连接成字符串:\n" . print_r($string, true) . "\n", FILE_APPEND);

        //第一次md5并转换成小写
        $theFirstMd5String = strtolower(md5($string));
        file_put_contents(LOG_FILE, "#\n#第一次md5并小写:\n" . print_r($theFirstMd5String, true) . "\n", FILE_APPEND);

        //追加privatekey
        $addPrivateKeyString = $theFirstMd5String . $privateKey;
        file_put_contents(LOG_FILE, "#\n#追加privatekey:\n" . print_r($addPrivateKeyString, true) . "\n", FILE_APPEND);

        //第二次md5并转换成小写
        $theLastMd5String = strtolower(md5($addPrivateKeyString));
        file_put_contents(LOG_FILE, "#\n#最后一次md5并小写(签名):\n" . print_r($theLastMd5String, true) . "\n", FILE_APPEND);

        return $theLastMd5String;
//        return strtolower(md5(strtolower(md5($string)) . $privateKey));
}

/**
 * 获取HTTP请求原文 调试使用
 * @return string 
 */
function get_http_raw() {
        $raw = '';

        // (1) 请求行 
        $raw .= $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . ' ' . $_SERVER['SERVER_PROTOCOL'] . "\n";

        // (2) 请求Headers 
        foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) === 'HTTP_') {
                        $key = substr($key, 5);
                        $key = str_replace('_', '-', $key);

                        $raw .= $key . ': ' . $value . "\n";
                }
        }

        // (3) 空行 
        $raw .= "\n";

        // (4) 请求Body 
        $raw .= file_get_contents('php://input');

        return $raw;
}

?>
