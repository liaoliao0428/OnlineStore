<?php

namespace App\Http\Controllers\Backend\Invoice;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;

use App\Http\Traits\ToolTrait;
use App\Http\Traits\Ecpay\InvoiceTrait;


class InvoiceController extends Controller
{
    use ToolTrait,InvoiceTrait;

    //建構子 middleware設定
    public function __construct()
    {
        $this->middleware('authCheck');
    }

    // 發票首頁
    public function index()
    {
        return view('backstage.invoice.index'); 
    }

    // 查詢字軌使用情況
    public function invoice(Request $request)
    {
        $year = $request->year;
        $invoiceTerm = (int)$request->invoiceTerm;  // 發票期別

        $invoice = InvoiceTrait::getInvoiceWordSetting($year,$invoiceTerm);
        return response()->json(['invoice' => $invoice], Response::HTTP_OK);
    }

    // 查詢財政部配號結果 取下一期的資料
    public function getinvoiceWordSetting(Request $request)
    {
        $year = (string)$request->year;

        $invoice = InvoiceTrait::getGovInvoiceWordSetting($year);
        return response()->json(['invoice' => $invoice], Response::HTTP_OK);
    }

    // 字軌與配號設定
    public function addInvoice(Request $request)
    {
        $invoice['InvoiceYear'] = (string)$request->invoiceYear;
        $invoice['InvoiceTerm'] = (int)$request->invoiceTerm;
        $invoice['InvoiceHeader'] = $request->invoiceHeader;
        $invoice['InvoiceStart'] = $request->invoiceStart;
        $invoice['InvoiceEnd'] = $request->invoiceEnd;

        $response = InvoiceTrait::AddInvoiceWordSetting($invoice);
        return response()->json(['response' => $response], Response::HTTP_OK);
    }

    // 設定字軌號碼狀態
    public function updateInvoicStatus(Request $request)
    {
        $trackId = $request->trackId;
        $invoiceStatus = (int)$request->invoiceStatus;

        $response = InvoiceTrait::updateInvoiceWordStatus($trackId,$invoiceStatus);
        return response()->json(['response' => $response], Response::HTTP_OK);

    }

    /*********************invoice_excel上傳並匯入******************** */
    //發票csv檔匯入上傳
    public function import_invoice_excel_csv(Request $request)
    {
        $file = $request->file('file'); //將檔案存入變數中
        $new_path_name = $this->upload_invoice_excel_csv($file);    //發票excel上傳至資料夾 並回傳excel檔案位置及名稱
        $invoice_excel = $this->read_invoice_excel_csv($new_path_name); //讀取發票excel_csv檔
        return json_encode($invoice_excel);
    }
    //發票excel上傳至資料夾 並回傳excel檔案位置及名稱
    public function upload_invoice_excel_csv($file)
    {
        $file_path = Storage::disk('public')->path('invoice_excel/');   //取得該資料夾位置
		$this->delete_file_path_data($file_path);   //刪除指定資料夾裡面的檔案
        Storage::disk('public')->put('invoice_excel' , $file); //將excel檔案上傳至指定資料夾 dalimarket/storage/app/public/invoice_excel/";
        $old_path_name = $file_path . $file->hashName();    //上傳後的檔名會變成.txt檔，所以要改副檔名變回.csv檔案，先獲取舊的檔名
        $new_path_name = $file_path . "invoice_excel.csv";   //副檔名為.csv新的檔名
        rename("$old_path_name","$new_path_name");    //改檔名
        return $new_path_name;  //回傳excel檔案位置及名稱
    }

    //刪除指定資料夾裡面的檔案
    public function delete_file_path_data($file_path)
    {
        $old_file = glob($file_path.'*');   //掃描資料夾內的檔案
        if(!empty($old_file)){     //如果不是空的代表有東西
            foreach ($old_file as $old_files)   //跑foreach展開
            {   
                unlink($old_files);     //刪除資料夾內的檔案
            }
        }
    }

    //讀取發票excel_csv檔
    public function read_invoice_excel_csv($new_path_name)    
    {
        //$new_path_name放要讀取的excel的檔案位址
        $file = fopen($new_path_name,'r');
        while ($data = fgetcsv($file)) { //每次讀取CSV裡面的一行內容
            $goods_list[] = $data;  //此為一個數組，要獲得每一個數據，訪問陣列下標即可
        }

        $date = $goods_list[1][3];         //時間
        $date_detail = $this->invoice_excel_date_deal($date);   //從發票excel解析出來的時間分析正確的時間

        $invoice_excel['invoiceYear'] = $date_detail['invoiceYear'];      //時間
        $invoice_excel['invoiceTerm'] = $date_detail['invoiceTerm']; //時間
        $invoice_excel['invoiceHeader'] = $goods_list[1][4];  //發票字軌
        $invoice_excel['invoiceStart'] = $goods_list[1][5];  //開始號碼
        $invoice_excel['invoiceEnd'] = $goods_list[1][6];    //結束號碼
        fclose($file);
        return $invoice_excel;  //回傳處理完成後的發票excel_csv檔資料
    }

    //從發票excel解析出來的時間分析正確的時間
    public function invoice_excel_date_deal($date)
    {
        $date_explode = explode(" ",$date); //發票excel內的日期寫的是111/01 ~ 111/02 所以先用explode拆解 空格當分點
        $startTime = $date_explode[0];  //用開始時間來判斷
        $startTime_explode = explode("/",$startTime);   //開始時間拆解出來後為111/01 用explode拆解 "/"當分點
        $invoiceYear = $startTime_explode[0] + 1911;   //政府給的是民國年 系統存的是西元年 所以要+1911
        $month = $startTime_explode[1]; //發票月份
        $invoiceTerm = $this->invoiceTerm($month);   //用已給出的月份判斷invoiceTerm
        return [
            'invoiceYear'=>$invoiceYear,
            'invoiceTerm'=>$invoiceTerm
        ];
    }

    //用已給出時間判斷invoiceTerm
    //01 - 02月 invoiceTerm = 1 、 03 - 04月 invoiceTerm = 3 、 05 - 06月 invoiceTerm = 5 、 07 - 08月 invoiceTerm = 7 、 09 - 10月 invoiceTerm = 9 、 11 - 12月 invoiceTerm = 11
    public function invoiceTerm($month)
    {
        switch($month){
            case '01' :
                $invoiceTerm = 1;
            break;

            case '03' :
                $invoiceTerm = 2;
            break;

            case '05' :
                $invoiceTerm = 3;
            break;

            case '07' :
                $invoiceTerm = 4;
            break;

            case '09' :
                $invoiceTerm = 5;
            break;

            case '11' :
                $invoiceTerm = 6;
            break;
        }
        return $invoiceTerm;
    }
    /*********************invoice_excel上傳並匯入******************** */
}
