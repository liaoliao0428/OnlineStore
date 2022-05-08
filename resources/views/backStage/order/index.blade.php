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
                    <li class="breadcrumb-item active" aria-current="page">訂單管理</li>
                </ol>
            </nav>
        </div>
    </div>
    <hr />
    <div class="col-12 row m-0 my-3">
        <!-- 時間選擇 -->
        <div class="col-3 d-flex align-items-center justify-content-start">
            <input type="date">
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
                <thead>
                    <tr>
                        <th scope="col" width="12%" class="text-center fs-4">訂單編號</th>
                        <th scope="col" width="16%" class="text-center fs-4">訂單日期</th>
                        <th scope="col" width="11%" class="text-center fs-4">訂單金額</th>
                        <th scope="col" width="11%" class="text-center fs-4">付款方式</th>
                        <th scope="col" width="11%" class="text-center fs-4">付款狀態</th>
                        <th scope="col" width="11%" class="text-center fs-4">訂單狀態</th>
                        <th scope="col" width="11%" class="text-center fs-4">物流</th>
                        <th scope="col" width="17%" class="text-center fs-4">操作</th>
                    </tr>
                </thead>
                <!-- 訂單內容 -->
                <tbody id="order" class="align-middle text-center">
                    <tr>
                        <th scope="row">111111111111</th>
                        <td>2022-05-06 12:00:00</td>
                        <td>$100000</td>
                        <td>綠界金流</td>
                        <td>未付款</td>
                        <td>訂單成立</td>
                        <td>7-11</td>
                        <!-- 詳情按鈕 -->
                        <td>
                            <a href="{{route('orderDetail')}}" type="button" class="btn btn-outline-primary  mx-auto" ><i class='bx bx-edit mr-1'></i>詳情</a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">111111111111</th>
                        <td>2022-05-06 12:00:00</td>
                        <td>$100000</td>
                        <td>綠界金流</td>
                        <td>未付款</td>
                        <td>訂單成立</td>
                        <td>7-11</td>
                        <!-- 詳情按鈕 -->
                        <td>
                            <a href="{{route('orderDetail')}}" type="button" class="btn btn-outline-primary  mx-auto" ><i class='bx bx-edit mr-1'></i>詳情</a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">111111111111</th>
                        <td>2022-05-06 12:00:00</td>
                        <td>$100000</td>
                        <td>綠界金流</td>
                        <td>未付款</td>
                        <td>訂單成立</td>
                        <td>7-11</td>
                        <!-- 詳情按鈕 -->
                        <td>
                            <a href="{{route('orderDetail')}}" type="button" class="btn btn-outline-primary mx-auto" ><i class='bx bx-edit mr-1'></i>詳情</a>
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

</script>

@endsection