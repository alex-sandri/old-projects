<?php
    function reCAPTCHAVerify($token){
        require("global-vars.php");

        $url = "https://www.google.com/recaptcha/api/siteverify";

        $data = array(
            "secret" => $reCAPTCHAPrivateKey,
            "response" => $token,
            "remoteip" => $_SERVER['HTTP_X_FORWARDED_FOR']
        );

        $options = array(
            "http" => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                "method" => "POST",
                "content" => http_build_query($data)
            )
        );

        $context = stream_context_create($options);

        $result = file_get_contents($url, false, $context);

        $captcha = json_decode($result);

        return $captcha->success;
    }
?>