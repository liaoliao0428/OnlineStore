<?

namespace App\Http\Traits\Ecpay;

use Ecpay\Sdk\Factories\Factory;
use Ecpay\Sdk\Services\UrlService;

trait PaymentTrait
{
    // 綠界特店編號
    protected static $MerchantID = "2000132";    
    // 綠界發票參數
    protected static $hashKey = "5294y06JbISpM5x9";
    protected static $hashIv = "v77hoKGq4kWxNNIS";    

    // 打api
    public static function ecpayPayment( $input , $action )
    {
        $factory = new Factory([
            'hashKey' => PaymentTrait::$hashKey,
            'hashIv' => PaymentTrait::$hashIv,
        ]);
        $autoSubmitFormService = $factory->create('AutoSubmitFormWithCmvService');

        echo $autoSubmitFormService->generate($input, $action);
    }

    // 建立訂單
    public static function aioCheckOut($orderNumber , $totalPrice , $itemName ,  $clientBackUrl)
    {
        // $returnUrl = 'http://192.168.1.106/OnlineStore/Backend/public/api/checkout/ecpayPaymentCheckoutResponse'; // 訂單付款狀態response
        $returnUrl = env('ECPAY_RETURN_URL_DOMAIN') . '/OnlineStore/Backend/public/api/frontend/checkout/ecpayPaymentCheckoutResponse'; // 訂單付款狀態response 現在使用ngrok 之後上線改正式

        $input = [
            'MerchantID' => PaymentTrait::$MerchantID,
            'MerchantTradeNo' => $orderNumber,
            'MerchantTradeDate' => date('Y/m/d H:i:s'),
            'PaymentType' => 'aio',
            'TotalAmount' => $totalPrice,
            'TradeDesc' => UrlService::ecpayUrlEncode('交易描述範例'),
            'ItemName' => $itemName,
            'ReturnURL' => $returnUrl,
            'ChoosePayment' => 'Credit',
            'EncryptType' => 1,
            'ClientBackURL' => $clientBackUrl
        ];
        $action = 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5';

        PaymentTrait::ecpayPayment( $input , $action );
    }
}
