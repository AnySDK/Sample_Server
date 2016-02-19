<?php

/**
 * AnySDK服务端SDK for PHP
 * 
 * @author    李播<libo@anysdk.com>
 * @date        2016-01-11
 * @version  2.0.0
 * @link         http://docs.anysdk.com/OauthLogin         统一登录验证
 * @link         http://docs.anysdk.com/PaymentNotice  订单支付通知
 */
class Sdk_AnySDK {

	const DEBUG_MODE_ON = TRUE;
	const DEBUG_MODE_OFF = FALSE;

	/**
	 * 支付通知检查所有签名
	 */
	const PAYMENT_SIGN_CHECK_MODE_BOTH = 1;

	/**
	 * 支付通知仅检查增强签名 enhanced_sign，此项为默认值
	 */
	const PAYMENT_SIGN_CHECK_MODE_ENHANCED = 2;

	/**
	 * 各种过期时间的默认值10秒
	 */
	const TIMEOUT_SECONDS_DEFAULT = 10;

	/**
	 * AnySDK登录验证url
	 * @var String 
	 */
	const LOGIN_CHECK_URL = 'http://oauth.anysdk.com/api/User/LoginOauth/';

	/**
	 * ===================================================================================
	 * 将字符常量定义成代码常量
	 * 主要是一些字段名称
	 */
	const FIELD_STATUS = 'status';
	const FIELD_COMMON = 'common';
	const FIELD_COMMON_CHANNEL = 'channel';
	const FIELD_COMMON_USER_SDK = 'user_sdk';
	const FIELD_COMMON_UID = 'uid';
	const FIELD_COMMON_SERVER_ID = 'server_id';
	const FIELD_COMMON_PLUGIN_ID = 'plugin_id';
	const FIELD_DATA = 'data';
	const FIELD_EXT = 'ext';
	const FIELD_PRIVATE_KEY = 'private_key';
	const FIELD_SIGN = 'sign';
	const FIELD_ENHANCED_SIGN = 'enhanced_sign';
	const FIELD_CHANNEL = 'channel';
	const FIELD_UAPI_KEY = 'uapi_key';
	const FIELD_UAPI_SECRET = 'uapi_secret';
	const FIELD_ORDER_ID = 'order_id';
	const FIELD_PRODUCT_COUNT = 'product_count';
	const FIELD_AMOUNT = 'amount';
	const FIELD_PAY_STATUS = 'pay_status';
	const FIELD_PAY_TIME = 'pay_time';
	const FIELD_USER_ID = 'user_id';
	const FIELD_ORDER_TYPE = 'order_type';
	const FIELD_GAME_USER_ID = 'game_user_id';
	const FIELD_SERVER_ID = 'server_id';
	const FIELD_PRODUCT_NAME = 'product_name';
	const FIELD_PRODUCT_ID = 'product_id';
	const FIELD_PRIVATE_DATA = 'private_data';
	const FIELD_CHANNEL_PRODUCT_ID = 'channel_product_id';
	const FIELD_CHANNEL_ORDER_ID = 'channel_order_id';
	const FIELD_CHANNEL_NUMBER = 'channel_number';
	const FIELD_SOURCE = 'source';

	/**
	 * 将字符常量定义成代码常量结束
	 * ===================================================================================
	 */
	const LOGIN_STATUS_OK = 'ok';
	const LOGIN_STATUS_FAIL = 'fail';
	const PAYMENT_RESPONSE_OK = 'ok';
	const PAYMENT_RESPONSE_FAIL = 'fail.';
	const PAYMENT_STATUS_SUCCESS = 1;
	const HTTP_METHOD_POST = 'POST';
	const HTTP_METHOD_GET = 'GET';

	private $_debugMode = self::DEBUG_MODE_OFF;
	private $_loginCheckUrl = self::LOGIN_CHECK_URL;
	private $_lastError = '';
	private $_privateKey = '';
	private $_enhancedKey = '';
	private $_httpConnectTimeout = 10;
	private $_httpTimeout = 10;
	private $_usePrivateKeyForLoginForward = FALSE;

	/**
	 * 登录验证结果
	 * 
	 * @var type 
	 */
	private $_loginStatus = FALSE;

