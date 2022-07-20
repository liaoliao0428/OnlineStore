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
        <!-- 訂單狀態選擇 -->
        <div class="col-12 d-flex p-0 justify-content-center">
            <div class="col-1">
                <button id="button_1" class="btn btn-outline-success active" style="width: 90%;" onclick="changeButtonClass('button_1')">新訂單</button>
            </div>
            <div class="col-1">
                <button id="button_2" class="btn btn-outline-success" style="width: 90%;" onclick="changeButtonClass('button_2')">處理中</button>
            </div>
            <div class="col-1">
                <button id="button_3" class="btn btn-outline-success" style="width: 90%;" onclick="changeButtonClass('button_3')">已出貨</button>
            </div>
            <div class="col-1">
                <button id="button_4" class="btn btn-outline-success" style="width: 90%;" onclick="changeButtonClass('button_4')">已送達</button>
            </div>
            <div class="col-1">
                <button id="button_5" class="btn btn-outline-success" style="width: 90%;" onclick="changeButtonClass('button_5')">訂單完成</button>
            </div>
            <div class="col-2">
                <button id="button_6" class="btn btn-outline-success" style="width: 90%;" onclick="changeButtonClass('button_6')">訂單取消/退貨申請</button>
            </div>
            <div class="col-2">
                <button id="button_7" class="btn btn-outline-success" style="width: 90%;" onclick="changeButtonClass('button_7')">訂單取消/退貨</button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body ">
            <!-- 訂單列表 -->
            <table class="table table-bordered mb-0">
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
                <tbody id="order" class="align-middle text-center"></tbody>
            </table>
        </div>
    </div>
</div>

@endsection
@section('script')

<script>
    let today = new Date();
    let year = today.getFullYear()
    let month = (today.getMonth()+1) < 10 ? '0' + (today.getMonth()+1) : today.getMonth()
    let day = today.getDate() < 10 ? '0' + today.getDate() : today.getDate() 
    let date = year + '-' + month + '-' + day
    $("#date").val(date)

    // 改變訂單狀態按鈕樣式
    const changeButtonClass = (button) => {
        for(i = 1;i < 8;i++){
            $(`#button_${i}`).removeClass("active")
        }        
        $(`#${button}`).addClass("active")
        getOrder(button)
    }

    // 撈訂單資料
    const getOrder = async (button) => {
        let orderStatus = button.split('_')
        let response = await axios.post("{{route('order')}}",{
            'orderStatus': orderStatus[1]
        })

        let orderHtml = setOrderHtml(response.data.order)

        $('tbody#order').html(orderHtml)
    }

    // 將訂單資料處合成html
    const setOrderHtml = (orders) => {
        let orderHtml = ''
        orders.forEach((order) => {
            orderHtml += `<tr>
                              <th scope="row">${order.orderNumber}</th>
                              <td>${order.createTime}</td>
                              <td>$${order.amount}</td>
                              <td>${order.payMethod}</td>
                              <td>${order.payStatus}</td>
                              <td>${order.orderStatus}</td>
                              <td>${order.receiverStoreType}</td>
                              <!-- 詳情按鈕 -->
                              <td>
                                  <button onclick="orderDetail('${order.orderNumber}')" class="btn btn-outline-primary mx-auto" ><i class='bx bx-edit mr-1'></i>詳情</button>
                              </td>
                          </tr>`
        })

        return orderHtml;
    }

    // 撈訂單資料
    getOrder('button_1')

    // 訂單詳情
    const orderDetail = (orderNumber) => {
        let url = '{{route("orderDetail",["orderNumber" => ":orderNumber"])}}';
        url = url.replace(':orderNumber', orderNumber)
        location.href = url;
    }

</script>
@endsection