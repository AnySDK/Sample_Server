<?php

include '../AnySDK.config.php';
include '../AnySDK.Sdk.class.php';

header("Content-type: application/json; charset=utf-8");

$login_params = $_REQUEST;
$anysdk = new Sdk_AnySDK();

/**
 * 如果您使用的是云版本或者企业版，记得修改LOGIN_CHECK_URL的定义
 * 
$anysdk->setLoginCheckUrl(LOGIN_CHECK_URL);
 */

/**
 * 如果接入的是AnySDK for H5，则需要在此处将private_key传给AnySDK服务器
 * 
$anysdk->setPrivateKey(ANYSDK_PRIVATE_KEY);
$anysdk->usePrivateKeyForLoginForward();
 */

$response = $anysdk->loginForward($login_params);

// 登录验证成功
if ($anysdk->getLoginStatus()) {

        // 获取登录结果的一些字段
        $channel = $anysdk->getLoginChannel();
        $uid = $anysdk->getLoginUid();
        $user_sdk = $anysdk->getLoginUserSdk();
        $plugin_id = $anysdk->getLoginPluginId();
        $server_id = $anysdk->getLoginServerId();
        $data = $anysdk->getLoginData();   // 获取登录验证渠道返回的原始内容
        // 获取登录结果字段值示例结束

}

/**
 * 可在此处往ext字段加入一些内容以便传递给客户端
 * 
$resp_arr = json_decode($response, TRUE);
$resp_arr['ext'] = '';
$response = json_encode($resp_arr);
*/

echo is_scalar($response)? $response: json_encode($response);
