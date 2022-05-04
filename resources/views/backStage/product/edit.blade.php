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
                <div class="card-body">
                    <div class="col-12 d-flex border-bottom">
                        <label for="inputFirstName" class="form-label col-7 fs-2 d-flex justify-content-end">商品資料</label>
                        <div class="col-2"></div>
                        <button class="btn btn-outline-danger my-2 col-3" onclick="deleteProductConfirm()"><i class='bx bx-trash mr-1'></i>刪除</button>
                    </div>
                    <div class="col-12 mt-2">
                        <label for="productName" class="form-label fs-4">商品名稱</label>
                        <input type="hidden" name="productId" id="productId">
                        <input type="hidden" name="oldSort" id="oldSort">
                        <input id="productName" name="productName" class="form-control">
                    </div>
                    <div class="col-12 mt-3">
                        <label for="category" class="form-label fs-4">商品分類</label>
                        <input type="hidden" name="oldCategory" id="oldCategory">
                        <select name="categoryId" id="category" class="form-select">
                        </select>
                    </div>
                    <div class="col-12 ">
                        <label for="description" class="form-label fs-4 ">商品描述</label>
                        <textarea id="description" name="description" type="textarea" class="form-control"></textarea>
                    </div>
                    <div class="col-12 d-flex mt-4">
                        <div class="col-8 d-flex">
                            <label for="description" class="form-label fs-4 m-0 col-3 d-flex align-items-center justify-content-start">上下架</label>
                            <div class="form-check form-switch p-0 d-flex align-items-center justify-content-start col-9">
                                <input id="enable" name="enable" class="form-check-input m-0" type="checkbox" onclick="changeProductEnable()">
                            </div>
                        </div>
                        <div class="col-4 d-flex justify-content-end">
                            <button class="btn btn-outline-primary px-5" onclick="updateProduct()"><i class='bx bx-edit mr-1'></i>更新</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 商品圖片 -->
        <div class=" col-7">
            <div class="card">
                <div class="card-body">
                    <div class="col-12 d-flex border-bottom">
                        <label for="inputFirstName" class="form-label col-12 fs-2 d-flex justify-content-center">商品圖片</label>
                    </div>
                    <table class="table table-bordered mt-4" id="productImage">
                    </table>
                </div>
            </div>
        </div>
        <!-- 商品子項 -->
        <div class="col-12">
            <div class="card p-3">
                <div class="col-12 d-flex mb-2">
                    <div class="col-3">
                    </div>
                    <div class="col-6">
                        <label for="inputFirstName" class="form-label fs-2 d-flex justify-content-center">商品子項</label>
                    </div>
                    <div class="col-3 d-flex justify-content-end m-2 pe-2">
                        <button class="btn btn-outline-success px-5 me-1 " data-bs-toggle="modal" data-bs-target="#insertProductDetail">新增子項</button>
                    </div>
                    <!-- 新增子項彈窗 -->
                    <div class="modal fade" id="insertProductDetail" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title" id="staticBackdropLabel">新增商品子項</h3>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="col-9">
                                        <label for="inputFirstName" class="form-label fs-4">品名</label>
                                        <input type="productDetailName" class="form-control" id="productDetailName">
                                    </div>
                                    <hr>
                                    <div class="col-6 mt-2">
                                        <label for="inputFirstName" class="form-label fs-4">規格</label>
                                        <input type="specification" class="form-control" id="specification">
                                    </div>
                                    <hr>
                                    <div class="col-6">
                                        <label for="inputFirstName" class="form-label fs-4">單價</label>
                                        <input type="number" class="form-control" id="unitPrice">
                                    </div>
                                    <hr>
                                    <div class="col-6">
                                        <label for="inputFirstName" class="form-label fs-4">數量</label>
                                        <input type="number" class="form-control" id="quantity" value="0">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                                    <button type="button" class="btn btn-success" onclick="insertProductDetail()">新增</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 新增子項彈窗 -->
                </div>
                <!-- 商品細項 -->
                <div id="productDetail">
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

    // 回到商品管理頁面
    const backToProductIndex = async () => {
        categoryId = $('input#oldCategory').val()
        let url = '{{route("productIndex", ["categoryId"=>":categoryId"])}}';
        url = url.replace(':categoryId', categoryId)
        location.href = url;
    }