	/**
	 * 登录验证成功之后将验证结果以数组形式存储起来
	 * @var Array 
	 */
	private $_loginResponse = array();

	/**
	 * 默认只检查增强签名
	 * @var type 
	 */
	private $_paymentSignCheckMode = self::PAYMENT_SIGN_CHECK_MODE_ENHANCED;

	/**
	 * 调试信息
	 * @var Array 
	 */
	private $_debugInfo = array();

	/**
	 * ip白名单
	 */
	private $_ipWhiteList = array('211.151.20.126', '211.151.20.127', '117.121.57.82', '::1', '127.0.0.1');

	/**
	 * 支付通知验签成功后将参数保存起来
	 * 
	 * @var Array 
	 */
	private $_paymentParams = array();

	/**
	 * 构造方法，优先设置debug模式以便构造方法接下来也可以记录_debugInfo
	 * 
	 * @param type $enhancedKey
	 * @param type $privateKey
	 * @param type $loginCheckUrl
	 * @param type $debugMode
	 */
	public function __construct($enhancedKey = '', $privateKey = '', $loginCheckUrl = self::LOGIN_CHECK_URL, $debugMode = self::DEBUG_MODE_OFF) {
		$this->_debugMode = $debugMode;
		$this->_appendDebugInfo(__METHOD__ . ': init _debugMode = ' . $this->_debugMode);

		$this->_enhancedKey = $enhancedKey;
		$this->_appendDebugInfo(__METHOD__ . ': init _enhancedKey = ' . $this->_enhancedKey);

		$this->_privateKey = $privateKey;
		$this->_appendDebugInfo(__METHOD__ . ': init _privateKey = ' . $this->_privateKey);

		$this->_loginCheckUrl = $loginCheckUrl;
		$this->_appendDebugInfo(__METHOD__ . ': init _loginCheckUrl = ' . $this->_loginCheckUrl);
	}

	public function __destruct() {
		;
	}

	/**
	 * 设置debug模式
	 * 
	 * @param Boolean $debugMode
	 */
	public function setDebugMode($debugMode = self::DEBUG_MODE_ON) {
		$this->_debugMode = $debugMode;
		$this->_appendDebugInfo(__METHOD__ . ': init _debugMode = ' . $this->_debugMode);
	}

	/**
	 * 获取调试信息
	 * 
	 * @param String $separator 传递null将会以数组形式返回，否则将以$separator连接调试信息为字符串后返回
	 * @return Mixed String/Array
	 */
	public function getDebugInfo($separator = PHP_EOL) {
		if (is_null($separator)) {
			return $this->_debugInfo;
		}

		return implode($separator, $this->_debugInfo);
	}

	/**
	 * 获取登录状态
	 * 
	 * @return Boolean
	 */
	public function getLoginStatus() {
		return $this->_loginStatus;
	}

	/**
	 * 获取登录返回结果的common/channel字段
	 * @return boolean|string
	 */
	public function getLoginChannel() {
		if (isset($this->_loginResponse[self::FIELD_COMMON][self::FIELD_COMMON_CHANNEL])) {
			return $this->_loginResponse[self::FIELD_COMMON][self::FIELD_COMMON_CHANNEL];
		}

		return FALSE;
	}

	/**
	 * 获取登录返回结果的common/user_sdk字段
	 * @return boolean|string
	 */
	public function getLoginUserSdk() {
		if (isset($this->_loginResponse[self::FIELD_COMMON][self::FIELD_COMMON_USER_SDK])) {
			return $this->_loginResponse[self::FIELD_COMMON][self::FIELD_COMMON_USER_SDK];
		}

		return FALSE;
	}

	/**
	 * 获取登录结果的common/server_id字段
	 * @return boolean|string
	 */
	public function getLoginServerId() {
		if (isset($this->_loginResponse[self::FIELD_COMMON][self::FIELD_COMMON_SERVER_ID])) {
			return $this->_loginResponse[self::FIELD_COMMON][self::FIELD_COMMON_SERVER_ID];
		}

		return FALSE;
	}

	/**
	 * 获取登录结果的common/uid字段
	 * @return boolean|string
	 */
	public function getLoginUid() {
		if (isset($this->_loginResponse[self::FIELD_COMMON][self::FIELD_COMMON_UID])) {
			return $this->_loginResponse[self::FIELD_COMMON][self::FIELD_COMMON_UID];
		}

		return FALSE;
	}

