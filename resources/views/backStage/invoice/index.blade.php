@extends('backstage.layout')
<!-- 引用模板 -->
@section('head')
@endsection
@section('content')
<style>

</style>
<div class="bg-white p-3">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">首頁</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">發票管理</li>
                </ol>
            </nav>
        </div>
    </div>
    <hr />
    <div class="col-12 row m-0 my-3">
        <!-- 時間選擇 -->
        <div class="col-3 d-flex align-items-center justify-content-start">
            <input id="date" type="month">
        </div>
        <div class="col-9 d-flex justify-content-end">
            <div class="col-2 me-2">
                <button id="button_6" class="btn btn-outline-success" style="width: 100%;">綠界自動新增發票</button>
            </div>
            <div class="col-2">
                <button id="button_6" class="btn btn-outline-success" style="width: 100%;" data-bs-toggle="modal" data-bs-target="#invoiceModal">手動新增發票</button>
                <!-- 新增發票彈窗 -->
                <div class="modal fade" id="invoiceModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg ">
                        <div id="loading" style="display: none;    position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);z-index: 1500;font-size: 2rem;">
                            網頁自動跳轉，請勿重整...</div>
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold fs-3" id="exampleModalLabel">新增發票</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="d-flex align-items-center my-2">
                                        <h5 class="fw-bold my-0" style="min-width:100px">年　　份</h5>
                                        <select name="year" id="date" class="form-select">
                                        </select>
                                    </div>
                                    <div class="d-flex align-items-center my-2">
                                        <h5 class="fw-bold my-0" style="min-width:100px">月　　份</h5>
                                        <select name="date" id="" class="form-select">
                                            <option value="1">1月-2月</option>
                                            <option value="3">3月-4月</option>
                                            <option value="5">5月-6月</option>
                                            <option value="7">7月-8月</option>
                                            <option value="9">9月-10月</option>
                                            <option value="11">11月-12月</option>
                                        </select>
                                    </div>
                                    <div class="d-flex align-items-center my-2">
                                        <h5 class="fw-bold my-0" style="min-width:100px">字　　軌</h5>
                                        <input type="text" name="invoiceCode" class="form-control" placeholder="輸入發票字軌" autocomplete="off" required="required">
                                    </div>
                                    <div class="d-flex align-items-center my-2">
                                        <h5 class="fw-bold my-0" style="min-width:100px">起始號碼</h5>
                                        <input type="number" name="startNumber" class="form-control" placeholder="輸入發票起始號碼" autocomplete="off" required="required">
                                    </div>
                                    <div class="d-flex align-items-center my-2">
                                        <h5 class="fw-bold my-0" style="min-width:100px">結束號碼</h5>
                                        <input type="number" name="endNumber" class="form-control" placeholder="輸入發票結束號碼" autocomplete="off" required="required">
                                    </div>
                                    <div class="d-flex align-items-center my-2">
                                        <h5 class="fw-bold my-0" style="min-width:100px">發票匯入</h5>
                                        <input type="file" name="invoice_excel" class="form-control" id="invoice_excel">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                                    <button type="button" onclick="" class="btn btn-primary">送出</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-body ">
            <!-- 訂單列表 -->
            <table class="table table-bordered mb-0" id="product">
                <!-- 外框欄位 -->
                <thead>
                    <tr>
                        <th scope="col" width="9%" class="text-center fs-4">發票年度</th>
                        <th scope="col" width="9%" class="text-center fs-4">發票期別</th>
                        <th scope="col" width="9%" class="text-center fs-4">發票類別</th>
                        <th scope="col" width="16%" class="text-center fs-4">字軌類別</th>
                        <th scope="col" width="9%" class="text-center fs-4">字軌名稱</th>
                        <th scope="col" width="12%" class="text-center fs-4">起始號碼</th>
                        <th scope="col" width="12%" class="text-center fs-4">結束號碼</th>
                        <th scope="col" width="12%" class="text-center fs-4">目前已使用號碼</th>
                        <th scope="col" width="12%" class="text-center fs-4">使用狀態</th>
                    </tr>
                </thead>
                <!-- 訂單內容 -->
                <tbody id="order" class="align-middle text-center">
                    <tr>
                        <th scope="row">111</th>
                        <td>5月-6月</td>
                        <td>B2C</td>
                        <td>07-一般稅額計算之電子發票</td>
                        <td>VJ</td>
                        <td>10001050</td>
                        <td>10002099</td>
                        <td>10002099</td>
                        <td>已停用</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection
@section('script')

<script>
    let today = new Date();
    let year = today.getFullYear()
    let month = (today.getMonth() + 1) < 10 ? '0' + (today.getMonth() + 1) : today.getMonth()
    let date = year + '-' + month 
    $("#date").val(date)

</script>
@endsection