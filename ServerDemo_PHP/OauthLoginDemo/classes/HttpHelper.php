<?php

/**
 *  HttpHelper provides http methods
 * @author ad
 */
class HttpHelper {

        /**
         * request url
         * @var type 
         */
        private $_url = '';

        /**
         * connect time out
         * @var int 
         */
        private $_connectTimeOut = 30;

        /**
         * time out second
         * @var int
         */
        private $_timeOut = 30;

        /**
         * user agent
         * @var string 
         */
        private $_userAgent = 'px v1.0';

        /**
         * set CURLOPT_CONNECTTIMEOUT  - s
         * @param type $time
         * @return \HttpHelper
         */
        public function setConnectTimeOut($time = 30) {
                $this->_connectTimeOut = $time;
                return $this;
        }

        /**
         * set CURLOPT_TIMEOUT - s
         * @param type $time
         * @return \HttpHelper
         */
        public function setTimeOut($time = 30) {
                $this->_timeOut = $time;
                return $this;
        }

        /**
         * request url
         * @return type
         */
        public function getUrl() {
                return $this->_url;
        }

        /**
         * Make an POST request.
         * @param string $url      url like "http://example.com".
         * @param array  $data     An array to make query string like "example1=&example2=" .
         * @return mixed
         */
        public function post($url, $data = array()) {
                $this->_url = $url;
                $query = $this->buildHttpQuery($data, 'POST');

                $response = $this->makeRequest($this->_url, 'POST', $query);
                return $response;
        }

        /**
         * Make an GET request.
         * @param string $url     url like "http://example.com".
         * @param array  $data    An array to make query string like "example1=&example2=" .
         * @return mixed
         */
        public function get($url, $data = array()) {
                $this->_url = $url;
                if (!empty($data)) {
                        $this->_url .= "?" . $this->buildHttpQuery($data);
                }
                $response = $this->makeRequest($this->_url, 'GET');
                return $response;
        }

        /**
         * Make an HTTP request.
         * @param string $url        url like "http://example.com/xxxx?example1=&example2=".
         * @param string $method     Request method is "GET" or "POST".
         * @param string $postfields A query string post to $url.
         * @param bool   $multi.
         * @return mixed
         */
        public function makeRequest($url, $method, $postfields = NULL) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
                if ('POST' === $method) {
                        curl_setopt($ch, CURLOPT_POST, 1);
                        if (!empty($postfields)) {
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
                        }
                }
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_connectTimeOut);
                curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeOut);
                curl_setopt($ch, CURLOPT_USERAGENT, $this->_userAgent);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                $output = curl_exec($ch);
                curl_close($ch);
                return $output;
        }

        /**
         * Build HTTP Query.
         * @param array $params Name => value array of parameters.
         * @return string HTTP query.
         */
        public function buildHttpQuery(array $params, $method = 'GET') {
                if (empty($params)) {
                        return '';
                }

                if ('GET' == $method) {
                        $keys = $this->urlencode(array_keys($params));
                        $values = $this->urlencode(array_values($params));
                } else {
                        $keys = array_keys($params);
                        $values = array_values($params);
                }

                $params = array_combine($keys, $values);

                uksort($params, 'strcmp');

                $pairs = array();
                foreach ($params as $key => $value) {
                        $pairs[] = $key . '=' . $value;
                }

                return implode('&', $pairs);
        }

        /**
         * URL Encode.
         * @param mixed $item string or array of items to url encode.
         * @return mixed url encoded string or array of strings.
         */
        public function urlencode($item) {
                static $search = array('%7E');
                static $replace = array('~');

                if (is_array($item)) {
                        return array_map(array(&$this, 'urlencode'), $item);
                }

                if (is_scalar($item) === false) {
                        return $item;
                }

                return str_replace($search, $replace, rawurlencode($item));
        }

        /**
         * URL Decode.
         * @param mixed $item Item to url decode.
         * @return string URL decoded string.
         */
        public function urldecode($item) {
                if (is_array($item)) {
                        return array_map(array(&$this, 'urldecode'), $item);
                }

                return urldecode($item);
        }

}

?>
