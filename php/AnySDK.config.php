<?php
/**
 * AnySDK服务端SDK config for PHP
 * 
 * @author    李播<libo@anysdk.com>
 * @date        2016-01-11
 * @version  2.0.0     
 * @link         http://docs.anysdk.com/OauthLogin         统一登录验证
 * @link         http://docs.anysdk.com/PaymentNotice  订单支付通知
 */
defined('LOGIN_CHECK_URL') or define('LOGIN_CHECK_URL', 'http://oauth.anysdk.com/api/User/LoginOauth/');
defined('DEBUG_MODE') or define('DEBUG_MODE', FALSE);

// 增强密钥        前往dev.anysdk.com => 游戏列表 获取，此参数请严格保密
defined('ANYSDK_ENHANCED_KEY') or define('ANYSDK_ENHANCED_KEY', 'YTdkY2Q5ZmRmNDUxMjkxYzAxOTM');
// private_key        前往dev.anysdk.com => 游戏列表 获取
defined('ANYSDK_PRIVATE_KEY') or define('ANYSDK_PRIVATE_KEY', 'CB2B5A269635B23ACD4FBE1D7B687274');
