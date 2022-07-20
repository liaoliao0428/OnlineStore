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
                    <li class="breadcrumb-item active" aria-current="page">訂單詳情</li>
                </ol>
            </nav>
        </div>
    </div>
    <hr />
    <div class="col-8 mx-auto">
        <div class="card">
            <div class="card-body row">
                <div class="col-6 border-end px-4">
                    <div id="" class="col-12 row">
                        <p>訂單編號 : <input type="text" id="orderNumber" value="" disabled></p>
                    </div>
                    <div id="date" class="col-12">
                        <p>訂單日期 : <input type="text" id="createTime" value="" disabled></p>
                    </div>
                    <div id="" class="col-12">
                        <p>收件人 : <input type="text" id="receiverName" value="" style="margin-left: 14px;" disabled></p>
                    </div>
                    <div id="date" class="col-12">
                        <p>收件電話 : <input type="text" id="receiverCellPhone" value="" disabled></p>
                    </div>
                    <div id="" class="col-12">
                        <p>收件地址 : <input type="text" id="receiverAddress" value="" disabled></p>
                    </div>
                    <div id="" class="col-12" >
                        <p>寄件物流 : <input type="text" id="receiverStoreType" value="" disabled></p>
                    </div>
                    <div id="" class="col-12">
                        <p>寄送運費 : <input type="text" id="deliveryFee" value="" disabled></p>
                    </div>
                    <!-- <div id="" class="col-12">
                        <p>訂單備註 : </p>
                        <textarea type="text" id="" value="" style="width: 48%;"></textarea>
                    </div> -->
                </div>
                <div class="col-6 px-4">
                    <div id="" class="col-12">
                        <p>訂單金額 : <input type="text" id="amount" value="" disabled></p>
                    </div>
                    <div id="" class="col-12">
                        <p>付款方式 : <input type="text" id="payMethod" value="" disabled></p>
                    </div>
                    <div id="" class="col-12">
                        <p>付款狀態 : <input type="text" id="payStatus" value="" disabled></p>
                    </div>
                    <div id="" class="col-12">
                        <p>付款時間 : <input type="text" id="payTime" value="" disabled></p>
                    </div>
                    <div id="" class="col-12">
                        <p>發票號碼 : <input type="text" id="invoiceNumber" value="" disabled></p>
                    </div>
                    <div id="" class="col-12">
                        <p>發票統編 : <input type="text" id="" value="" disabled></p>
                    </div>
                    <div id="" class="col-12">
                        <p>發票載具 : <input type="text" id="carrierId" value="" disabled></p>
                    </div>
                    <div id="" class="col-12">
                        <p>發票捐贈 : <input type="text" id="invoiceDonate" value="" disabled></p>
                    </div>
                    <div id="" class="col-12">
                        <p>訂單狀態 : <input type="text" id="orderStatus" value="" disabled></p>
                    </div>
                    <div class="col-12" id="orderAction">
                        <div class="col-4">
                            <button class="btn btn-outline-success" style="width: 100%;" onclick="confirmOrder()">訂單成立</button>
                        </div>
                        <div class="col-4 mt-3">
                            <button class="btn btn-outline-danger" style="width: 100%;" onclick="cancelOrder()">訂單取消</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-8 mx-auto">
        <div class="card">
            <div class="card-body row">
                <div class="mb-2">
                    <h4>購買商品</h4>
                </div>
                <div class="p-auto">
                    <table class="table table-bordered mb-0" id="product">
                        <!-- 外框欄位 -->
                        <thead>
                            <tr>
                                <th scope="col" width="16%" class="text-center fs-4">商品分類</th>
                                <th scope="col" width="20%" class="text-center fs-4">商品名稱</th>
                                <th scope="col" width="16%" class="text-center fs-4">規格</th>
                                <th scope="col" width="16%" class="text-center fs-4">單價</th>
                                <th scope="col" width="16%" class="text-center fs-4">數量</th>
                                <th scope="col" width="16%" class="text-center fs-4">總金額</th>
                            </tr>
                        </thead>
                        <!-- 訂單內容 -->
                        <tbody id="orderDetail" class="align-middle text-center"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>


@endsection
@section('script')

