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
    <hr />
    <!--breadcrumb-->
    <div class="row">
        <div class="col-4 row">
            <div class="col-6">
                <select class="form-select " name="category" id="category" onchange="changeCategory()">
                </select>
            </div>
        </div>
        <div class="col-8 text-end">
            <button class="btn btn-outline-success px-5 mb-3 " onclick="addProduct()"><i class='bx bx-cloud-upload mr-1'></i>新增商品</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body ">
            <!-- 商品列表 -->
            <table class="table table-bordered mb-0" id="product">
                <!-- 外框欄位 -->
                <thead>
                    <tr>
                        <th scope="col" width="5%" class="text-center fs-4">#</th>
                        <th scope="col" width="15%" class="text-center fs-4">商品圖</th>
                        <th scope="col" width="55%" class="text-center fs-4">商品</th>
                        <th scope="col" width="10%" class="text-center fs-4">上下架</th>
                        <th scope="col" width="20%" class="text-center fs-4">操作</th>
                    </tr>
                </thead>
                <!-- 表格內容 -->
                <tbody id="productData">
                </tbody>
            </table>
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
        let splitUrlLength = splitUrl.length
        let categoryId = splitUrl[splitUrlLength - 1]
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
        let categorys = await getAllCategory()
        let html = `<option value="0">選擇商品分類</option>`
        let categoryId = await getCategoryIdFromUrl() // 從網址取得現在categoryId
        categorys.forEach((category, $key) => {
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

    // 商品編輯
    const editProduct =  (productId) => {
        let url = '{{route("productEdit", ["productId"=>":productId"])}}';
        url = url.replace(':productId', productId)
        location.href = url;
    }

    /**************************************商品刪除************************************ */
    // 商品刪除確認
    const deleteProductConfirm = (productId) => {
        let check = confirm('確定刪除商品，所有資料將會遺失？');
        if(check){
            deleteProduct(productId)
        }else{
            return false
        }
    }

    // 商品刪除
    const deleteProduct = async (productId) => {
        // 打axios刪除
        let response = await axios.post("{{route('productDelete')}}", {'productId': productId});
        if(response){
            alert(response.data.message)
            window.location.reload()
        }        
    }
    /**************************************商品刪除************************************ */

    // 商上下架
    const changeProductEnable = async (productId) => {
        let productEnable = productId + "_enable"
        if ($(`#${productEnable}:checked`).val()) {
            enable = 1
        } else {
            enable = 0
        }
        let response =  await axios.post("{{route('productChangeEnable')}}",{'productId': productId,'enable': enable});  
    }

    // 改變排序
    const changeSort = async (productId,newSort,oldSort,productsCount) => {
        if(newSort <= 0 || newSort > productsCount){
            return null
        }

        let response = await axios.post("{{route('productChangeSort')}}",{
            'productId': productId,
            'newSort': newSort,
            'oldSort': oldSort
        })
        productHtmlInsert()
    }
    
    /**************************************抓商品************************************ */
    // 撈此分類全部商品資料
    const getProduct = async () => {
        let categoryId = await getCategoryIdFromUrl()
        let response = await axios.post("{{route('product')}}", {'categoryId': categoryId});
        return response.data.product
    }

    // // 組合商品body - 失敗
    // const productBodyHtml = async (productDetail) => {
    //     let htmlBody = ``
    //     productDetails.forEach((productDetail, productDetailIndex) => {
    //         let productDetailName = productDetail.productDetailName
    //         let specification = productDetail.specification
    //         let unitPrice = productDetail.unitPrice
    //         let quantity = productDetail.quantity
    //         htmlBody += `<div class="row m-0 p-0 align-items-start">
    //                         <div class="col-3 p-0 border-bottom border-end fs-4">${productDetailIndex+1}</div>
    //                         <div class="col-3 p-0 border-bottom border-end fs-4">${specification}</div>
    //                         <div class="col-3 p-0 border-bottom border-end fs-4">${unitPrice}</div>
    //                         <div class="col-3 p-0 border-bottom fs-4">${quantity}</div>
    //                     </div>`
    //     });
    //     return htmlBody
    // }
    

    // 組合商品html
    const productHtml = async () => {
        // 撈此分類全部商品資料
        let products = await getProduct()

        // 跑forEach組合html
        let html = ``
        let productsCount = products.length
        products.forEach((product, productIndex) => {
            let productId = product.productId
            let productName = product.productName
            let enable = product.enable
            let productDetails = product.productDetail
            let sort = product.sort

            let host = product.host
            let productImage = product.productImage
            let image = ``
            productImage ? image = `${host}/OnlineStore/Backend/storage/app/public/productImage/${productId}/${productImage}` : null

            // let htmlBody = await productBodyHtml(productDetails)
            let htmlBody = ``
            productDetails.forEach((productDetail, productDetailIndex) => {
                let productDetailName = productDetail.productDetailName
                let specification = productDetail.specification
                let unitPrice = productDetail.unitPrice
                let quantity = productDetail.quantity        
                htmlBody += `<div class="row m-0 p-0  border-bottom">
                                <div class="col-1 p-0  border-end fs-4 d-flex align-items-center justify-content-center"><span class="">${productDetailIndex+1}</div>
                                <div class="col-4 p-0  border-end fs-4 d-flex align-items-center justify-content-center">${productDetailName}</div>
                                <div class="col-2 p-0  border-end fs-4 d-flex align-items-center justify-content-center">${specification}</div>
                                <div class="col-3 p-0  border-end fs-4 d-flex align-items-center justify-content-center">$${unitPrice}</div>
                                <div class="col-2 p-0  fs-4 d-flex align-items-center justify-content-center">${quantity}</div>
                            </div>`
            });

            let enableCheck = ``
            if(enable == 1){
                enableCheck = `<input id="${productId}_enable" name="${productId}_enable" class="form-check-input m-0" type="checkbox" onclick="changeProductEnable('${productId}')" checked>`
            }else{
                enableCheck = `<input id="${productId}_enable" name="${productId}_enable" class="form-check-input m-0" type="checkbox" onclick="changeProductEnable('${productId}')">`
            }

            html += `<tr class="text-center align-middle">
                        <!-- 排序 -->
                        <th scope="row" style="font-size: 3rem;">${productIndex+1}</th>
                        <!-- 商品圖 -->
                        <td><img src="${image}" height="200"></td>
                        <!-- 商品 - 商品名稱 -->
                        <td class="row m-0 p-0 align-items-start flex-grow-1 border-0">
                            <div class="row m-0 p-0 align-items-start">
                                <div class="col-12 p-0 border-bottom fs-4 fw-bolder">商品 - ${productName}</div>
                            </div>
                            <div class="row m-0 p-0 align-items-start">
                                    <div class="col-1 p-0 border-bottom border-end fs-4">#</div>
                                    <div class="col-4 p-0 border-bottom border-end fs-4">名稱</div>
                                    <div class="col-2 p-0 border-bottom border-end fs-4">規格</div>
                                    <div class="col-3 p-0 border-bottom border-end fs-4">價格</div>
                                    <div class="col-2 p-0 border-bottom fs-4">數量</div>
                            </div>
                            ${htmlBody}               
                        </td>
                        <!-- 上下架 -->
                        <td>
                            <div class="form-check form-switch p-0 d-flex justify-content-center">
                                ${enableCheck}
                            </div>
                        </td>
                        <!-- 操作 -->
                        <td class="align-middle">
                            <div class="row justify-content-center">
                                <!-- 編輯按鈕 -->
                                <div class="col-2"></div>
                                    <button type="button" class="col-8 btn btn-outline-primary px-5 mb-3 d-block mx-auto" onclick="editProduct('${productId}')"><i class='bx bx-edit mr-1'></i>編輯</button>
                                <div class="col-2"></div>
                                <!-- 刪除按鈕 -->
                                <div class="col-2"></div>
                                    <button type="button" class="col-8 btn btn-outline-danger px-5 mb-3 d-block mx-auto" onclick="deleteProductConfirm('${productId}')"><i class='bx bx-trash mr-1'></i>刪除</button>
                                <div class="col-2"></div>
                                <!-- 上下移按鈕 -->
                                <div class="col-2"></div>
                                    <button type="button" class="col-3 btn btn-outline-secondary mb-3 d-block text-center" onclick="changeSort('${productId}',${sort-1},${sort},${productsCount})"><i class='bx bx-caret-up-circle m-0'></i></button>
                                <div class="col-2"></div>
                                    <button type="button" class="col-3 btn btn-outline-secondary mb-3 d-block text-center" onclick="changeSort('${productId}',${sort+1},${sort},${productsCount})"><i class='bx bx-caret-down-circle m-0'></i></button>
                                <div class="col-2"></div>
                            </div>
                        </td>
                    </tr>`
            
        });
        return html      
    }    

    // 塞商品資料
    const productHtmlInsert = async () => {
        let product = await productHtml()
        $('tbody#productData').html(product)
    }

    productHtmlInsert()
    /**************************************抓商品************************************ */

    // 改變分類
    const changeCategory = () => {
        categoryId = $('select[name="category"]').val()
        let url = '{{route("productIndex", ["categoryId"=>":categoryId"])}}';
        url = url.replace(':categoryId', categoryId)
        location.href = url;
    }

    // 新增商品頁面
    const addProduct = () => {
        categoryId = $('select[name="category"]').val()
        let url = '{{route("productAdd", ["categoryId"=>":categoryId"])}}';
        url = url.replace(':categoryId', categoryId)
        location.href = url;
    }   

</script>

@endsection