<?php
/**
 * 支付通知验签demo
 */

$data = $_POST;
/**
* 注意$_POST数据如果服务器没有自动处理urldecode，请做一次urldecode(参考rfc1738标准)处理
*/
/**
foreach ($data as $key => $value) {
        $data[$key] = urldecode($value);
}
**/

$privateKey = "abcdef123456xxxxxx";
if (checkSign($data, $privateKey)) {
        // @todo 验证成功，游戏服务器处理逻辑
        echo "ok";
} else {
        //@todo
        echo "failed";
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
        //sign 不参与签名
        unset($data['sign']);
        //数组按key升序排序
        ksort($data);
        //将数组中的值不加任何分隔符合并成字符串
        $string = implode('', $data);
        //做一次md5并转换成小写，末尾追加游戏的privateKey，最后再次做md5并转换成小写
        return strtolower(md5(strtolower(md5($string)) . $privateKey));
}

?>
