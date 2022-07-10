<?

namespace App\Http\Traits\Ecpay;

use Illuminate\Http\Request;
use Ecpay\Sdk\Factories\Factory;


trait InvoiceTrait
{
    // 綠界特店編號
    protected static $MerchantID = "2000132";    
    // 綠界發票參數
    protected static $hashKey = "ejCk326UnaZWKisg";
    protected static $hashIv = "q9jcZX8Ib9LM8wYk";    

    // 打api
    public static function ecpayInvoice($data,$url)
    {
        $factory = new Factory([
            'hashKey' => InvoiceTrait::$hashKey,
            'hashIv' => InvoiceTrait::$hashIv,
        ]);
        $postService = $factory->create('PostWithAesJsonResponseService');

        $input = [
            'MerchantID' => InvoiceTrait::$MerchantID,
            'RqHeader' => [
                'Timestamp' => time(),
                'Revision' => '3.0.0',
            ],
            'Data' => $data,
        ];

        $response = $postService->post($input, $url);
        return $response;
    }

    // 查詢字軌使用情況
    public function getInvoiceWordSetting($year,$invoiceTerm)
    {
        $data = [
            "MerchantID" => InvoiceTrait::$MerchantID,
            "InvoiceTerm" => $invoiceTerm,
            "InvoiceYear" => $year,
            "UseStatus" => 0,
            "InvoiceCategory" => 1,
        ];
        $url = 'https://einvoice-stage.ecpay.com.tw/B2CInvoice/GetInvoiceWordSetting';
        
        $response = InvoiceTrait::ecpayInvoice($data,$url);
        return $response;
    }

    // 查詢財政部配號結果
    public function getGovInvoiceWordSetting($year)
    {        
        $data = [
            'MerchantID' => InvoiceTrait::$MerchantID,
            'InvoiceYear' => $year, //發票年份
        ];
        $url = 'https://einvoice-stage.ecpay.com.tw/B2CInvoice/GetGovInvoiceWordSetting';
        
        $response = InvoiceTrait::ecpayInvoice($data,$url);
        return $response;
    }

    // 字軌與配號設定
    public function AddInvoiceWordSetting($invoice)
    {
        $data = [
            'MerchantID' => InvoiceTrait::$MerchantID,
            'InvoiceTerm' => $invoice['InvoiceTerm'],   // 發票期別 1->1月~2月、2->3月~4月 以此類推到12月
            'InvoiceYear' => $invoice['InvoiceYear'], //發票年份
            'InvType' => '07',  //字軌類別
            'InvoiceCategory' => '1',   //發票種類 固定寫1
            'InvoiceHeader' => $invoice['InvoiceHeader'],    //發票字軌 
            'InvoiceStart' => $invoice['InvoiceStart'],   //起始發票編號
            'InvoiceEnd' => $invoice['InvoiceEnd'], //結束發票編號
        ];
        $url = 'https://einvoice-stage.ecpay.com.tw/B2CInvoice/AddInvoiceWordSetting';
        
        $response = InvoiceTrait::ecpayInvoice($data,$url);
        return $response;
    }

    // 設定字軌號碼狀態
    public function updateInvoiceWordStatus($trackId,$invoiceStatus)
    {
        $data = [
            'MerchantID' => InvoiceTrait::$MerchantID,
            'TrackID' => $trackId,  // 字軌號碼 ID S
            'InvoiceStatus' => $invoiceStatus, //發票字軌狀態 0->停用、1->暫停、2->啟用
        ];
        $url = 'https://einvoice-stage.ecpay.com.tw/B2CInvoice/UpdateInvoiceWordStatus';
        
        $response = InvoiceTrait::ecpayInvoice($data,$url);
        return $response;
    }

    // 開立發票
    public static function Issue()
    {
        $data = [
            "MerchantID" => InvoiceTrait::$MerchantID,
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
            "MerchantID" => InvoiceTrait::$MerchantID,
            "InvoiceNo" => "AA123456",
            "InvoiceDate" => "2018-10-28",
        ];
        $url = 'https://einvoice-stage.ecpay.com.tw/B2CInvoice/GetIssue';
        
        $response = InvoiceTrait::ecpayInvoice($data,$url);
        return $response;
    }  
}
