<?php

    namespace  TwitterRestApi;

    Class Wrapper {

        public $api_key;
        public $api_secret;
        public $access_token;
        public $access_token_secret;
        public $oauth;
        public $api_base_url;

        public function __construct($api_key, $api_secret, $version = '1.1'){
            $this->api_key = $api_key;
            $this->api_secret = $api_secret;
            $this->api_base_url = 'https://api.twitter.com/'.$version.'/';
        }

        public function setAccessToken($token, $secret){
            $this->access_token = $token;
            $this->access_token_secret = $secret;
        }

        public function get($url, $parameters=''){
            return json_decode($this->curlGET($this->buildRequestURL($url), $parameters));
        }

        public function post($url, $parameters=''){
            return json_decode($this->curlPOST($this->buildRequestURL($url), $parameters));
        }

        public function debug($data){
            echo "<pre>";
            print_r($data);
            echo "</pre>";
        }

        private function curlPOST($url, $parameters=''){

            $oauth = $this->setOAuthSignature($url, 'POST', $parameters);
            $header = array(
                $this->buildAuthorizationHeader($oauth),
                'Expect:'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, (is_array($parameters) ? ltrim($this->parseParameters($parameters), '?') : ''));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $return = curl_exec($ch);
            curl_close($ch);

            return $return;

        }

        private function curlGET($url, $parameters=''){

            $oauth = $this->setOAuthSignature($url, 'GET', $parameters);
            $header = array(
                $this->buildAuthorizationHeader($oauth),
                'Expect:'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_URL, $url.(is_array($parameters) ? $this->parseParameters($parameters) : ''));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $return = curl_exec($ch);
            curl_close($ch);

            return $return;

        }

        private function buildRequestURL($url){
            return $this->api_base_url.ltrim($url, '/');
        }

        private function parseParameters($parameters){
            $params = '?';
            $params_array = array();
            foreach ($parameters as $key => $val){
                $params_array[] = $key.'='.urlencode($val);
            }
            $params .= implode('&', $params_array);
            return $params;
        }

        private function addParamsToOauth($parameters, $oauth){
            foreach($parameters as $key => $val){
                $oauth[$key] = $val;
            }
            return $oauth;
        }

        private function setOAuthSignature($url, $type, $data=''){
            
            $oauth = array(
                'oauth_consumer_key' => $this->api_key,
                'oauth_nonce' => time(),
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_token' => $this->access_token,
                'oauth_timestamp' => time(),
                'oauth_version' => '1.0'
            );

            if (is_array($data)){
                $oauth = $this->addParamsToOauth($data, $oauth);
            }

            $base_info = $this->buildBaseString($url, $type, $oauth);
            $composite_key = rawurlencode($this->api_secret) . '&' . rawurlencode($this->access_token_secret);
            $oauth['oauth_signature'] = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));

            return $oauth;

        }

        private function buildBaseString($baseURI, $method, $params) {
           
            $r = array();
            ksort($params);

            foreach($params as $key=>$value){
                $r[] = "$key=" . rawurlencode($value);
            }

            return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));

        }

        private function buildAuthorizationHeader($oauth) {

            $r = 'Authorization: OAuth ';
            $values = array();

            foreach($oauth as $key=>$value){
                $values[] = "$key=\"" . rawurlencode($value) . "\"";
            }

            $r .= implode(', ', $values);

            return $r;
        }

    }

?>
