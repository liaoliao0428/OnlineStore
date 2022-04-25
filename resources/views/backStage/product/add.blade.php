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
                    <li class="breadcrumb-item active" aria-current="page">新增商品</li>
                </ol>
            </nav>
        </div>
    </div>
    <hr />
    <!--breadcrumb-->
    <!-- <div class="row">
        <div class="col-4 row">
            <div class="col-6">
                <select class="form-select radius-30 " name="category" id="category" onchange=changeSize()>
                </select>
            </div>
        </div>
        <div class="col-8 text-end">
            <a href="" type="button" class="btn btn-outline-secondary px-5 mb-3 radius-30"><i class='bx bx-cloud-upload mr-1'></i>新增商品</a>
        </div>
    </div> -->
    <div class="d-flex justify-content-center">
        <div class="card col-6">
            <div class="card-body">
                <div class="col-12">
                    <label for="inputFirstName" class="form-label fs-3">商品名稱</label>
                    <input type="email" class="form-control" id="inputFirstName">
                </div>
                <div class="col-12 mt-3">
                    <label for="inputLastName" class="form-label fs-3">商品分類</label>
                    <select class="form-select" name="category" id="category" onchange=changeSize()>
                        <option value="United States">United States</option>
                    </select>
                </div>
                <div class="col-12 mt-3">
                    <label for="inputEmail" class="form-label fs-3">商品描述</label>
                    <textarea  type="textarea" class="form-control" id="inputEmail"></textarea>
                </div>
                <div class="col-12 d-flex align-items-end justify-content-center mt-4">
                    <a href="{{route('productEdit',['categoryId'=>0])}}" type="button" class="btn btn-outline-success px-5 "><i class='bx bx-cloud-upload mr-1'></i>送出</a>
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