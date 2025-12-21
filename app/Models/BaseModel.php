<?php
class BaseModel
{
    protected $pdo;
    public function __construct()
    {
        $this->pdo = $GLOBALS['pdo'];
    }
    public function encryptData($input)
    {
        if ($input === null || $input === '') return $input;
        $password = 'Hcraj@123';
        $method = 'AES-256-CBC';
        $key = substr(hash('SHA256', $password, true), 0, 32);
        $iv  = str_repeat(chr(0x0), 16);
        return base64_encode(openssl_encrypt($input, $method, $key, OPENSSL_RAW_DATA, $iv));
    }

    public function decryptData($input)
    {
        if ($input === null || $input === '') return $input;
        $password = 'Hcraj@123';
        $method = 'AES-256-CBC';
        $key = substr(hash('SHA256', $password, true), 0, 32);
        $iv  = str_repeat(chr(0x0), 16);
        return openssl_decrypt(base64_decode($input), $method, $key, OPENSSL_RAW_DATA, $iv);
    }
    function decodeField($value)
    {
        return urldecode(base64_decode($value));
    }

   
}