	/**
	 * 获取登录结果的common/plugin_id字段
	 * 
	 * @return boolean|string
	 */
	public function getLoginPluginId() {
		if (isset($this->_loginResponse[self::FIELD_COMMON][self::FIELD_COMMON_PLUGIN_ID])) {
			return $this->_loginResponse[self::FIELD_COMMON][self::FIELD_COMMON_PLUGIN_ID];
		}

		return FALSE;
	}

	/**
	 * 获取登录结果的data字段，也就是渠道返回的原始参数
	 * @return mixed
	 */
	public function getLoginData() {
		if (isset($this->_loginResponse[self::FIELD_DATA])) {
			return $this->_loginResponse[self::FIELD_DATA];
		}

		return FALSE;
	}

	/**
	 * 
	 * @return boolean
	 */
	public function getLoginExt() {
		if (isset($this->_loginResponse[self::FIELD_EXT])) {
			return $this->_loginResponse[self::FIELD_EXT];
		}

		return FALSE;
	}

	/**
	 * 获取登录结果数组，通常用于设置完ext后获取结果返回给请求发起方
	 * 
	 * @return Array
	 */
	public function getLoginResponse() {
		return $this->_loginResponse;
	}

	/**
	 * 支付通知参数
	 * 
	 * @return type
	 */
	public function getPaymentParams() {
		return $this->_paymentParams;
	}

	/**
	 * 设置登录结果的ext字段
	 * @param type $ext
	 */
	public function setLoginExt($ext) {
		$this->_loginResponse[self::FIELD_EXT] = $ext;
	}

	/**
	 * 订单状态
	 * 
	 * @return boolean
	 */
	public function getPaymentStatus() {
		if (isset($this->_paymentParams[self::FIELD_PAY_STATUS])) {
			return $this->_paymentParams[self::FIELD_PAY_STATUS];
		}

		return FALSE;
	}

	/**
	 * AnySDK订单id
	 * @return boolean
	 */
	public function getPaymentOrderId() {
		if (isset($this->_paymentParams[self::FIELD_ORDER_ID])) {
			return $this->_paymentParams[self::FIELD_ORDER_ID];
		}

		return FALSE;
	}

	/**
	 * 订单金额
	 * @return boolean
	 */
	public function getPaymentAmount() {
		if (isset($this->_paymentParams[self::FIELD_AMOUNT])) {
			return $this->_paymentParams[self::FIELD_AMOUNT];
		}

		return FALSE;
	}

	/**
	 * 订单类型
	 * 
	 * @return boolean
	 */
	public function getPaymentOrderType() {
		if (isset($this->_paymentParams[self::FIELD_ORDER_TYPE])) {
			return $this->_paymentParams[self::FIELD_ORDER_TYPE];
		}

		return FALSE;
	}

	/**
	 * 渠道号
	 * 
	 * @return boolean
	 */
	public function getPaymentChannelNumber() {
		if (isset($this->_paymentParams[self::FIELD_CHANNEL_NUMBER])) {
			return $this->_paymentParams[self::FIELD_CHANNEL_NUMBER];
		}

		return FALSE;
	}

	/**
	 * 自定义参数，透传字段
	 * 
	 * @return boolean
	 */
	public function getPaymentPrivateData() {
		if (isset($this->_paymentParams[self::FIELD_PRIVATE_DATA])) {
			return $this->_paymentParams[self::FIELD_PRIVATE_DATA];
		}

		return FALSE;
	}

	/**
	 * 渠道通知原始参数
	 * 
	 * @return boolean
	 */
	public function getPaymentSource() {
		if (isset($this->_paymentParams[self::FIELD_SOURCE])) {
			return $this->_paymentParams[self::FIELD_SOURCE];
		}

		return FALSE;
	}

	/**
	 * 在AnySDK配置的商品id
	 * 
	 * @return boolean
	 */
	public function getPaymentProductId() {
		if (isset($this->_paymentParams[self::FIELD_PRODUCT_ID])) {
			return $this->_paymentParams[self::FIELD_PRODUCT_ID];
		}

		return FALSE;
	}