/********************************************************************商品相關******************************************************************************* */

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
        $('select#category').html(category)
    }
    /**************************************抓分類************************************ */

    /**************************************抓商品************************************ */
    // 撈此分類全部商品資料
    const getProduct = async () => {
        let productId = await getProductIdFromUrl()
        let response = await axios.post("{{route('product')}}", {
            'productId': productId
        });
        if (response) {
            let productName = response.data.product[0].productName
            let categoryId = response.data.product[0].categoryId
            let description = response.data.product[0].description
            let enable = response.data.product[0].enable
            let sort = response.data.product[0].sort
            $('input#productName').val(productName)
            categoryHtmlInsert(categoryId)
            $('textarea#description').val(description)
            $('input#oldCategory').val(categoryId)
            $('input#productId').val(productId)
            $('input#oldSort').val(sort)
            if (enable == 1) {
                $("input#enable").prop("checked", true).val(1);
            } else {
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
        let oldCategoryId = $('input#oldCategory').val()
        let oldSort = $('input#oldSort').val()
        let categoryId = $('select#category').val()
        let description = $('textarea#description').val()
        let enable = $('input#enable').val()
        response = await axios.post("{{route('productUpdate')}}", {
            'productId': productId,
            'productName': productName,
            'oldCategoryId': oldCategoryId,
            'oldSort': oldSort,
            'categoryId': categoryId,
            'description': description,
            'enable': enable
        });

        // 商品名稱驗證
        if (!productName) {
            alert('商品名稱不可空白')
            return false
        }

        if (response) {
            $('input#oldCategory').val(categoryId)
            alert(response.data.message)
            getProduct()
        } else {
            alert("更新失敗")
        }
    }

    /**************************************商品刪除************************************ */
    // 商品刪除確認
    const deleteProductConfirm = () => {
        let productId = $('input#productId').val()
        let check = confirm('確定刪除商品，所有資料將會遺失？');
        if (check) {
            deleteProduct(productId)
        } else {
            return false
        }
    }

    // 商品刪除
    const deleteProduct = async (productId) => {
        // 打axios刪除
        let response = await axios.post("{{route('productDelete')}}", {
            'productId': productId
        });
        if (response) {
            alert(response.data.message)
            await backToProductIndex()
        }
    }
    /**************************************商品刪除************************************ */

    // 商上下架
    const changeProductEnable = async () => {
        $(`input#enable:checked`).val() ? $(`input#enable`).val(1) : $(`input#enable`).val(0)
        let productId = $('input#productId').val()
        let enable = $(`input#enable`).val()
        let response = await axios.post("{{route('productChangeEnable')}}", {
            'productId': productId,
            'enable': enable
        });
    }
/********************************************************************商品相關******************************************************************************* */

/********************************************************************商品圖片相關******************************************************************************* */
    // 上傳圖片
    const uploadImage = async (productId,i) => {
        let formData = new FormData($("#uploadForm")[0]) //创建一个forData
        let productImage = "input#productImage_" + i
        formData.append('productImage', $(productImage)[0].files[0]) //把file添加进去  name命名为img
        formData.append('productId', productId) //把file添加进去  name命名为img

        $.ajax({
            url: "{{route('productImageUpload')}}",
            type: "post",
            data: formData,
            processData: false, // 告诉jQuery不要去处理发送的数据
            contentType: false, // 告诉jQuery不要去设置Content-Type请求头
            dataType: 'text',
            success: function(data) {
                productImageTableHtmlInsert()
                console.log(data);
            }
        })
    }

    // 改變排序
    const changeProductImageSort = async (productId,imageId, newSort, oldSort, imageCount) => {
        if (newSort <= 0 || newSort > imageCount) {
            return null
        }
        let response = await axios.post("{{route('productImageChangeSort')}}", {
            'productId': productId,
            'imageId': imageId,
            'newSort': newSort,
            'oldSort': oldSort
        })
        await productImageTableHtmlInsert()
    }

    /**************************************商品圖片刪除************************************ */
    // 商品圖片刪除確認
    const deleteProductImageConfirm = async (productId,imageId,image) => {
        let check = confirm('商品圖片將被刪除');
        if (check) {
            deleteProductImage(productId,imageId,image)
        } else {
            return false
        }
    }

    // 商品圖片刪除
    const deleteProductImage = async (productId,imageId,image) => {
    // 打axios刪除
    let response = await axios.post("{{route('productImageDelete')}}", {
            'productId': productId,
            'imageId': imageId,
            'image': image
        });
        if (response) {
            console.log(response);
            await productImageTableHtmlInsert()
        }
    }
    /**************************************商品圖片刪除************************************ */    

    // 撈圖片資料
    const getProductImage = async (productId) => {
        let response = await axios.post("{{route('productImage')}}",{
            'productId': productId
        })
        return response.data.productImage
    }    

    //  取名叫productImageTableHtml就會有非同步問題 
    const productImageTableHtml = async () => {
        let productId = await getProductIdFromUrl()
        let productImages = await getProductImage(productId)
        let td = []

        for(i=0;i<8;i++){
            if(productImages[i]){
                let imageId = productImages[i].imageId
                let image = productImages[i].image
                let sort = productImages[i].sort
                let host = productImages.host
                let imageCount = productImages.imageCount

                td[i] = `<img src="${host}/OnlineStore/storage/app/public/productImage/${productId}/${image}" height="140">
                         <div class="d-flex justify-content-center">
                             <button class="btn btn-outline-secondary mt-2 mx-auto col-3" onclick="changeProductImageSort('${productId}','${imageId}','${sort-1}','${sort}','${imageCount}')"><i class='bx bx-caret-up-circle mx-0'></i></button>
                             <button class="btn btn-outline-secondary mt-2 mx-auto col-3" onclick="changeProductImageSort('${productId}','${imageId}','${sort+1}','${sort}','${imageCount}')"><i class='bx bx-caret-down-circle mx-0'></i></button>
                             <button class="btn btn-outline-danger mt-2 mx-auto col-3" onclick="deleteProductImageConfirm('${productId}','${imageId}','${image}')"><i class='bx bx-trash mx-0'></i></button>
                         </div>`
            }else{
                td[i] = `<label class="btn btn-outline-success">
                            <form enctype="multipart/form-data" id="uploadForm">
                                <input id="productImage_${i}" name="productImage_${i}" type="file" style="display: none;" onchange="uploadImage('${productId}',${i})">
                            </form>
                            <i class='bx bx-cloud-upload mr-1'></i>上傳圖片
                        </label>`
            }
        }

        let html = `<tbody class="text-center align-center">
                        <tr>
                            <th scope="col" width="25%">1</th>
                            <th scope="col" width="25%">2</th>
                            <th scope="col" width="25%">3</th>
                            <th scope="col" width="25%">4</th>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;">${td[0]}</td>
                            <td style="vertical-align: middle;">${td[1]}</td>
                            <td style="vertical-align: middle;">${td[2]}</td>
                            <td style="vertical-align: middle;">${td[3]}</td>
                        </tr>
                        <tr>
                            <th scope="col" width="25%">5</th>
                            <th scope="col" width="25%">6</th>
                            <th scope="col" width="25%">7</th>
                            <th scope="col" width="25%">8</th>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle;">${td[4]}</td>
                            <td style="vertical-align: middle;">${td[5]}</td>
                            <td style="vertical-align: middle;">${td[6]}</td>
                            <td style="vertical-align: middle;">${td[7]}</td>
                        </tr>
                    </tbody>`
        return html
    }

    // 商品圖片table html塞入
    const productImageTableHtmlInsert = async () => {
        let html = await productImageTableHtml()
        $("table#productImage").html(html)
    }

    productImageTableHtmlInsert()    
/********************************************************************商品圖片相關******************************************************************************* */

/********************************************************************商品子項相關******************************************************************************* */
    // 新增商品子項
    const insertProductDetail = async () => {
        let productId = $('input#productId').val()
        let productDetailName = $('input#productDetailName').val()
        let specification = $('input#specification').val()
        let unitPrice = $('input#unitPrice').val()
        let quantity = $('input#quantity').val()

        // 商品子項名稱驗證
        if (!productDetailName) {
            alert('品名不可空白')
            return false
        }

        // 規格驗證
        if (!specification) {
            alert('規格不可空白')
            return false
        }

        // 單價驗證
        if (unitPrice < 0) {
            alert('單價錯誤')
            return false
        }

        // 數量驗證
        if (quantity < 0) {
            alert('數量錯誤')
            return false
        }

        let response = await axios.post("{{route('productDetailInsert')}}", {
            'productId': productId,
            'productDetailName': productDetailName,
            'specification': specification,
            'unitPrice': Number(unitPrice),
            'quantity': Number(quantity)
        })
        if (response) {
            window.location.reload()
        }
    }

    // 商品子項更新
    const updatePorductDetail = async (productDetailId) => {
        let productDetailName = $(`input#${productDetailId}_productDetailName`).val()
        let specification = $(`input#${productDetailId}_specification`).val()
        let unitPrice = $(`input#${productDetailId}_unitPrice`).val()
        let quantity = $(`input#${productDetailId}_quantity`).val()

        // 商品子項名稱驗證
        if (!productDetailName) {
            alert('品名不可空白')
            return false
        }

        // 規格驗證
        if (!specification) {
            alert('規格不可空白')
            return false
        }

        // 單價驗證
        if (unitPrice < 0) {
            alert('單價錯誤')
            return false
        }

        // 數量驗證
        if (quantity < 0) {
            alert('數量錯誤')
            return false
        }

        let response = await axios.post("{{route('productDetailUpdate')}}", {
            'productDetailId': productDetailId,
            'productDetailName': productDetailName,
            'specification': specification,
            'unitPrice': Number(unitPrice),
            'quantity': Number(quantity)
        })
        if (response) {
            alert("商品子項更新成功")
            productDetailHtmlInsert()
            let modal = '#' + productDetailId + "_edit"
            $(modal).modal('hide')
        }
    }

    /**************************************商品子項刪除************************************ */
    // 商品刪除確認
    const deleteProductDetailConfirm = (productDetailId) => {
        let check = confirm('確定刪除商品子項，所有資料將會遺失？');
        if (check) {
            deleteProductDetail(productDetailId)
        } else {
            return false
        }
    }

    // 商品子項刪除
    const deleteProductDetail = async (productDetailId) => {
        // 打axios刪除
        let response = await axios.post("{{route('productDetailDelete')}}", {
            'productDetailId': productDetailId
        });
        if (response) {
            alert(response.data.message)
            productDetailHtmlInsert()
        }
    }
    /**************************************商品子項刪除************************************ */

    // 商品子項上下架
    const changeProductDetailEnable = async (productDetailId) => {
        $(`input#${productDetailId}_enable:checked`).val() ? $(`input#${productDetailId}_enable`).val(1) : $(`input#${productDetailId}_enable`).val(0)
        let enable = $(`input#${productDetailId}_enable`).val()
        let response = await axios.post("{{route('productDetailChangeEnable')}}", {
            'productDetailId': productDetailId,
            'enable': enable
        });
    }

    // 改變排序
    const changeProductDetailSort = async (productDetailId, newSort, oldSort, productDetailsCount) => {
        if (newSort <= 0 || newSort > productDetailsCount) {
            return null
        }
        let response = await axios.post("{{route('productDetailChangeSort')}}", {
            'productDetailId': productDetailId,
            'newSort': newSort,
            'oldSort': oldSort
        })
        productDetailHtmlInsert()
    }

    /**************************************產出productDetail的HTML並塞入************************************ */
    // 抓productDetail 資料
    const getProductDetail = async () => {
        let productId = await getProductIdFromUrl()
        let response = await axios.post("{{route('productDetail')}}", {
            'productId': productId
        })
        return response.data.productDetail
    }

    // 產出productDetail 資料
    const productDetailHtml = async (productDetails) => {
        let html = ``
        if (productDetails === "無資料") {
            return html
        }
        let productDetailsCount = productDetails.length
        productDetails.forEach((productDetail, key) => {
            let productDetailId = productDetail.productDetailId
            let productDetailName = productDetail.productDetailName
            let specification = productDetail.specification
            let unitPrice = productDetail.unitPrice
            let quantity = productDetail.quantity
            let enable = productDetail.enable
            let sort = productDetail.sort

            let enableHtml = ``
            if (enable == 1) {
                enableHtml = `<input id="${productDetailId}_enable" name="${productDetailId}_enable" class="form-check-input m-0" type="checkbox" onclick="changeProductDetailEnable('${productDetailId}')" checked>`
            } else {
                enableHtml = `<input id="${productDetailId}_enable" name="${productDetailId}_enable" class="form-check-input m-0" type="checkbox" onclick="changeProductDetailEnable('${productDetailId}')">`
            }

            html += `<!-- 品項${key+1} -->
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12 d-flex">
                                <label for="index" class="form-label fs-2 d-flex justify-content-start col-8">品項${key+1}</label>
                                <div class="col-4 d-flex justify-content-end my-3 ms-2">
                                    <button class="btn btn-outline-primary px-5 me-2" data-bs-toggle="modal" data-bs-target="#${productDetailId}_edit" ><i class='bx bx-edit mr-1'></i></i>編輯</button>
                                    <button class="btn btn-outline-danger px-5" onclick="deleteProductDetailConfirm('${productDetailId}')"><i class='bx bx-trash mr-1'></i>刪除</button>
                                </div>
                                <!-- 新增子項彈窗 -->
                                <div class="modal fade" id="${productDetailId}_edit" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h3 class="modal-title" id="staticBackdropLabel">編輯商品子項</h3>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="col-9">
                                                    <label for="${productDetailId}_productDetailName" class="form-label fs-4">品名</label>
                                                    <input type="productDetailName" class="form-control" id="${productDetailId}_productDetailName" value="${productDetailName}">
                                                </div>
                                                <hr>
                                                <div class="col-6 mt-2">
                                                    <label for="${productDetailId}_specification" class="form-label fs-4">規格</label>
                                                    <input type="specification" class="form-control" id="${productDetailId}_specification" value="${specification}">
                                                </div>
                                                <hr>
                                                <div class="col-6">
                                                    <label for="${productDetailId}_unitPrice" class="form-label fs-4">單價</label>
                                                    <input type="number" class="form-control" id="${productDetailId}_unitPrice" value="${unitPrice}">
                                                </div>
                                                <hr>
                                                <div class="col-6">
                                                    <label for="${productDetailId}_quantity" class="form-label fs-4">數量</label>
                                                    <input type="number" class="form-control" id="${productDetailId}_quantity" value="${quantity}">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                                                <button type="button" class="btn btn-primary" onclick="updatePorductDetail('${productDetailId}')">更新</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- 新增子項彈窗 -->
                            </div>
                            <hr class="m-0">
                            <div class="col-12 row mt-3">
                                <div class="col-3">
                                    <label for="${productDetailId}_productDetaiName" class="form-label fs-4">品名</label>
                                    <input type="email" class="form-control" value="${productDetailName}" disabled>
                                </div>
                                <div class="col-2">
                                    <label for="${productDetailId}_specification" class="form-label fs-4">規格</label>
                                    <input type="email" class="form-control" value="${specification}" disabled>
                                </div>
                                <div class="col-2">
                                    <label for="${productDetailId}_unitPrice" class="form-label fs-4">單價</label>
                                    <input type="email" class="form-control" value="${unitPrice}" disabled>
                                </div>
                                <div class="col-2">
                                    <label for="${productDetailId}_quantity" class="form-label fs-4">數量</label>
                                    <input type="email" class="form-control" value="${quantity}" disabled>
                                </div>
                                <div class="col-1">
                                    <label for="inputFirstName" class="form-label fs-4">上下架</label>
                                    <div class="form-check form-switch p-0 d-flex align-items-end justify-content-center col-9">
                                        ${enableHtml}
                                    </div>
                                </div>
                                <div class="col-2 row d-flex align-items-center justify-content-end">
                                    <label for="inputFirstName" class="form-label fs-4 d-flex justify-content-center">排序</label>
                                    <!-- 上下移按鈕 -->
                                    <div class="col-2"></div>
                                        <button type="button" class="col-3 btn btn-outline-secondary  text-center" onclick="changeProductDetailSort('${productDetailId}',${sort-1},${sort},${productDetailsCount})"><i class='bx bx-caret-up-circle m-0'></i></button>
                                    <div class="col-2"></div>
                                        <button type="button" class="col-3 btn btn-outline-secondary  text-center" onclick="changeProductDetailSort('${productDetailId}',${sort+1},${sort},${productDetailsCount})"><i class='bx bx-caret-down-circle m-0'></i></button>
                                    <div class="col-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 品項${key+1} -->`
        })
        return html
    }

    // 產出productDetail的HTML並塞入
    const productDetailHtmlInsert = async () => {
        let productDetails = await getProductDetail()
        let html = await productDetailHtml(productDetails)
        $("#productDetail").html(html)
    }
    productDetailHtmlInsert()
    /**************************************產出productDetail的HTML並塞入************************************ */
/********************************************************************商品子項相關******************************************************************************* */
</script>

@endsection