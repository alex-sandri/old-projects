<?php

namespace Denvelope\Api;

class Api
{
    public static function SetStatus (int $code) : void
    {
        http_response_code($code);
        exit();
    }
}