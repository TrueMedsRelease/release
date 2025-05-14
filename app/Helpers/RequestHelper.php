<?php

namespace App\Helpers;

class RequestHelper
{
    public static function GetUserIp(): string
    {
        return request()->headers->get('cf-connecting-ip') ?? request()->ip();
    }
}