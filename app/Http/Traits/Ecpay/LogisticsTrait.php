<?

namespace App\Http\Traits\Ecpay;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Ecpay\Sdk\Factories\Factory;


trait LogisticsTrait
{
    // (B2C)測試標籤資料產生
    public function express()
    {
        $factory = new Factory([
            'hashKey' => '5294y06JbISpM5x9',
            'hashIv' => 'v77hoKGq4kWxNNIS',
            'hashMethod' => 'md5',
        ]);
        $autoSubmitFormService = $factory->create('AutoSubmitFormWithCmvService');
    
        $input = [
            'MerchantID' => '2000132',  //廠商編號
            'LogisticsSubType' => 'FAMI',   //物流子類型
    
            // 請參考 example/Logistics/Domestic/GetCreateTestDataResponse.php 範例開發
            'ClientReplyURL' => 'https://www.ecpay.com.tw/example/client-reply',
    
        ];
        $action = 'https://logistics-stage.ecpay.com.tw/Express/CreateTestData';
    
        echo $autoSubmitFormService->generate($input, $action);
    }

    // 門市電子地圖
    public function map()
    {
        $factory = new Factory([
            'hashKey' => '5294y06JbISpM5x9',
            'hashIv' => 'v77hoKGq4kWxNNIS',
            'hashMethod' => 'md5',
        ]);
        $autoSubmitFormService = $factory->create('AutoSubmitFormWithCmvService');
    
        $input = [
            'MerchantID' => '2000132',  //廠商編號
            'MerchantTradeNo' => 'Test' . time(),   //廠商交易編號
            'LogisticsType' => 'CVS',   //物流類型 CVS->超商取貨
            'LogisticsSubType' => 'FAMI',   //物流子類型
            'IsCollection' => 'N',  //是否代收貨款
    
            // 請參考 example/Logistics/Domestic/GetMapResponse.php 範例開發
            'ServerReplyURL' => 'https://www.ecpay.com.tw/example/server-reply',    //Server 端回覆網址
        ];
        $action = 'https://logistics-stage.ecpay.com.tw/Express/map';
    
        echo $autoSubmitFormService->generate($input, $action);
    }

    // 門市訂單建立 超商取貨 api類型
    public function createCvs()
    {
        // api類型??
        $factory = new Factory([
            'hashKey' => '5294y06JbISpM5x9',
            'hashIv' => 'v77hoKGq4kWxNNIS',
            'hashMethod' => 'md5',
        ]);
        $postService = $factory->create('PostWithCmvEncodedStrResponseService');
    
        $input = [
            'MerchantID' => '2000132',
            'MerchantTradeNo' => 'Test' . time(),
            'MerchantTradeDate' => date('Y/m/d H:i:s'),
            'LogisticsType' => 'CVS',
            'LogisticsSubType' => 'FAMI',
            'GoodsAmount' => 1000,
            'GoodsName' => '綠界 SDK 範例商品',
            'SenderName' => '陳大明',
            'SenderCellPhone' => '0911222333',
            'ReceiverName' => '王小美',
            'ReceiverCellPhone' => '0933222111',
    
            // 請參考 example/Logistics/Domestic/GetLogisticStatueResponse.php 範例開發
            'ServerReplyURL' => 'https://www.ecpay.com.tw/example/server-reply',
    
            // 請參考 example/Logistics/Domestic/GetMapResponse.php 範例取得
            'ReceiverStoreID' => '006598',
        ];
        $url = 'https://logistics-stage.ecpay.com.tw/Express/Create';
    
        $response = $postService->post($input, $url);
        var_dump($response);
    }

    // 門市訂單建立 超商取貨 form表單類型
    public function createCvsForm()
    {
        // form類型
        $factory = new Factory([
            'hashKey' => '5294y06JbISpM5x9',
            'hashIv' => 'v77hoKGq4kWxNNIS',
            'hashMethod' => 'md5',
        ]);
        $autoSubmitFormService = $factory->create('AutoSubmitFormWithCmvService');
    
        $input = [
            'MerchantID' => '2000132',
            'MerchantTradeNo' => 'Test' . time(),
            'MerchantTradeDate' => date('Y/m/d H:i:s'),
            'LogisticsType' => 'CVS',
            'LogisticsSubType' => 'FAMI',
            'GoodsAmount' => 1000,
            'GoodsName' => '綠界 SDK 範例商品',
            'SenderName' => '陳大明',
            'SenderCellPhone' => '0911222333',
            'ReceiverName' => '王小美',
            'ReceiverCellPhone' => '0933222111',
    
            // 請參考 example/Logistics/Domestic/GetLogisticStatueResponse.php 範例開發
            'ServerReplyURL' => 'https://www.ecpay.com.tw/example/server-reply',
            'ClientReplyURL' => 'https://www.ecpay.com.tw/example/client-reply',
    
            // 請參考 example/Logistics/Domestic/GetMapResponse.php 範例取得
            'ReceiverStoreID' => '006598'
        ];
        $action = 'https://logistics-stage.ecpay.com.tw/Express/Create';
    
        echo $autoSubmitFormService->generate($input, $action);
    }

