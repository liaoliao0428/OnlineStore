<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\ProductDetailModel;
use App\Http\Traits\ToolTrait;
use App\Http\Traits\SortTrait;

class ProductDetailController extends Controller
{
    use ToolTrait,SortTrait;
    //建構子 middleware設定
    public function __construct()
    {
        $this->middleware('authCheck');
    }

    // 抓商品細項資料
    public function productDetail(Request $request)
    {
        $productId = $request->productId;
        $productDetail = ProductDetailModel::select_product_detail_with_productId_db($productId);
        // 回傳資料
        if(!empty($productDetail)){
            return response()->json(['productDetail' => $productDetail], Response::HTTP_OK);
        }else{
            return response()->json(['productDetail' => '無資料'], Response::HTTP_OK);
        }
    }

    // 商品子項新增
    public function insert(Request $request)
    {
        $productDetail = $request->all();
        $unitPrice = $request->unitPrice;
        // 商品稅額計算
        $caculateTax = $this->caculateTax($unitPrice);

        // 抓要填入的排序
        $productId = $request->productId;
        $producCount = ProductDetailModel::select_product_detail_with_productId_db($productId);
        $productDetail['sort'] = count($producCount) + 1;
        
        $productDetail['productDetailId'] = $this->randomString(13);
        $productDetail['taxType'] = 1;
        $productDetail['unitPriceNoTax'] = $caculateTax['unitPriceNoTax'];
        $productDetail['taxAmount'] = $caculateTax['taxAmount'];
        ProductDetailModel::insert_product_detail_db($productDetail);
        //這句是用來自動排序的 若外來改成新增商品時可選擇排序就會用到 確保排序不會重複
        $this->sortArrange(1,'product_detail','productId','sort','productDetailId',$productDetail['productDetailId'],$oldIdentify=null,$oldSort=null);

        return response()->json(['message' => "商品子項新增成功"], Response::HTTP_OK);
    }

    // 商品子項稅收計算
    public function caculateTax($unitPrice)
    {
        // 商品未稅價
        $unitPriceNoTax = floor($unitPrice/1.05);
        $taxAmount = $unitPrice - $unitPriceNoTax;
        return [
            'unitPriceNoTax'=>$unitPriceNoTax,
            'taxAmount'=>$taxAmount
        ];
    }

    // 商品子項更新
    public function update(Request $request)
    {
        $productDetail = $request->all();
        $productDetailId = $request->productDetailId;
        $unitPrice = $request->unitPrice;
        // 商品稅額計算
        $caculateTax = $this->caculateTax($unitPrice);

        $productDetail['taxType'] = 1;
        $productDetail['unitPriceNoTax'] = $caculateTax['unitPriceNoTax'];
        $productDetail['taxAmount'] = $caculateTax['taxAmount'];
        ProductDetailModel::update_product_detail_db($productDetailId,$productDetail);
        return response()->json(['message' => "更新成功"], Response::HTTP_OK);
    }

    // 商品子項刪除
    public function delete(Request $request)
    {
        $productDetailId = $request->productDetailId;

        // 刪除後其餘商品子項排序調整        
        $this->sortArrange(3,'product_detail','productId','sort','productDetailId',$productDetailId,$oldIdentify=null,$oldSort=null);

        ProductDetailModel::delete_product_detail_db($productDetailId);
        return response()->json(['message' => "商品子項刪除成功"], Response::HTTP_OK);
    }

    // 商品子項上下架
    public function changeEnable(Request $request)
    {
        $productDetailId = $request->productDetailId;
        $enable = $request->enable;
        ProductDetailModel::update_product_detail_enable_db($productDetailId,$enable);
        return response()->json(['message' => "上/下架成功"], Response::HTTP_OK);
    }

    // 商品子項排序調整
    public function changeSort(Request $request)
    {
        $productDetailId = $request->productDetailId;
        $newSort = $request->newSort;
        $oldSort = $request->oldSort;
        ProductDetailModel::update_product_detail_sort_db($productDetailId,$newSort);
        $productDetail = ProductDetailModel::select_product_detail_with_productDetailId_db($productDetailId);
        $productId = $productDetail[0]->productId;
        $this->sortArrange(2,'product_detail','productId','sort','productDetailId',$productDetailId,$productId,$oldSort);
        return response()->json(['message' => "改變排序成功"], Response::HTTP_OK);
    }

}
