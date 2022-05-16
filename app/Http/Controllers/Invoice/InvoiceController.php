<?php

namespace App\Http\Controllers\Invoice;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;

use App\Http\Traits\ToolTrait;
use App\Http\Traits\Ecpay\InvoiceTrait;
use Ecpay\Sdk\Factories\Factory;


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

    // 查詢字軌
    public function invoiceWordSetting()
    {
        $invoice = InvoiceTrait::getGovInvoiceWordSetting();
        dd($invoice);        
    }

    // 字軌與配號設定
    public function addInvoice()
    {
        $invoice = InvoiceTrait::AddInvoiceWordSetting();
        dd($invoice);
    }
}
