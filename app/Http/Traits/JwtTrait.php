<?

namespace App\Http\Traits;

trait JwtTrait
{
    // jwtEncode
    public static function jwtEncode($uid)
    {       
        $header = [
            'typ'=> 'JWT',
            'alg'=> 'HS256',
        ];

        // token核發時間
        $iat = time();
        // token過期時間 預設3小時(變成秒數) 3 * 60 * 60
        $exp = time() + 10800;

        $payload = [
            'uid' => $uid,
            'iat' => $iat,
            'exp' => $exp
        ];

        $header_payload = base64_encode(json_encode($header)) . "." . base64_encode(json_encode($payload));

        $secretKey = env('JWT_SECRET_KEY');
        $signature = hash_hmac('sha256', $header_payload, $secretKey);

        $accesstoken = $header_payload . "." . base64_encode($signature);

        return $accesstoken;
    }

    // jwtDecode
    public static function jwtDecode($accessToken)
    {
        // 將accessToken分解 如果不是三個部分 false
        $tokens = explode('.', $accessToken);
        if (count($tokens) != 3){
            return false;
        }

        // 用list將要解析的三個部分拆分
        list($base64header, $base64payload, $signature) = $tokens;       

        // 取得jwt算法 如果沒有alg false
        $base64decodeheader = json_decode(base64_decode($base64header),true);
        if (empty($base64decodeheader['alg'])){
            return false;
        }
 
        // $signature 簽章驗證
        $header_payload = $base64header . '.' . $base64payload;
        $requestSignature = JwtTrait::signatureCheck($base64decodeheader['alg'] , $header_payload);
        if ($requestSignature !== $signature){
            return false;
        }         

        $payload = json_decode(base64_decode($base64payload), true);

        // 簽證核發時間大於當前時間 代表簽證是錯的 false
        if(isset($payload['exp']) && $payload['iat'] > time()){
            return false;
        }

        // 簽證過期時間小於當前時間 代表簽證已過期 false
        if(isset($payload['exp']) && $payload['exp'] < time()){
            return false;
        }

        return $payload;
    }

    // 簽章驗證
    public static function signatureCheck($alg , $header_payload)
    {
        $secretKey = '123456789';   // env有時候取不到  只能放這邊了

        $alg_config = [
            'HS256'=>'sha256'
        ];            

        return base64_encode(hash_hmac($alg_config[$alg] , $header_payload , $secretKey));
    }
}
