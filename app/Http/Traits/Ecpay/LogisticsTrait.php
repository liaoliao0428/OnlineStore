<?

namespace App\Http\Traits\Ecpay;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Ecpay\Sdk\Factories\Factory;


trait LogisticsTrait
{
    // 綠界特店編號
    protected static $MerchantID = "2000132";    
    // 綠界發票參數
    protected static $hashKey = "5294y06JbISpM5x9";
    protected static $hashIv = "v77hoKGq4kWxNNIS";

    // 打api
    public static function ecpayLogistics( $input , $url )
    {
        $factory = new Factory([
            'hashKey' => LogisticsTrait::$hashKey,
            'hashIv' => LogisticsTrait::$hashIv,
        ]);

        $postService = $factory->create('PostWithAesJsonResponseService');

        $response = $postService->post($input, $url);

        return $response;
    }

    // 一段標測試資料產生(B2C)
    public static function createTestData($receiverStoreType)
    {        
        $data = [
            'MerchantID' => LogisticsTrait::$MerchantID,
            'LogisticsSubType' => $receiverStoreType,
        ];

        $input = [
            'MerchantID' => LogisticsTrait::$MerchantID,
            'RqHeader' => [
                'Timestamp' => time(),
                'Revision' => '1.0.0',
            ],
            'Data' => $data,
        ];
        $url = 'https://logistics-stage.ecpay.com.tw/Express/v2/CreateTestData';

        $response = LogisticsTrait::ecpayLogistics( $input , $url );
        return $response;
    }

    // 開啟物流選擇頁
    public static function redirectToLogisticsSelection($clientReplyUrl , $GoodsAmount = 100)
    {
        $factory = new Factory([
            'hashKey' => LogisticsTrait::$hashKey,
            'hashIv' => LogisticsTrait::$hashIv,
        ]);
        $postService = $factory->create('PostWithAesStrResponseService');
        
        $data = [
            'TempLogisticsID' => '0',
            'GoodsAmount' => $GoodsAmount,
            'IsCollection' =>'Y',
            'GoodsName' => '範例商品',
            'SenderName' => '陳大明',
            'SenderZipCode' => '11560',
            'SenderAddress' => '台北市南港區三重路19-2號6樓',
        
            // 請參考 example/Logistics/AllInOne/LogisticsStatusNotify.php 範例開發
            'ServerReplyURL' => 'https://www.ecpay.com.tw/example/server-reply',
        
            // 請參考 example/Logistics/AllInOne/TempTradeEstablishedResponse.php 範例開發
            'ClientReplyURL' => $clientReplyUrl,
            
        ];
        $input = [
            'MerchantID' => '2000132',
            'RqHeader' => [
                'Timestamp' => time(),
                'Revision' => '1.0.0',
            ],
            'Data' => $data,
        ];
        $url = 'https://logistics-stage.ecpay.com.tw/Express/v2/RedirectToLogisticsSelection';
        
        $response = $postService->post($input, $url);
        echo $response['body'];
    }

    // 更新暫時存物流訂單
    public static function updateTempTrade()
    {
        $data = [
            'TempLogisticsID' => 2000140,
            'SenderName' => '王小美',
        ];
        $input = [
            'MerchantID' => '2000132',
            'RqHeader' => [
                'Timestamp' => time(),
                'Revision' => '1.0.0',
            ],
            'Data' => $data,
        ];
        $url = 'https://logistics-stage.ecpay.com.tw/Express/v2/UpdateTempTrade';

        $response = LogisticsTrait::ecpayLogistics( $input , $url );
        return $response;
    }

    // 建立正式物流訂單
    public static function createByTempTrade()
    {
        $data = [
            'TempLogisticsID' => '2264',
        ];
        $input = [
            'MerchantID' => '2000132',
            'RqHeader' => [
                'Timestamp' => time(),
                'Revision' => '1.0.0',
            ],
            'Data' => $data,
        ];
        $url = 'https://logistics-stage.ecpay.com.tw/Express/v2/CreateByTempTrade';

        $response = LogisticsTrait::ecpayLogistics( $input , $url );
        return $response;
    }

    // 查詢訂單
    public function queryLogisticsTradeInfo()
    {
        $data = [
            'MerchantID' => '2000132',
            'LogisticsID' => '1769853',
        ];
        $input = [
            'MerchantID' => '2000132',
            'RqHeader' => [
                'Timestamp' => time(),
                'Revision' => '1.0.0',
            ],
            'Data' => $data,
        ];
        $url = 'https://logistics-stage.ecpay.com.tw/Express/v2/QueryLogisticsTradeInfo';

        $response = LogisticsTrait::ecpayLogistics( $input , $url );
        return $response;
    }

    // 建立綠界物流訂單
    public static function create($orderNumber , $receiverStoreType , $amount , $receiverName , $receiverCellPhone , $receiverStoreID)
    {
        $factory = new Factory([
            'hashKey' => LogisticsTrait::$hashKey,
            'hashIv' => LogisticsTrait::$hashIv,
            'hashMethod' => 'md5',
        ]);
        $postService = $factory->create('PostWithCmvEncodedStrResponseService');
    
        $input = [
            'MerchantID' => '2000132',
            'MerchantTradeNo' => $orderNumber,
            'MerchantTradeDate' => date('Y/m/d H:i:s'),
            'LogisticsType' => 'CVS',
            'LogisticsSubType' => $receiverStoreType,
            'GoodsAmount' => $amount,
            'GoodsName' => '綠界 SDK 範例商品',
            'SenderName' => '陳大明',
            'SenderCellPhone' => '0911222333',
            'ReceiverName' => $receiverName,
            'ReceiverCellPhone' => $receiverCellPhone,
    
            // 請參考 example/Logistics/Domestic/GetLogisticStatueResponse.php 範例開發
            'ServerReplyURL' => 'https://www.ecpay.com.tw/example/server-reply',
    
            // 請參考 example/Logistics/Domestic/GetMapResponse.php 範例取得
            'ReceiverStoreID' => $receiverStoreID,
        ];
        $url = 'https://logistics-stage.ecpay.com.tw/Express/Create';
    
        $response = $postService->post($input, $url);
        return $response;
    }

}