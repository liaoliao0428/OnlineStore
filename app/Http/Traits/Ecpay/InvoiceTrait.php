<?

namespace App\Http\Traits\Ecpay;

use Illuminate\Http\Request;
use Ecpay\Sdk\Factories\Factory;


trait InvoiceTrait
{
    // 打api
    public static function ecpayInvoice($data,$url)
    {
        $factory = new Factory([
            'hashKey' => env('ECPAY_INVOICE_HASHKEY'),
            'hashIv' => env('ECPAY_INVOICE_HASHIV'),
        ]);
        $postService = $factory->create('PostWithAesJsonResponseService');

        $input = [
            'MerchantID' => env('ECPAY_MERCHANTID'),
            'RqHeader' => [
                'Timestamp' => time(),
                'Revision' => '3.0.0',
            ],
            'Data' => $data,
        ];

        $response = $postService->post($input, $url);
        return $response;
    }

    // 查詢字軌
    public function getGovInvoiceWordSetting()
    {        
        $data = [
            'MerchantID' => env('ECPAY_MERCHANTID'),
            'InvoiceYear' => '111', //發票年份
        ];
        $url = 'https://einvoice-stage.ecpay.com.tw/B2CInvoice/GetGovInvoiceWordSetting';
        
        $response = InvoiceTrait::ecpayInvoice($data,$url);
        return $response;
    }

    // 字軌與配號設定
    public function AddInvoiceWordSetting()
    {
        $data = [
            'MerchantID' => env('ECPAY_MERCHANTID'),
            'InvoiceTerm' => '1',   // 發票期別 0->1月~2月、1->3月~4月 以此類推到12月
            'InvoiceYear' => '109', //發票年份
            'InvType' => '07',  //字軌類別
            'InvoiceCategory' => '1',   //發票種類 固定寫1
            'InvoiceHeader' => 'TW',    //發票字軌 
            'InvoiceStart' => '10000000',   //起始發票編號
            'InvoiceEnd' => '10000049', //結束發票編號
        ];
        $url = 'https://einvoice-stage.ecpay.com.tw/B2CInvoice/AddInvoiceWordSetting';
        
        $response = InvoiceTrait::ecpayInvoice($data,$url);
        return $response;
    }

    // 設定字軌號碼狀態
    public function updateInvoiceWordStatus()
    {
        $data = [
            'MerchantID' => env('ECPAY_MERCHANTID'),
            'TrackID' => '1234567890',  // 字軌號碼 ID S
            'InvoiceStatus' => '2', //發票字軌狀態 0->停用、1->暫停、2->啟用
        ];
        $url = 'https://einvoice-stage.ecpay.com.tw/B2CInvoice/AddInvoiceWordSetting';
        
        $response = InvoiceTrait::ecpayInvoice($data,$url);
        return $response;
    }

    // 開立發票
    public function Issue()
    {
        $data = [
            "MerchantID" => env('ECPAY_MERCHANTID'),
            "RelateNumber" => "20181028000000001",
            "CustomerID" => "",
            "CustomerIdentifier" => "",
            "CustomerName" => "綠界科技股份有限公司",
            "CustomerAddr" => "106 台北市南港區發票一街 1 號 1 樓",
            "CustomerPhone" => "",
            "CustomerEmail" => "test@ecpay.com.tw",
            "ClearanceMark" => "1",
            "Print" => "1",
            "Donation" => "0",
            "LoveCode" => "",
            "CarrierType" => "",
            "CarrierNum" => "",
            "TaxType" => "1",
            "SalesAmount" => 100,
            "InvoiceRemark" => "發票備註",
            "InvType" => "07",
            "vat" => "1",
            "items" => [
                [
                    "ItemSeq" => 1,
                    "ItemName" => "item02",
                    "ItemCount" => 1,
                    "ItemWord" => "個",
                    "ItemPrice" => 20,
                    "ItemTaxType" => "1",
                    "ItemAmount" => 20,
                    "ItemRemark" => "item02_desc"
                ],
            ],
        ];
        $url = 'https://einvoice-stage.ecpay.com.tw/B2CInvoice/Issue';
        
        $response = InvoiceTrait::ecpayInvoice($data,$url);
        return $response;
    }

    // 查詢發票明細
    public function GetIssue()
    {
        $data = [
            "MerchantID" => env('ECPAY_MERCHANTID'),
            "InvoiceNo" => "AA123456",
            "InvoiceDate" => "2018-10-28",
        ];
        $url = 'https://einvoice-stage.ecpay.com.tw/B2CInvoice/GetIssue';
        
        $response = InvoiceTrait::ecpayInvoice($data,$url);
        return $response;
    }

    
}
