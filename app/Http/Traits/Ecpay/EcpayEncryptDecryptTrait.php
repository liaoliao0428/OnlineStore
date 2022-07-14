<?

namespace App\Http\Traits\Ecpay;


trait EcpayEncryptDecryptTrait
{ 
    // 綠界AES解密參數
    protected static $hashKey = "5294y06JbISpM5x9";
    protected static $hashIv = "v77hoKGq4kWxNNIS";  

    /**
     * 綠界AES解密
     * return JSON string
     */
    public static function ecpayAesDecrypt($data)
    {
        $key = EcpayEncryptDecryptTrait::$hashKey;
        $iv = EcpayEncryptDecryptTrait::$hashIv;

        // AES解密
        $decryptString = openssl_decrypt(base64_decode($data), 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);

        // URLDecode
        $response = urldecode($decryptString);

        return $response;
    }
}