	/**
	 * 商品数量
	 * 
	 * @return boolean
	 */
	public function getPaymentProductCount() {
		if (isset($this->_paymentParams[self::FIELD_PRODUCT_COUNT])) {
			return $this->_paymentParams[self::FIELD_PRODUCT_COUNT];
		}

		return FALSE;
	}

	/**
	 * 通知时间
	 * 
	 * @return boolean
	 */
	public function getPaymentTime() {
		if (isset($this->_paymentParams[self::FIELD_PAY_TIME])) {
			return $this->_paymentParams[self::FIELD_PAY_TIME];
		}

		return FALSE;
	}

	/**
	 * 用户id
	 * 
	 * @return boolean
	 */
	public function getPaymentUserId() {
		if (isset($this->_paymentParams[self::FIELD_USER_ID])) {
			return $this->_paymentParams[self::FIELD_USER_ID];
		}

		return FALSE;
	}

	/**
	 * 游戏用户id
	 * 
	 * @return boolean
	 */
	public function getPaymentGameUserId() {
		if (isset($this->_paymentParams[self::FIELD_GAME_USER_ID])) {
			return $this->_paymentParams[self::FIELD_GAME_USER_ID];
		}

		return FALSE;
	}

	/**
	 * 服务器id
	 * 
	 * @return boolean
	 */
	public function getPaymentServerId() {
		if (isset($this->_paymentParams[self::FIELD_SERVER_ID])) {
			return $this->_paymentParams[self::FIELD_SERVER_ID];
		}

		return FALSE;
	}

	/**
	 * 商品名
	 * 
	 * @return boolean
	 */
	public function getPaymentProductName() {
		if (isset($this->_paymentParams[self::FIELD_PRODUCT_NAME])) {
			return $this->_paymentParams[self::FIELD_PRODUCT_NAME];
		}

		return FALSE;
	}

	/**
	 * 渠道订单号
	 * 
	 * @return boolean
	 */
	public function getPaymentChannelOrderId() {
		if (isset($this->_paymentParams[self::FIELD_CHANNEL_ORDER_ID])) {
			return $this->_paymentParams[self::FIELD_CHANNEL_ORDER_ID];
		}

		return FALSE;
	}

	/**
	 * 渠道商品id
	 * 
	 * @return boolean
	 */
	public function getPaymentChannelProductId() {
		if (isset($this->_paymentParams[self::FIELD_CHANNEL_PRODUCT_ID])) {
			return $this->_paymentParams[self::FIELD_CHANNEL_PRODUCT_ID];
		}

		return FALSE;
	}

	/**
	 * 设置AnySDK提供的private_key
	 * 
	 * @param type $privateKey
	 */
	public function setPrivateKey($privateKey) {
		$this->_privateKey = $privateKey;
		$this->_appendDebugInfo(__METHOD__ . ': init _privateKey = ' . $this->_privateKey);
	}

	/**
	 * 设置AnySDK提供的enhanced_key
	 * 
	 * @param type $enhancedKey
	 */
	public function setEnhancedKey($enhancedKey) {
		$this->_enhancedKey = $enhancedKey;
		$this->_appendDebugInfo(__METHOD__ . ': init _enhancedKey = ' . $this->_enhancedKey);
	}

	/**
	 * 自定义 curl CURLOPT_CONNECTTIMEOUT
	 * 
	 * @param type $httpConnectTimeout
	 */
	public function setHttpConnectTimeout($httpConnectTimeout = self::TIMEOUT_SECONDS_DEFAULT) {
		$this->_httpConnectTimeout = $httpConnectTimeout;
		$this->_appendDebugInfo(__METHOD__ . ': init _httpConnectTimeout = ' . $this->_httpConnectTimeout);
	}

	/**
	 * 自定义 curl CURLOPT_TIMEOUT
	 * 
	 * @param type $httpTimeout
	 */
	public function setHttpTimeout($httpTimeout = self::TIMEOUT_SECONDS_DEFAULT) {
		$this->_httpTimeout = $httpTimeout;
		$this->_appendDebugInfo(__METHOD__ . ': init _httpTimeout = ' . $this->_httpTimeout);
	}

