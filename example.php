<?php

    include_once 'twitter-rest-api.php';

    $api_key = 'YOUR_API_KEY';
    $api_secret = 'YOUR_API_SECRET';
    $access_token = 'YOUR_ACCESS_TOKEN';
    $access_token_secret = 'YOUR_ACCESS_TOKEN_SECRET';

    $twitter = new \TwitterRestApi\Wrapper($api_key, $api_secret);
    $twitter->setAccessToken($access_token, $access_token_secret);
    $twitter->debug($twitter->get('/search/tweets.json', ['q'=>'#twitter']));

?>