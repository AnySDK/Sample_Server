<?php

include '../AnySDK.config.php';
include '../AnySDK.Sdk.class.php';

$payment_params = $_REQUEST;
$anysdk = new Sdk_AnySDK(ANYSDK_ENHANCED_KEY, ANYSDK_PRIVATE_KEY);

/**
 * 设置调试模式
 * 
 */
$anysdk->setDebugMode(Sdk_AnySDK::DEBUG_MODE_ON);

/**
 * ip白名单检查
 *
$anysdk->pushIpToWhiteList('127.0.0.1');
$anysdk->checkIpWhiteList() or die(Sdk_AnySDK::PAYMENT_RESPONSE_FAIL . 'ip');
 */

/**
 * SDK默认只检查增强签名，如果要检查普通签名和增强签名，则需要此设置
 * 
 */
$anysdk->setPaymentSignCheckMode(Sdk_AnySDK::PAYMENT_SIGN_CHECK_MODE_BOTH);
$check_sign = $anysdk->checkPaymentSign($payment_params);
if (!$check_sign) {
        echo $anysdk->getDebugInfo();
        die(Sdk_AnySDK::PAYMENT_RESPONSE_FAIL . 'sign_error');
}

/**
 * 检查订单状态，1为成功
 */
if (intval($anysdk->getPaymentStatus()) !== Sdk_AnySDK::PAYMENT_STATUS_SUCCESS) {
	die(Sdk_AnySDK::PAYMENT_RESPONSE_OK);
}

/**
 * 获取支付通知详细参数
 * 
$amount = $anysdk->getPaymentAmount();
$product_id = $anysdk->getPaymentProductId();
$product_name = $anysdk->getPaymentProductName();
$product_count = $anysdk->getPaymentProductCount();
$channel_product_id = $anysdk->getPaymentChannelProductId();
$user_id = $anysdk->getPaymentUserId();
$game_user_id = $anysdk->getPaymentGameUserId();
$order_id = $anysdk->getPaymentOrderId();
$channel_order_id = $anysdk->getPaymentChannelOrderId();
$private_data = $anysdk->getPaymentPrivateData();
 */

        echo $anysdk->getDebugInfo();
echo Sdk_AnySDK::PAYMENT_RESPONSE_OK;
