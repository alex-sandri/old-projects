<?php

namespace Denvelope\Utils;

class Crypto
{
    public static function encrypt(string $plaintext){
        return $plaintext;
    }

    public static function decrypt(string $ciphertext){
        return $ciphertext;
    }

    public static function decryptArray(array $array){
        $decrypted_array = array();

        foreach ($array as $key => $value) {
            if(\is_array($value)){
                $decrypted_array[$key] = self::decryptArray($value);
            }
            else{
                $decrypted_array[$key] = self::decrypt($value);
            }
        }

        return $decrypted_array;
    }

    public static function HashPassword(string $password)
    {
        if (empty($password)) return null;

        return \sodium_crypto_pwhash_str($password, \SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE, \SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE);
    }

    public static function VerifyPassword(string $password, string $hash) : bool
    {
        if (empty($password) || empty($hash)) return false;

        return \sodium_crypto_pwhash_str_verify($hash, $password);
    }
}