<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Traits\ToolTrait;
use App\Http\Traits\SortTrait;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;

use App\Models\ProductModel;
use App\Models\ProductDetailModel;
use App\Models\ProductImageModel;

class ProductController extends Controller
{
    use ToolTrait,SortTrait;
    //建構子 middleware設定
    public function __construct()
    {
        $this->middleware('authCheck');
    }
    
    /*********************************************view************************************************ */
    // 首頁畫面
    public function index()
    {  
        return view('backstage.product.index'); 
    }

    // 新增商品畫面
    public function add()
    {  
        return view('backstage.product.add'); 
    }

    // 編輯商品畫面
    public function edit()
    {  
        return view('backstage.product.edit'); 
    }
    /*********************************************view************************************************ */

    //取得商品 有分類取分類 有指定取指定 沒指定拿全部
    public function product(Request $request)
    {
        $categoryId = $request->categoryId;
        $productId = $request->productId;

        // 撈指定分類商品
        if($categoryId == 0){   // categoryId != 0 撈全部
            $product = null;
        }else{
            $productsMain = ProductModel::select_product_with_categoryId_db($categoryId);
        }

        // 如果有productId 就撈指定的
        if(!empty($productId)){
            $productsMain = ProductModel::select_product_with_productId_db($productId);
        }
        
        $host = $request->getSchemeAndHttpHost();
        $product = $this->productArray($productsMain,$host);

        // 回傳資料
        if(!empty($product)){
            return response()->json(['product' => $product], Response::HTTP_OK);
        }else{
            return response()->json(['product' => '無資料'], Response::HTTP_OK);
        }
    }

    // 商品陣列組合
    public function productArray($productsMain,$host)
    {
        $product = array();
        foreach($productsMain as $productMain){
            $productId = $productMain->productId;
            // 取得商品細項
            $productDetail = ProductDetailModel::select_product_detail_with_productId_db($productId);
            // 商品細項塞入商品主檔
            $productMain->productDetail = $productDetail;

            // 取得商品圖片
            $productImage = ProductImageModel::select_product_image_with_productId_one_db($productId);
            // 商品細項塞入商品主檔
            if($productImage){
                $productMain->productImage = $productImage[0]->image;
            }else{
                $productMain->productImage = null;
            }            

            // 取得圖片host
            $productMain->host = $host;

            $product[] = $productMain;  
        }
        return $product;
    }

    //商品新增
    public function insert(Request $request)
    {
        $product = $request->all();
        unset($product['_token']);
        $product['productId'] = $this->randomString(13);
        $categoryId = $request->categoryId;
        // 計算本比資料的排序
        $producCount = ProductModel::select_product_with_categoryId_db($categoryId);
        $product['sort'] = count($producCount) + 1;
        ProductModel::insert_product_db($product);
        //這句是用來自動排序的 若外來改成新增商品時可選擇排序就會用到 確保排序不會重複
        $this->sortArrange(1,'product','categoryId','sort','productId', $product['productId'],$oldIdentify=null,$oldSort=null); 

        return redirect()->route('productEdit',['productId'=>$product['productId']]);
    }

    //商品更新
    public function update(Request $request)
    {
        $product = $request->all();
        $productId = $request->productId;
        $categoryId = $request->categoryId;
        $oldCategoryId = $request->oldCategoryId;
        $oldSort = $request->oldSort;

        unset($product['oldCategoryId']);   //這兩個不用更新 變數取出後就刪掉
        unset($product['oldSort']); //這兩個不用更新 變數取出後就刪掉

        // 更新後分類不同代表移動到不同分類 計算本比資料的排序
        if($categoryId !== $oldCategoryId){
            $producCount = ProductModel::select_product_with_categoryId_db($categoryId);
            $product['sort'] = count($producCount) + 1;
        }        
        ProductModel::update_product_db($productId,$product);
        
        //這句是用來自動排序的 若外來改成新增商品時可選擇排序就會用到 確保排序不會重複
        $this->sortArrange(2,'product','categoryId','sort','productId',$productId,$oldCategoryId,$oldSort);

        return response()->json(['message' => "更新成功"], Response::HTTP_OK);
    }

    // 商品刪除
    public function delete(Request $request)
    {
        $productId = $request->productId;
        // 刪除後其餘商品子項排序調整        
        $this->sortArrange(3,'product','categoryId','sort','productId',$productId,$oldIdentify=null,$oldSort=null);

        $filePath = "productImage/" . $productId;
        // 刪除圖片資料夾 會將裡面圖片一同刪除
        storage::deleteDirectory("/public/" . $filePath);

        ProductModel::delete_product_db($productId);
        return response()->json(['message' => "刪除成功"], Response::HTTP_OK);
    }

    // 商品上下架
    public function changeEnable(Request $request)
    {
        $productId = $request->productId;
        $enable = $request->enable;
        ProductModel::update_product_enable_db($productId,$enable);
        return response()->json(['message' => "上/下架成功"], Response::HTTP_OK);
    }

    // 商品排序調整
    public function changeSort(Request $request)
    {
        $productId = $request->productId;
        $newSort = $request->newSort;
        $oldSort = $request->oldSort;
        ProductModel::update_product_sort_db($productId,$newSort);

        $product = ProductModel::select_product_with_productId_db($productId);
        $categoryId = $product[0]->categoryId;
        //這句是用來自動排序的 若外來改成新增商品時可選擇排序就會用到 確保排序不會重複
        $this->sortArrange(2,'product','categoryId','sort','productId',$productId,$categoryId,$oldSort);

        return response()->json(['message' => "改變排序成功"], Response::HTTP_OK);
    }  
}
