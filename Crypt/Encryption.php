<?php

namespace SfCod\VichUploaderEncrypt\Crypt;

class Encryption
{
    const ENCRYPT_METHOD = "AES-256-CBC";
    const ACTION_ENCRYPT = 'encrypt';
    const ACTION_DECRYPT = 'decrypt';

    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var string
     */
    private $secretIv;

    /**
     * @param string $key
     * @param string $secretIv
     */
    public function __construct(string $key, string $secretIv)
    {
        $this->secretKey = $key;
        $this->secretIv = $secretIv;
    }

    /**
     * @param string $data
     *
     * @return bool|mixed|string
     */
    public function encrypt(string $data)
    {
        return $this->crypt(static::ACTION_ENCRYPT, $data);
    }

    /**
     * @param string $data
     *
     * @return bool|mixed|string
     */
    public function decrypt(string $data)
    {
        return $this->crypt(static::ACTION_DECRYPT, $data);
    }

    /**
     * @param string $action
     * @param string $string
     *
     * @return string|bool
     */
    protected function crypt(string $action, string $string)
    {
        $output = false;
        $key = hash('sha256', $this->secretKey);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $this->secretIv), 0, 16);
        if ($action == static::ACTION_ENCRYPT) {
            $output = openssl_encrypt($string, static::ENCRYPT_METHOD, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == static::ACTION_DECRYPT) {
            $output = openssl_decrypt(base64_decode($string), static::ENCRYPT_METHOD, $key, 0, $iv);
        }

        return $output;
    }
}