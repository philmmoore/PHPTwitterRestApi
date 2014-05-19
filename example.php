<?php

    include_once 'twitter-rest-api.php';

    $api_key = 'YOUR_API_KEY';
    $api_secret = 'YOUR_API_SECRET';
    $access_token = 'YOUR_ACCESS_TOKEN';
    $access_token_secret = 'YOUR_ACCESS_TOKEN_SECRET';

    $twitter = new \TwitterRestApi\Wrapper($api_key, $api_secret);
    $twitter->setAccessToken($access_token, $access_token_secret);

    // $response = $twitter->get('https://api.twitter.com/1.1/statuses/user_timeline.json', array('count'=>'15'));
    // $response = $twitter->post('https://api.twitter.com/1.1/account/update_profile.json', array('location'=>'Southampton, UK'));

    echo "<pre>";
    print_r($response);
    echo "</pre>";


?>