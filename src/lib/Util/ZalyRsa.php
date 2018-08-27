<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 17/07/2018
 * Time: 4:34 PM
 */

class ZalyRsa
{

    private $option = OPENSSL_PKCS1_PADDING;

    public function encrypt($data, $key)
    {
        openssl_public_encrypt($data, $crypted, $key, $this->option);
        return $crypted;
    }


    public function decrypt($data, $key)
    {
        openssl_private_decrypt($data, $decrypted, $key, $this->option);
        return $decrypted;
    }

    public function sign($data, $privateKey)
    {
        openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        return $signature;
    }

}
