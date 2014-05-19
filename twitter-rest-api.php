<?php

    namespace  TwitterRestApi;

    Class Wrapper {

        public $api_key;
        public $api_secret;
        public $access_token;
        public $access_token_secret;
        public $oauth;

        public function __construct($api_key, $api_secret){
            $this->api_key = $api_key;
            $this->api_secret = $api_secret;
        }

        public function setAccessToken($token, $secret){
            $this->access_token = $token;
            $this->access_token_secret = $secret;

            $this->oauth = array(
                'oauth_consumer_key' => $this->api_key,
                'oauth_nonce' => time(),
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_token' => $this->access_token,
                'oauth_timestamp' => time(),
                'oauth_version' => '1.0'
            );

        }

        public function get($url, $parameters=''){
            return json_decode($this->curlGET($url, $parameters));
        }

        public function post($url, $parameters=''){
            return json_decode($this->curlPOST($url, $parameters));
        }

        protected function parseParameters($parameters){
            $params = '?';
            $params_array = array();
            foreach ($parameters as $key => $val){
                $params_array[] = $key.'='.$val;
            }
            $params .= implode('&', $params_array);
            return $params;
        }

        protected function addParamsToOauth($parameters){
            foreach($parameters as $key => $val){
                $this->oauth[$key] = $val;
            }
            return true;
        }

        protected function curlPOST($url, $parameters=''){
            return array('msg' => 'Not yet implemented');
        }

        protected function curlGET($url, $parameters=''){

            if (is_array($parameters)){
                $this->addParamsToOauth($parameters);
                $parameters = $this->parseParameters($parameters);
            }

            // Oauth
            $base_info = $this->buildBaseString($url, 'GET', $this->oauth);
            $composite_key = rawurlencode($this->api_secret) . '&' . rawurlencode($this->access_token_secret);
            $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
            $this->oauth['oauth_signature'] = $oauth_signature;
            $header = array($this->buildAuthorizationHeader($this->oauth), 'Expect:');


            // Make requests
            $options = array( CURLOPT_HTTPHEADER => $header,
                              // CURLOPT_POSTFIELDS => http_build_query($parameters, '', '&'),
                              CURLOPT_HEADER => false,
                              CURLOPT_URL => $url.$parameters,
                              CURLOPT_RETURNTRANSFER => true,
                              CURLOPT_SSL_VERIFYPEER => false);

            $cn = curl_init();
            curl_setopt_array($cn, $options);
            $return = curl_exec($cn);
            curl_close($cn);

            return $return;

        }

        protected function buildBaseString($baseURI, $method, $params) {
            $r = array();
            ksort($params);
            foreach($params as $key=>$value){
                $r[] = "$key=" . rawurlencode($value);
            }
            return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
        }

        protected function buildAuthorizationHeader($oauth) {
            $r = 'Authorization: OAuth ';
            $values = array();
            foreach($oauth as $key=>$value)
                $values[] = "$key=\"" . rawurlencode($value) . "\"";
            $r .= implode(', ', $values);
            return $r;
        }

    }

?>
