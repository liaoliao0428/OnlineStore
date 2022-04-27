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
                    <li class="breadcrumb-item active" aria-current="page">編輯商品</li>
                </ol>
            </nav>
        </div>
    </div>
    <hr />
    <div class="row">
        <!-- 商品資料 -->
        <div class="col-5">
            <div class="card ">
                <div class="card-body ">
                    <label for="inputFirstName" class="form-label fs-2 d-flex justify-content-center">商品資料</label>
                    <div class="col-12">
                        <label for="productName" class="form-label fs-4">商品名稱</label>
                        <input id="productName" name="productName" class="form-control" >
                    </div>
                    <div class="col-12 mt-3">
                        <label for="category" class="form-label fs-4">商品分類</label>
                        <input type="hidden" name="oldCategory" id="oldCategory">
                        <select name="categoryId" id="category" class="form-select">
                        </select>
                    </div>
                    <div class="col-12 ">
                        <label for="description" class="form-label fs-4 ">商品描述</label>
                        <textarea id="description" name="description" type="textarea" class="form-control" ></textarea>
                    </div>
                    <div class="col-12 d-flex mt-4">
                        <div class="col-8 d-flex">
                            <label for="description" class="form-label fs-4 m-0 col-3 d-flex align-items-center justify-content-start">上下架</label>
                            <div class="form-check form-switch p-0 d-flex align-items-center justify-content-start col-9">
                                <input id="enable" name="enable" class="form-check-input m-0" type="checkbox" onclick="changeEnable()">
                            </div>
                        </div>
                        <div class="col-4 d-flex justify-content-end">
                            <button class="btn btn-outline-primary px-5 " onclick="updateProduct()"><i class='bx bx-cloud-upload mr-1'></i>更新</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 商品圖片 -->
        <div class=" col-7">
            <div class="card">
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
        </div>
        <!-- 商品子項 -->
        <div class="col-12">
            <div class="card p-3">
                <label for="inputFirstName" class="form-label fs-2 d-flex justify-content-center">商品子項</label>
                <div class="card">
                    <div class="card-body">
                        <label for="inputFirstName" class="form-label fs-2 d-flex justify-content-start">商品1</label>
                        <div class="col-12">
                            <label for="inputFirstName" class="form-label fs-4">商品名稱</label>
                            <input type="email" class="form-control" id="inputFirstName">
                        </div>
                        <div class="col-12 mt-3">
                            <label for="inputLastName" class="form-label fs-4">商品分類</label>
                            <select class="form-select" name="category" id="category" >
                                <option value="United States">United States</option>
                            </select>
                        </div>
                        <div class="col-12 mt-3">
                            <label for="inputEmail" class="form-label fs-4">商品描述</label>
                            <textarea  type="textarea" class="form-control" id="inputEmail"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card ">
                    <div class="card-body">
                        <label for="inputFirstName" class="form-label fs-2 d-flex justify-content-start">商品2</label>
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
    </div>
</div>

@endsection
@section('script')

<script>
    // 從網址取得當前分類id
    const getProductIdFromUrl = async () => {
        let url = location.pathname
        let splitUrl = url.split("/");
        let productId = splitUrl[5]
        return productId
    }

    /**************************************抓分類************************************ */
    // 撈全部分類
    const getAllCategory = async () => {
        let response = await axios.post("{{route('categoryAll')}}");
        return response.data.category
    }

    // 組合分類selectHtml
    const categoryHtml = async (categoryId) => {
        let category = await getAllCategory()
        let html = ``
        category.forEach((category, $key) => {
            html += categoryId == category.categoryId ? `<option selected value="${category.categoryId}">${category.categoryName}</option>` : `<option value="${category.categoryId}">${category.categoryName}</option>`
        });
        return html
    }

    // categoryHtml 塞入select
    const categoryHtmlInsert = async (categoryId) => {
        let category = await categoryHtml(categoryId)
        $('select#category').html(category) //把banner指定給這個function
    }
    /**************************************抓分類************************************ */

    /**************************************抓商品************************************ */
    // 撈此分類全部商品資料
    const getProduct = async () => {
        let productId = await getProductIdFromUrl()
        let response = await axios.post("{{route('product')}}", {'productId': productId});
        if(response){
            let productName = response.data.product[0].productName
            let categoryId = response.data.product[0].categoryId
            let description = response.data.product[0].description
            let enable = response.data.product[0].enable
            $('input#productName').val(productName)
            categoryHtmlInsert(categoryId)
            $('textarea#description').val(description)
            $('input#oldCategory').val(categoryId)
            if(enable == 1){
                $("input#enable").prop("checked", true).val(1);
            }else{
                $("input#enable").prop("checked", false).val(0);
            }
        }
    }

    // 撈此指定商品
    getProduct()
    /**************************************抓商品************************************ */

    // 更新商品
    const updateProduct = async () => {
        let productId = await getProductIdFromUrl()
        let productName = $('input#productName').val()
        let categoryId = $('select#category').val()
        let description = $('textarea#description').val()
        let enable = $('input#enable').val()
        response = await axios.post("{{route('productUpdate')}}",{
            'productId': productId,
            'productName': productName,
            'categoryId': categoryId,
            'description': description,
            'enable': enable
        });
        if(response){
            $('input#oldCategory').val(categoryId)
            alert(response.data.message)
        }else{
            alert("更新失敗")
        }
    }

    // 回到商品管理頁面
    const backToProductIndex = async () => {
        categoryId = $('input#oldCategory').val()
        let url = '{{route("productIndex", ["categoryId"=>":categoryId"])}}';
        url = url.replace(':categoryId', categoryId)
        location.href = url;
    } 

    // 商上下架
    const changeEnable = async () => {
        $(`input#enable:checked`).val() ? $(`input#enable`).val(1) : $(`input#enable`).val(0)
    }

</script>

@endsection