<script>
    // 從網址取得當前分類id
    const getOrderNumber = () => {
        let url = location.pathname
        let splitUrl = url.split("/");
        let splitUrlLength = splitUrl.length
        let orderNumber = splitUrl[splitUrlLength - 1]
        return orderNumber
    }

    // 取得訂單詳細資料
    const getOrderFullData = async () => {
        let orderNumber = getOrderNumber()
        let response = await axios.post("{{route('orderFullData')}}",{
            'orderNumber': orderNumber
        })

        insertOrderData(response.data.orderFullData.order)
        insertOrderDetailData(response.data.orderFullData.orderDetail)
        insertOrderAction(response.data.orderFullData.order.orderStatus)
    }

    // 塞訂單資料
    const insertOrderData = (order) => {
        let orderStatus = order.orderStatus
        let receiverStoreType = order.receiverStoreType
        let invoiceDonate = order.invoiceDonate
        let payStatus = order.payStatus

        // 因為有些東西不用把原本的狀態轉掉 所以訂單細項的某先狀態中文改在這邊判斷
        let orderStatusName = ''
        switch(orderStatus){
                case 1:
                    orderStatusName = '確認中';
                break;

                case 2:
                    orderStatusName = '訂單成立處理中';
                break;

                case 3:
                    orderStatusName = '已出貨';
                break;

                case 4: 
                    orderStatusName = '已送達';
                break;

                case 5: 
                    orderStatusName = '訂單完成';
                break;

                case 6: 
                    orderStatusName = '訂單取消申請';
                break;

                case 7: 
                    orderStatusName = '訂單退貨申請';
                break;

                case 8: 
                    orderStatusName = '訂單取消';
                break;

                case 9: 
                    orderStatusName = '訂單退貨';
                break;
        }

        let receiverStoreTypeName = ''
        switch(receiverStoreType){
            case 'FAMI': case 'FAMIC2C':
                receiverStoreTypeName = '全家';
            break;

            case 'UNIMART': case 'UNIMARTFREEZE': case 'UNIMARTC2C':
                receiverStoreTypeName = '7-11';
            break;

            case 'HILIFE': case 'HILIFEC2C': case 'OKMARTC2C':
                receiverStoreTypeName = '萊爾富';
            break;

            case 'OKMARTC2C': 
                receiverStoreTypeName = 'OK';
            break;
        }

        let invoiceDonateName = ''
        switch(invoiceDonate){
            case 0:
                invoiceDonateName = '不捐贈';
            break;

            case 1:
                invoiceDonateName = '捐贈';
            break;
        }

        let payStatusName = ''
        switch(payStatus){
            case 0:
                payStatusName = '未付款';
            break;

            case 1:
                payStatusName = '已付款';
            break;

            case 2:
                payStatusName = '已退款';
            break;
        }

        $('#orderNumber').val(order.orderNumber)
        $('#createTime').val(order.createTime)
        $('#receiverName').val(order.receiverName)
        $('#receiverCellPhone').val(order.receiverCellPhone)
        $('#receiverAddress').val(receiverStoreTypeName + order.receiverStoreName)
        $('#receiverStoreType').val(receiverStoreTypeName)
        $('#deliveryFee').val(order.deliveryFee)
        $('#amount').val(order.amount)
        $('#payMethod').val(order.payMethod)
        $('#payStatus').val(payStatusName)
        $('#payTime').val(order.payTime)
        $('#invoiceNumber').val(order.invoiceNumber)
        $('#carrierId').val(order.carrierId)
        $('#invoiceDonate').val(invoiceDonateName)
        $('#orderStatus').val(orderStatusName)
    }

    // 塞訂單細項資料
    const insertOrderDetailData = (orderDetails) => {
        let orderDetailHtml = setOrderDetailHtml(orderDetails)
        $('tbody#orderDetail').html(orderDetailHtml)
    }

    // 組合orderDetailHtml
    const setOrderDetailHtml = (orderDetails) => {
        let orderDetailHtml = ``
        orderDetails.forEach((orderDetail) => {
            orderDetailHtml += `<tr>
                                <td>${orderDetail.categoryName}</td>
                                <td>${orderDetail.productName}</td>
                                <td>${orderDetail.specification}</td>
                                <td>$${orderDetail.unitPrice}</td>
                                <td>${orderDetail.quantity}</td>
                                <td>$${orderDetail.unitPrice * orderDetail.quantity}</td>
                            </tr>`
        })
        
        return orderDetailHtml
    }

    // 塞訂單執行動作button
    const insertOrderAction = (orderStatus) => {
        let orderActionHtml = ``
        switch(orderStatus){
            // 訂單狀態 1->確認中、2->訂單成立處理中、3->已出貨、4->已送達、5->訂單完成、6->訂單取消申請、7->訂單退貨申請、8->訂單取消、9->訂單退貨
            // 所以只有訂單狀態 1、2、6會需要再action區塊塞按鈕
            case 1:
                orderActionHtml = `<div class="col-4">
                                       <button class="btn btn-outline-success" style="width: 100%;" onclick="confirmOrder()">訂單成立</button>
                                   </div>
                                   <div class="col-4 mt-3">
                                       <button class="btn btn-outline-danger" style="width: 100%;" onclick="cancelOrder()">訂單取消</button>
                                   </div>` 
            break;

            case 2:
                orderActionHtml = `<div class="col-4 mt-3">
                                       <button class="btn btn-outline-danger" style="width: 100%;" onclick="cancelOrder()">訂單取消</button>
                                   </div>`
            break;

            case 6:
                orderActionHtml = `<div class="col-4 mt-3">
                                       <button class="btn btn-outline-danger" style="width: 100%;" onclick="cancelOrder()">訂單取消/確認</button>
                                   </div>`
            break;

            case 7:
                orderActionHtml = `<div class="col-4 mt-3">
                                       <button class="btn btn-outline-danger" style="width: 100%;" onclick="returnOrder()">訂單退貨/確認</button>
                                   </div>`
            break;
        }

        $('#orderAction').html(orderActionHtml)

        
    }

    getOrderFullData()

    // 訂單成立
    const confirmOrder = async () => {
        let orderNumber = getOrderNumber()
        let response = await axios.patch("{{route('confirmOrder')}}",{
            'orderNumber': orderNumber
        })

        if(response.data){
            getOrderFullData()
        }
    }

    // 訂單取消
    const cancelOrder = async () => {
        let orderNumber = getOrderNumber()
        let response = await axios.patch("{{route('cancelOrder')}}",{
            'orderNumber': orderNumber
        })

        if(response.data){
            getOrderFullData()
        }
    }

    // 取消訂單
    const returnOrder = async () => {
        let orderNumber = getOrderNumber()
        let response = await axios.patch("{{route('returnOrder')}}",{
            'orderNumber': orderNumber
        })

        if(response.data){
            getOrderFullData()
        }
    }

</script>

@endsection