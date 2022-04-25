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
                    <li class="breadcrumb-item"><a href="{{route('productIndex',['categoryId'=>0])}}">商品管理</a></li>
                    <li class="breadcrumb-item active" aria-current="page">編輯商品</li>
                </ol>
            </nav>
        </div>
    </div>
    <hr />
    <div class="row mx-auto">
        <!-- 商品資料 -->
        <div class="card col-5">
            <div class="card-body ">
                <label for="inputFirstName" class="form-label fs-2 d-flex justify-content-center">商品資料</label>
                <div class="col-12">
                    <label for="inputFirstName" class="form-label fs-4">商品名稱</label>
                    <input type="email" class="form-control" id="inputFirstName">
                </div>
                <div class="col-12 mt-3">
                    <label for="inputLastName" class="form-label fs-4">商品分類</label>
                    <select class="form-select" name="category" id="category" onchange=changeSize()>
                        <option value="United States">United States</option>
                    </select>
                </div>
                <div class="col-12 mt-3">
                    <label for="inputEmail" class="form-label fs-4">商品描述</label>
                    <textarea  type="textarea" class="form-control" id="inputEmail"></textarea>
                </div>
                <div class="col-12 d-flex align-items-end justify-content-end mt-4">
                    <a href="{{route('productEdit',['categoryId'=>0])}}" type="button" class="btn btn-outline-primary px-5 "><i class='bx bx-cloud-upload mr-1'></i>更新</a>
                </div>
            </div>
        </div>
        <!-- 商品圖片 -->
        <div class="card col-7">
            <div class="card-body">
                <label for="inputFirstName" class="form-label fs-2 d-flex justify-content-center">商品圖片</label>
                <div class="col-12">
                    <label for="inputFirstName" class="form-label fs-4">商品名稱</label>
                    <input type="email" class="form-control" id="inputFirstName">
                </div>
                <div class="col-12 mt-3">
                    <label for="inputLastName" class="form-label fs-4">商品分類</label>
                    <select class="form-select" name="category" id="category" onchange=changeSize()>
                        <option value="United States">United States</option>
                    </select>
                </div>
                <div class="col-12 mt-3">
                    <label for="inputEmail" class="form-label fs-4">商品描述</label>
                    <textarea  type="textarea" class="form-control" id="inputEmail"></textarea>
                </div>
            </div>
        </div>
        <!-- 商品子項 -->
        <div class="card col-12">
            <div class="card-body">
                <label for="inputFirstName" class="form-label fs-2 d-flex justify-content-center">商品子項</label>
                <div class="col-12">
                    <label for="inputFirstName" class="form-label fs-4">商品名稱</label>
                    <input type="email" class="form-control" id="inputFirstName">
                </div>
                <div class="col-12 mt-3">
                    <label for="inputLastName" class="form-label fs-4">商品分類</label>
                    <select class="form-select" name="category" id="category" onchange=changeSize()>
                        <option value="United States">United States</option>
                    </select>
                </div>
                <div class="col-12 mt-3">
                    <label for="inputEmail" class="form-label fs-4">商品描述</label>
                    <textarea  type="textarea" class="form-control" id="inputEmail"></textarea>
                </div>
            </div>
        </div>
    </div>
        
</div>


@endsection
@section('script')

<script>
    /**************************************抓分類************************************ */
    // 撈全部分類
    const getAllCategory = async () => {
        let response = await axios.post("{{route('categoryAll')}}");
        return response.data.category
    }

    //組合分類selectHtml
    const categoryHtml = async () => {
        let category = await getAllCategory()
        let html = `<option value="">選擇商品分類</option>`
        category.forEach((category, $key) => {
            html += `<option value="${category.categoryId}">${category.categoryName}</option>`
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
    /**************************************抓分類************************************ */
</script>

@endsection