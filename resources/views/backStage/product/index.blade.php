@extends('backstage.layout')
<!-- 引用模板 -->
@section('head')
@endsection
@section('content')
<div class="bg-white p-3">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">首頁</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">商品管理</li>
                </ol>
            </nav>
        </div>
    </div>
    <hr/>
    <!--breadcrumb-->
    <div class="row">
        <div class="col-4 row">
            <div class="col-6">
                <select class="form-select radius-30 " name="category" id="category" onchange=changeCategory()>
                </select>
            </div>
        </div>
        <div class="col-8 text-end">
            <a href="{{route('productAdd',['categoryId'=>0])}}" type="button" class="btn btn-outline-secondary px-5 mb-3 radius-30"><i class='bx bx-cloud-upload mr-1'></i>新增商品</a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body ">
            <!-- 商品列表 -->
            <table class="table table-bordered mb-0" id="product">
                <!-- 外框欄位 -->
                <thead>
                    <tr>
                        <th scope="col" width="5%" class="text-center fs-4">排序</th>
                        <th scope="col" width="25%" class="text-center fs-4">商品圖</th>
                        <th scope="col" width="35%" class="text-center fs-4">商品 - 大便衣服</th>
                        <th scope="col" width="15%" class="text-center fs-4">上下架</th>
                        <th scope="col" width="20%" class="text-center fs-4">操作</th>
                    </tr>
                </thead>
                <!-- 表格內容 -->
                <tbody>
                    <tr class="text-center align-middle">
                        <!-- 排序 -->
                        <th scope="row" style="font-size: 3rem;">1</th>
                        <!-- 商品圖 -->
                        <td><img src="" height ="200"></td></td>
                        <!-- 商品 - 商品名稱 -->
                        <td class="row m-0 p-0 align-items-start flex-grow-1 border-0">
                            <div class="row m-0 p-0 align-items-start">
                                <div class="col-3 p-0 border-bottom border-end fs-4">#</div>
                                <div class="col-3 p-0 border-bottom border-end fs-4">規格</div>
                                <div class="col-3 p-0 border-bottom border-end fs-4">價格</div>
                                <div class="col-3 p-0 border-bottom fs-4">數量</div>
                            </div>
                            <div class="row m-0 p-0 align-items-start">
                                <div class="col-3 p-0 border-bottom border-end fs-5">1</div>  
                                <div class="col-3 p-0 border-bottom border-end fs-5">S</div>
                                <div class="col-3 p-0 border-bottom border-end fs-5">$100</div>
                                <div class="col-3 p-0 border-bottom fs-5">10</div>
                            </div>
                            <div class="row m-0 p-0 align-items-start">
                                <div class="col-3 p-0 border-bottom border-end fs-5">2</div>  
                                <div class="col-3 p-0 border-bottom border-end fs-5">M</div>
                                <div class="col-3 p-0 border-bottom border-end fs-5">$100</div>
                                <div class="col-3 p-0 border-bottom fs-5">6</div>
                            </div> 
                            <div class="row m-0 p-0 align-items-start">
                                <div class="col-3 p-0 border-bottom border-end fs-5">3</div>  
                                <div class="col-3 p-0 border-bottom border-end fs-5">L</div>
                                <div class="col-3 p-0 border-bottom border-end fs-5">$100</div>
                                <div class="col-3 p-0 border-bottom fs-5">10</div>
                            </div>                           
                        </td>
                        <!-- 上下架 -->
                        <td>
                            <div class="form-check form-switch p-0 d-flex justify-content-center">
                                <input class="form-check-input m-0" type="checkbox" id="" onclick="">
                            </div>
                        </td>
                        <!-- 操作 -->
                        <td class="align-middle">
                            <div class="row justify-content-center">
                                <!-- 編輯按鈕 -->
                                <div class="col-2"></div>
                                    <button type="button" class="col-8 btn btn-outline-primary px-5 mb-3 d-block mx-auto" ><i class='bx bx-edit mr-1'></i>編輯</button>
                                <div class="col-2"></div>                                
                                <!-- 刪除按鈕 -->
                                <div class="col-2"></div>
                                    <button type="button" class="col-8 btn btn-outline-danger px-5 mb-3 d-block mx-auto" ><i class='bx bx-trash mr-1'></i>刪除</button>
                                <div class="col-2"></div>                               
                                <!-- 上下移按鈕 -->
                                <div class="col-2"></div>
                                    <button type="button" class="col-3 btn btn-outline-secondary mb-3 d-block text-center" ><i class='bx bx-caret-up-circle m-0'></i></button>
                                <div class="col-2"></div>
                                    <button type="button" class="col-3 btn btn-outline-secondary mb-3 d-block text-center" ><i class='bx bx-caret-down-circle m-0'></i></button>
                                <div class="col-2"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection
@section('script')

<script>
    /**************************************抓分類************************************ */
    // 撈全部分類
    const getAllCategory = async() => {
        let response = await axios.post("{{route('categoryAll')}}");
        return response.data.category
    }

    //組合分類selectHtml
    const categoryHtml = async() => {
        let category = await getAllCategory()
        let html = `<option value="">選擇商品分類</option>`
        let categoryId = getCategoryIdFromUrl() // 從網址取得現在categoryId
        category.forEach((category,$key) => {
            html += categoryId == category.categoryId ? `<option selected value="${category.categoryId}">${category.categoryName}</option>` : `<option value="${category.categoryId}">${category.categoryName}</option>`
        });
        return html
    }

    // categoryHtml 塞入select
    const categoryHtmlInsert = async() => {
        let category = await categoryHtml()
        $('select#category').html(category) //把banner指定給這個function
    }

    // 分類html塞入select
    categoryHtmlInsert()
    /**************************************抓分類************************************ */

    /**************************************抓商品************************************ */
    //取得分類id
    const getCategoryIdFromUrl = () => {
        let url = location.pathname
        let splitUrl = url.split("/");
        let categoryId = splitUrl[5]
        return categoryId
    }

    //撈此分類全部商品資料
    const getProduct = () => {
        let categoryId = getCategoryIdFromUrl()
        // let response = await axios.post("{{route('categoryAll')}}");
    }
    /**************************************抓商品************************************ */

    const changeCategory = () => {
        // {{route('productIndex',['categoryId'=>0])}}
        categoryId = $('select[name="category"]').val()
        let url = '{{route("productIndex", ["categoryId"=>":categoryId"])}}';
        url = url.replace(':categoryId', categoryId)
        location.href = url;
    }





    
</script>

@endsection