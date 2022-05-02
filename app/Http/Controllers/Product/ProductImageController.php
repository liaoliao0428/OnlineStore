<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\ProductImageModel;
use App\Http\Traits\ToolTrait;
use App\Http\Traits\SortTrait;
use Illuminate\Support\Facades\Cookie;
class ProductImageController extends Controller
{
    use ToolTrait,SortTrait;
    //建構子 middleware設定
    public function __construct()
    {
        $this->middleware('authCheck');
    }

    // 抓商品圖片
    public function productImage(Request $request)
    {
        $productId = $request->productId;
        $productImage = ProductImageModel::select_product_image_with_productId_db($productId);
        $imageCount = count($productImage);
        $productImage['imageCount'] = $imageCount;
        $productImage['host'] = $request->getSchemeAndHttpHost();
        // 回傳資料
        if(!empty($productImage)){
            return response()->json(['productImage' => $productImage], Response::HTTP_OK);
        }else{
            return response()->json(['productImage' => '無資料'], Response::HTTP_OK);
        }
    }
    
    // 商品圖片上傳
    public function upload(Request $request)
    {
        $file = $request->file('productImage');
        $productId = $request->productId;
        $filePath = "productImage/" . $productId;
        $fileName = $this->fileAction(1, $file, $filePath); //檔案動作(上傳刪除更新判斷) 回傳檔案名稱
        $this->insert($fileName,$productId);
    }

    // 商品圖片資訊寫入
    public function insert($fileName,$productId)
    {
        $productImage = ProductImageModel::select_product_image_with_productId_db($productId);
        $imageCount = count($productImage);
        $roductImage['productId'] = $productId;
        $roductImage['imageId'] = $this->randomString(13);
        $roductImage['image'] = $fileName;
        $roductImage['sort'] = $imageCount+1;
        ProductImageModel::insert_product_image_db($roductImage);
        return response()->json(['message' => "商品圖片新增成功"], Response::HTTP_OK);
    }

    // 商品圖片刪除
    public function delete(Request $request)
    {
        $productId = $request->productId;
        $imageId = $request->imageId;
        $image = $request->image;
        // 自動排序 確保排序不會重複
        $this->sortArrange(3,'product_image','productId','sort','imageId',$imageId,$oldIdentify=null,$oldSort=null);

        ProductImageModel::delete_product_image_db($image);
        $filePath = "productImage/" . $productId;
        // 將指定刪除的圖片的原檔刪除
        $this->fileAction(3, null, $filePath, $image); //1->上傳、2->更新、3->刪除

        return response()->json(['message' => "商品圖片刪除成功"], Response::HTTP_OK);
    }

    // 商品圖片排序調整
    public function changeSort(Request $request)
    {
        $productId = $request->productId;
        $imageId = $request->imageId;
        $newSort = $request->newSort;
        $oldSort = $request->oldSort;
        ProductImageModel::update_product_image_sort_db($imageId,$newSort);
        // 自動排序 確保排序不會重複
        $this->sortArrange(2,'product_image','productId','sort','imageId',$imageId,$productId,$oldSort);
        return response()->json(['message' => "改變排序成功"], Response::HTTP_OK);
    }    
}
