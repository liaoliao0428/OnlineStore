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
                    <li class="breadcrumb-item active" aria-current="page">訂單管理</li>
                </ol>
            </nav>
        </div>
    </div>
    <hr />
    <div class="col-12 row m-0 my-3">
        <div class="col-3">
            <p>123</p>
        </div>
        <div class="col-9 d-flex p-0">
            <div class="col-2">
                <button class="btn btn-outline-success" style="width: 90%;">訂單成立</button>
            </div>
            <div class="col-2">
                <button class="btn btn-outline-success" style="width: 90%;">處理中</button>
            </div>
            <div class="col-2">
                <button class="btn btn-outline-success" style="width: 90%;">已出貨</button>
            </div>
            <div class="col-2">
                <button class="btn btn-outline-success" style="width: 90%;">已送達</button>
            </div>
            <div class="col-2">
                <button class="btn btn-outline-success" style="width: 90%;">訂單完成</button>
            </div>
            <div class="col-2">
                <button class="btn btn-outline-success" style="width: 90%;">訂單取消/退貨</button>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-body ">
            <!-- 訂單列表 -->
            <table class="table table-bordered mb-0" id="product">
                <!-- 外框欄位 -->
                <thead >
                    <tr>
                        <th scope="col" width="15%" class="text-center fs-4">訂單編號</th>
                        <th scope="col" width="20%" class="text-center fs-4">金額</th>
                        <th scope="col" width="20%" class="text-center fs-4">付款方式</th>
                        <th scope="col" width="10%" class="text-center fs-4">付款狀態</th>
                        <th scope="col" width="10%" class="text-center fs-4">狀態</th>
                        <th scope="col" width="25%" class="text-center fs-4">操作</th>
                    </tr>
                </thead>
                <!-- 表格內容 -->
                <tbody id="productData">
                    <th class="text-center" scope="row">1</th>
                    <td class="text-center">Mark</td>
                    <td class="text-center">Otto</td>
                    <td class="text-center">@mdo</td>
                    <td class="text-center">@mdo</td>
                    <td class="text-center">@mdo</td>
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection
@section('script')

<script>

</script>

@endsection