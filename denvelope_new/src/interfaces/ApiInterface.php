<?php

namespace Denvelope\interfaces;

interface ApiInterface
{
    public static function Create (array $data) : array;
    public static function Retrieve (array $data) : array;
    public static function Update (array $data) : array;
    public static function Delete (array $data) : array;
}