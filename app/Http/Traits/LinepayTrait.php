<?

namespace App\Http\Traits;

use GuzzleHttp\Client;

use App\Models\OrderModel;
use App\Models\OrderDetailModel;

use App\Models\ProductDetailModel;

trait LinepayTrait
{
    // linepay結帳
    public static function checkout($orderNumber)
    {       
        $orderDetailsArray = LinepayTrait::setLinepayOrderDetailsArray($orderNumber);

        $amount = $orderDetailsArray['amount'];

        $channelId = env('LINE_CHANNEL_ID');
        $key = env('CHANNEL_SECRET_KEY');
        $requestUrl = '/v3/payments/request';

        $order = [
            'amount' => $amount,
            'currency' => 'TWD',
            'orderId' => $orderNumber, //不可重複
            'packages' => [
                [
                    'id' => 'xxxx' . time(),
                    'amount' => $amount,
                    'name' => 'xxxxStore',
                    'products' => $orderDetailsArray['orderDetailsArray'],
                ],
            ],
            'redirectUrls' => [
                'confirmUrl' => env('NGROK_TEST_DOMAIN') . '/OnlineStore/Backend/public/api/frontend/checkout/linepayConfirm/' . $orderNumber . '/' . $amount,
                'cancelUrl' => 'http://localhost:3000/user/order',
            ],
        ];
        $nonce = LinepayTrait::create_uuid();

        $data = $key . $requestUrl . json_encode($order) . $nonce;
        $hash = hash_hmac('sha256', $data, $key, true);

        $hmacBase64 = base64_encode($hash);

        $headers =  [
            'Content-Type' => 'application/json',
            'X-LINE-ChannelId' => $channelId,
            'X-LINE-Authorization-Nonce' => $nonce,
            'X-LINE-Authorization' => $hmacBase64,
        ];

        // 打linepay api
        $url = env('LINE_URL') . $requestUrl;
        $client = new Client();
        $result = $client->post($url, [
            'headers' => $headers,
            'json' => $order
        ]);

        $body = $result->getBody();
        $linepayResponse = json_decode($body,true);

        return [
            'payType' => 2,
            'redirecturl' => $linepayResponse['info']['paymentUrl']['web']
        ];
    }

    // 組合linepay productDetailArray
    public static function setLinepayOrderDetailsArray($orderNumber)
    {
        $order = OrderModel::select_order_where_orderNumber_db($orderNumber);
        $orderDetails = OrderDetailModel::select_order_detail_db($orderNumber);

        $orderDetailsArray = [];

        // 陣列裡先加運費
        $deliveryFee['name'] = '運費';
        $deliveryFee['quantity'] = 1;
        $deliveryFee['price'] = 60;
        $orderDetailsArray[] = $deliveryFee;

        foreach($orderDetails as $orderDetail){
            $quantity = $orderDetail->quantity;
            $unitPrice = $orderDetail->unitPrice;

            $productDetailId = $orderDetail->productDetailId;
            $productDetail = ProductDetailModel::select_product_detail_with_productDetailId_db($productDetailId);
            $productDetailName = $productDetail[0]->productDetailName;

            $orderProductDetail['name'] = $productDetailName;
            $orderProductDetail['quantity'] = $quantity;
            $orderProductDetail['price'] = $unitPrice;

            $orderDetailsArray[] = $orderProductDetail;
        }

        return [
            'amount' => $order[0]->amount,
            'orderDetailsArray' => $orderDetailsArray
        ];
    }    

    // linepay 付款確認 要打到這支api 確認完付款後交易紀錄才會進到linepay後台
    public static function confirm($transactionId , $amount)
    { 
        $channelId = env('LINE_CHANNEL_ID');
        $key = env('CHANNEL_SECRET_KEY');
        $requestUrl = '/v3/payments/' . $transactionId . '/confirm';
        $nonce = LinepayTrait::create_uuid();

        $body = [
            'amount' => $amount,
            'currency' => 'TWD',
        ];

        $data = $key . $requestUrl . json_encode($body) . $nonce;
        $hash = hash_hmac('sha256', $data, $key, true);
        $hmacBase64 = base64_encode($hash);

        $headers =  [
            'Content-Type' => 'application/json',
            'X-LINE-ChannelId' => $channelId,
            'X-LINE-Authorization-Nonce' => $nonce,
            'X-LINE-Authorization' => $hmacBase64,
        ];

        // 打linepay api
        $url = env('LINE_URL') . $requestUrl;
        $client = new Client();
        $result = $client->post($url, [
            'headers' => $headers,
            'json' => $body
        ]);

        $body = $result->getBody();
        $linepayResponse = json_decode($body,true);
        if($linepayResponse['returnCode'] === '0000'){
            return true;
        }
    }

    // linepay uuid
    public static function create_uuid($prefix = "")
    {
        $chars = md5(uniqid(mt_rand(), true));
        $uuid = substr($chars, 0, 8) . '-'
        . substr($chars, 8, 4) . '-'
        . substr($chars, 12, 4) . '-'
        . substr($chars, 16, 4) . '-'
        . substr($chars, 20, 12);
        return $prefix . $uuid;
    } 
}