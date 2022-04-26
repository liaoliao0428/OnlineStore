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
                    <li class="breadcrumb-item"><a href="javascript:;" onclick="backToProductIndex()">商品管理</a></li>
                    <li class="breadcrumb-item active" aria-current="page">新增商品</li>
                </ol>
            </nav>
        </div>
    </div>
    <hr />
    <div class="d-flex justify-content-center">
        <div class="card col-6">
            <div class="card-body">
                <form action="{{route('productInsert')}}" method="POST" onsubmit="return productSubmitCheck()">
                    @csrf
                    <div class="col-12">
                        <label for="productName" class="form-label fs-3">商品名稱</label>
                        <input name="productName" id="productName" class="form-control" required>
                    </div>
                    <div class="col-12 mt-3">
                        <label for="category" class="form-label fs-3">商品分類</label>
                        <select name="categoryId" id="category" class="form-select">
                        </select>
                    </div>
                    <div class="col-12 mt-3">
                        <label for="description" class="form-label fs-3">商品描述</label>
                        <textarea name="description" id="inputEmail" type="textarea" class="form-control" ></textarea>
                    </div>
                    <div class="col-12 d-flex align-items-end justify-content-center mt-4">
                        <input type="submit" value="新增商品" class="btn btn-outline-success px-5 ">
                    </div>
                </form>
            </div>
        </div>
    </div>    
</div>


@endsection
@section('script')

<script>
    // 從網址取得當前分類id
    const getCategoryIdFromUrl = async () => {
        let url = location.pathname
        let splitUrl = url.split("/");
        let categoryId = splitUrl[5]
        return categoryId
    } 

    /**************************************抓所有分類************************************ */
    // 撈全部分類
    const getAllCategory = async () => {
        let response = await axios.post("{{route('categoryAll')}}");
        return response.data.category
    }

    // 組合分類selectHtml
    const categoryHtml = async () => {
        let category = await getAllCategory()
        let html = `<option value="0">選擇商品分類</option>`
        let categoryId = await getCategoryIdFromUrl() // 從網址取得現在categoryId
        category.forEach((category, $key) => {
            html += categoryId == category.categoryId ? `<option selected value="${category.categoryId}">${category.categoryName}</option>` : `<option value="${category.categoryId}">${category.categoryName}</option>`
        });
        return html
    }

    // categoryHtml 塞入select
    const categoryHtmlInsert = async () => {
        let category = await categoryHtml()
        $('select#category').html(category) //把banner指定給這個function
    }
    
    // 分類html塞入select
    categoryHtmlInsert()
    /**************************************抓所有分類************************************ */

    // 商品新增送出檢查
    const productSubmitCheck = () => {
        let categoryId = $('select#category').val()
        if(categoryId == 0){
            alert("請選擇正確分類")
            return false
        }
    }

    // 回到商品管理頁面
    const backToProductIndex = async () => {
        categoryId = await getCategoryIdFromUrl()
        let url = '{{route("productIndex", ["categoryId"=>":categoryId"])}}';
        url = url.replace(':categoryId', categoryId)
        location.href = url;
    }


</script>

@endsection