    // 門市訂單建立 宅配 api 類型
    public function createHome()
    {
        $factory = new Factory([
            'hashKey' => '5294y06JbISpM5x9',
            'hashIv' => 'v77hoKGq4kWxNNIS',
            'hashMethod' => 'md5',
        ]);
        $postService = $factory->create('PostWithCmvEncodedStrResponseService');
    
        $input = [
            'MerchantID' => '2000132',
            'MerchantTradeNo' => 'Test' . time(),
            'MerchantTradeDate' => date('Y/m/d H:i:s'),
            'LogisticsType' => 'HOME',
            'LogisticsSubType' => 'TCAT',
            'GoodsAmount' => 1000,
            'GoodsName' => '綠界 SDK 範例商品',
            'SenderName' => '陳大明',
            'SenderCellPhone' => '0911222333',
            'SenderZipCode' => '11560',
            'SenderAddress' => '台北市南港區三重路19-2號6樓',
            'ReceiverName' => '王小美',
            'ReceiverCellPhone' => '0933222111',
            'ReceiverZipCode' => '11560',
            'ReceiverAddress' => '台北市南港區三重路19-2號6樓',
            'Temperature' => '0001',
            'Distance' => '00',
            'Specification' => '0001',
            'ScheduledPickupTime' => '4',
            'ScheduledDeliveryTime' => '4',
    
            // 請參考 example/Logistics/Domestic/GetLogisticStatueResponse.php 範例開發
            'ServerReplyURL' => 'https://www.ecpay.com.tw/example/server-reply',
        ];
        $url = 'https://logistics-stage.ecpay.com.tw/Express/Create';
    
        $response = $postService->post($input, $url);
        var_dump($response);
    }

    // 門市訂單建立 宅配 form表單類型
    public function createHomeForm()
    {
        $factory = new Factory([
            'hashKey' => '5294y06JbISpM5x9',
            'hashIv' => 'v77hoKGq4kWxNNIS',
            'hashMethod' => 'md5',
        ]);
        $autoSubmitFormService = $factory->create('AutoSubmitFormWithCmvService');
    
        $input = [
            'MerchantID' => '2000132',
            'MerchantTradeNo' => 'Test' . time(),
            'MerchantTradeDate' => date('Y/m/d H:i:s'),
            'LogisticsType' => 'HOME',
            'LogisticsSubType' => 'TCAT',
            'GoodsAmount' => 1000,
            'GoodsName' => '綠界 SDK 範例商品',
            'SenderName' => '陳大明',
            'SenderCellPhone' => '0911222333',
            'SenderZipCode' => '11560',
            'SenderAddress' => '台北市南港區三重路19-2號6樓',
            'ReceiverName' => '王小美',
            'ReceiverCellPhone' => '0933222111',
            'ReceiverZipCode' => '11560',
            'ReceiverAddress' => '台北市南港區三重路19-2號6樓',
            'Temperature' => '0001',
            'Distance' => '00',
            'Specification' => '0001',
            'ScheduledPickupTime' => '4',
            'ScheduledDeliveryTime' => '4',
    
            // 請參考 example/Logistics/Domestic/GetLogisticStatueResponse.php 範例開發
            'ServerReplyURL' => 'https://www.ecpay.com.tw/example/server-reply',
            'ClientReplyURL' => 'https://www.ecpay.com.tw/example/client-reply',
        ];
        $action = 'https://logistics-stage.ecpay.com.tw/Express/Create';
    
        echo $autoSubmitFormService->generate($input, $action);
    }

    // 物流訂單查詢
    public function QueryLogisticsTradeInfo()
    {
        $factory = new Factory([
            'hashKey' => '5294y06JbISpM5x9',
            'hashIv' => 'v77hoKGq4kWxNNIS',
            'hashMethod' => 'md5',
        ]);
        $postService = $factory->create('PostWithCmvVerifiedEncodedStrResponseService');
    
        $input = [
            'MerchantID' => '2000132',
            'AllPayLogisticsID' => '1718546',
            'TimeStamp' => time(),
        ];
        $url = 'https://logistics-stage.ecpay.com.tw/Helper/QueryLogisticsTradeInfo/V2';
        
        $response = $postService->post($input, $url);
        var_dump($response);
    }

    // 訂單取消
    public function cancelC2cOrder()
    {
        $factory = new Factory([
            'hashKey' => 'XBERn1YOvpM9nfZc',
            'hashIv' => 'h1ONHk4P4yqbl5LK',
            'hashMethod' => 'md5',
        ]);
        $postService = $factory->create('PostWithCmvStrResponseService');
    
        $input = [
            'MerchantID' => '2000933',
            'AllPayLogisticsID' => '1718552',
            'CVSPaymentNo' => 'C9681067',
            'CVSValidationNo' => '2448',
        ];
        $url = 'https://logistics-stage.ecpay.com.tw/Express/CancelC2COrder';
    
        $response = $postService->post($input, $url);
        var_dump($response);
    }
}