	/**
	 * 设置验签模式，1为检查sign和enhanced_sign, 2为仅检查enhanced_sign，2为默认
	 * 
	 * @param type $paymentSignCheckMode
	 */
	public function setPaymentSignCheckMode($paymentSignCheckMode = self::PAYMENT_SIGN_CHECK_MODE_BOTH) {
		$this->_paymentSignCheckMode = $paymentSignCheckMode;
		$this->_appendDebugInfo(__METHOD__ . ': init _paymentSignCheckMode = ' . $this->_paymentSignCheckMode);
	}

	/**
	 * 设置登录验证地址
	 * 
	 * @param type $loginCheckUrl
	 */
	public function setLoginCheckUrl($loginCheckUrl = self::LOGIN_CHECK_URL) {
		$this->_loginCheckUrl = $loginCheckUrl;
		$this->_appendDebugInfo(__METHOD__ . ': init _loginCheckUrl = ' . $this->_loginCheckUrl);
	}

	/**
	 * 登录转发的时候注入private_key参数
	 */
	public function usePrivateKeyForLoginForward() {
		$this->_usePrivateKeyForLoginForward = TRUE;
		$this->_appendDebugInfo(__METHOD__ . ': init _usePrivateKeyForLoginForward = ' . (int) $this->_usePrivateKeyForLoginForward);
	}

	/**
	 * 最后一条错误信息
	 */
	public function getLastError() {
		return $this->_lastError;
	}

	/**
	 * 将一个ip地址添加到白名单列表
	 * 
	 * @param type $ip
	 */
	public function pushIpToWhiteList($ip) {
		$this->_ipWhiteList[] = $ip;
	}

	/**
	 * 清空ip白名单
	 * 如果你不想使用SDK已经内置的白名单ip，您可以将其清空
	 */
	public function clearIpWhiteList() {
		$this->_ipWhiteList = array();
	}

