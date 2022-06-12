<?php

namespace App\Http\Controllers\Frontend\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\CategoryModel;
use App\Http\Traits\ToolTrait;

class CategoryApi extends Controller
{
    use ToolTrait;
    //建構子 middleware設定
    // public function __construct()
    // {
    //     $this->middleware(['authCheck', 'cros']);
    // }

    // 撈全部分類
    public function categoryAll(Request $request)
    {
        $category = CategoryModel::select_category_db();    //撈全部分類
        if($category){
            return response()->json(['category' => $category], Response::HTTP_OK);       
        }else{
            return response()->json(['category' => "無資料"], Response::HTTP_OK);       
        }         
    }

}
