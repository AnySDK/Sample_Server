<?php
/**
 * 广告追踪服务端接口，目前只支持payment
 */

include '../AnySDK.config.php';
include '../AnySDK.Sdk.class.php';

header("Content-type: application/json; charset=utf-8");

$anysdk = new Sdk_AnySDK();

/**
 * 如果您使用的是云版本或者企业版，记得修改ADTRACKING_REPORT_URL的定义
 */
$anysdk->setAdTrackingReportUrl(ADTRACKING_REPORT_URL);

$timestamp  = time();
$order_id   = '';
$channel_id = '';

$data_to_report = array(
    'game_id'        => ANYSDK_GAME_ID,  // AnySDK游戏ID，必填
    'channel_number' => $channel_id,     // AnySDK渠道号，必填
    'amount'         => '',              // 单位为元，必填
    'order_id'       => $order_id,       // AnySDK订单号，必填
    'imei'           => '',              // 移动设备国际身份码
    'imsi'           => '',              // 国际移动用户识别码
    'idfv'           => '',              // 厂商标识符
    'idfa'           => '',              // 广告标识符
    'payment_type'   => '支付宝',         // 支付方式
    'currency'       => 'CNY',           // 货币类型，使用ISO 4217中规范的3位字母代码标记货币类型
    'user_account'   => '',              // 用户账号
    'mac'            => '',              // 设备mac地址
    'timestamp'      => $timestamp,      // 时间戳
    'ip'             => '',              // IPv4地址
    'os_version'     => '',              // 操作系统版本
    'msg_id'         => md5(ANYSDK_GAME_ID . $channel_id . $order_id . $timestamp),    // 该条数据的 ID,由开发者自行定义;
    'operators'      => '',              // 运营商
    'network'        => '',              // 网络类型
    'device_name'    => '',              // 设备名称，例如："XXX 的 iPhone"
    'gpid'           => '',              // GooglePlay ID
    'manufacturer'   => '',              // 制造商
    'device_model'   => '',              // 设备类型
    'android_id'     => '',              // Android ID
);

/**
 * 第二个参数是广告追踪提交数据类型，目前只支持payment
 */
$response = $anysdk->adTrackingReport($data_to_report, Sdk_AnySDK::ADTRACKING_METHOD_PAYMENT);

if ($anysdk->getAdTrackingReportStatus()) {
        echo "success\n";
} else {
        echo "fail\n";
}