	/**
	 * 检查ip白名单
	 * 
	 * @param type $ip
	 * @return boolean
	 */
	public function checkIpWhiteList($ip = '') {
		if (empty($ip)) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		if (!in_array($ip, $this->_ipWhiteList)) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * 执行登录验证请求转发
	 * 
	 * @param array $params 客户端请求参数，如果不传递此参数，此方法将自动使用$_REQUEST
	 * @return boolean|string
	 */
	public function loginForward(Array $params = array()) {
		if (empty($params)) {
			$params = $_REQUEST;
		}

		if (!$this->_parametersIsset($params)) {
			$this->_lastError = __METHOD__ . ': missing parameters(channel, uapi_key, uapi_secret)';
			$this->_appendDebugInfo(__METHOD__ . ': check _parametersIsset failed');
			return FALSE;
		}

		if ($this->_usePrivateKeyForLoginForward) {
			$params[self::FIELD_PRIVATE_KEY] = $this->_privateKey;
		}

		$http_response = $this->_httpRequest($this->_loginCheckUrl, $params);

		if (empty($http_response)) {
			return FALSE;
		}

		$res_arr = json_decode($http_response, TRUE);

		if (empty($res_arr)) {
			$msg = __METHOD__ . ': anysdk server response non-json data';
			$this->_lastError = $msg;
			$this->_appendDebugInfo($msg);
			return FALSE;
		}

		/**
		 * 设置验证结果状态，保存验证结果数组
		 */
		if (isset($res_arr[self::FIELD_STATUS]) && $res_arr[self::FIELD_STATUS] === self::LOGIN_STATUS_OK) {
			$this->_loginStatus = TRUE;
			$this->_loginResponse = $res_arr;
		}

		return $http_response;
	}

	/**
	 * 验证支付通知签名
	 * 
	 * @param array $params
	 * @return Boolean      
	 */
	public function checkPaymentSign(Array $params = array()) {
		if (empty($params)) {
			$params = $_REQUEST;
		}

		if ($this->_paymentSignCheckMode === self::PAYMENT_SIGN_CHECK_MODE_ENHANCED) {
			$checkResult = $this->_checkEnhancedSign($params);
		} elseif ($this->_paymentSignCheckMode === self::PAYMENT_SIGN_CHECK_MODE_BOTH) {
			$checkResult = $this->_checkSign($params) && $this->_checkEnhancedSign($params);
		}

		if ($checkResult) {
			$this->_paymentParams = $params;
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * http请求客户端
	 * 
	 * @param type $url
	 * @param type $params
	 * @param type $method
	 * @return boolean|string
	 */
	private function _httpRequest($url, $params, $method = self::HTTP_METHOD_POST) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$this->_appendDebugInfo(__METHOD__ . ': curl url(' . $url . ')');

		curl_setopt($ch, CURLOPT_USERAGENT, 'AnySDK.Sdk.class.php.v2.0.0');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_httpConnectTimeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->_httpTimeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Except:"));

		if (strtoupper($method) === self::HTTP_METHOD_POST) {
			curl_setopt($ch, CURLOPT_POST, TRUE);
			if (!is_scalar($params)) {
				$params = http_build_query($params);
			}
			$this->_appendDebugInfo(__METHOD__ . ': curl post fields(' . $params . ')');
			if (!empty($params)) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			}
		}

		$result = curl_exec($ch);
		if (empty($result)) {
			$this->_lastError = __METHOD__ . ': ' . curl_error($ch);
			curl_close($ch);
			return FALSE;
		}

		curl_close($ch);
		return $result;
	}

	/**
	 * 检查必要的参数是否存在，包括channel, uapi_key, uapi_secret, private_key
	 * 
	 * @param array $params
	 * @return boolean
	 */
	private function _parametersIsset(Array $params) {
		$paramsCheck = isset($params[self::FIELD_CHANNEL]) && isset($params[self::FIELD_UAPI_KEY]) && isset($params[self::FIELD_UAPI_SECRET]);
		if (!$paramsCheck) {
			$this->_appendDebugInfo(__METHOD__ . ': check _parametersIsset failed');
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * 计算并验证普通签名
	 * 
	 * @param array $params
	 * @return boolean
	 */
	private function _checkSign(Array $params) {
		if (empty($params) || !isset($params[self::FIELD_SIGN])) {
			$this->_appendDebugInfo(__METHOD__ . ': check params failed');
			return FALSE;
		}

		$sign = $params[self::FIELD_SIGN];
		// sign 字段不参与签名
		unset($params[self::FIELD_SIGN]);
		$_sign = $this->_getSign($params, $this->_privateKey);
		if ($sign != $_sign) {
			$this->_appendDebugInfo(__METHOD__ . ': check sign failed with sign(' . $_sign . '|' . $sign . '), private_key(' . $this->_privateKey . ')');
			return FALSE;
		}

		return TRUE;
	}

	private function _checkEnhancedSign(Array $params) {
		if (empty($params) || !isset($params[self::FIELD_ENHANCED_SIGN])) {
			$this->_appendDebugInfo(__METHOD__ . ': check params failed');
			return FALSE;
		}

		$enhancedSign = $params[self::FIELD_ENHANCED_SIGN];
		// sign 和 enhanced_sign 不参与签名
		unset($params[self::FIELD_SIGN], $params[self::FIELD_ENHANCED_SIGN]);
		$_enhancedSign = $this->_getSign($params, $this->_enhancedKey);
		if ($enhancedSign != $_enhancedSign) {
			$this->_appendDebugInfo(__METHOD__ . ': check enhanced_sign failed with sign(' . $_enhancedSign . '|' . $enhancedSign . '), enhanced_key(' . $this->_enhancedKey . ')');
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * 计算签名
	 * 
	 * @param array $params
	 * @param type $key
	 * @return type
	 */
	private function _getSign(Array $params, $key) {
		// 数组按key排序
		ksort($params);
		// 将数组中的值不加任何分割字符拼接成字符串
		$string = implode('', $params);
		$this->_appendDebugInfo(__METHOD__ . ': sign string(' . $string . ')');
		// 做一次md5并转换成小写，末尾追加key再做一次md5并转换成小写
		$md5_first = strtolower(md5($string));
		$sign = strtolower(md5($md5_first . $key));
		return $sign;
	}

	/**
	 * 追加调试信息
	 * 
	 * @param type $msg
	 */
	private function _appendDebugInfo($msg) {
		if ($this->_debugMode) {
			$this->_debugInfo[] = $msg;
		}
	}

}
