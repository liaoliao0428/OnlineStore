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
            <input id="date" type="month" onchange="invoiceHtmlInsert()">
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
                                        <select name="invoiceYear" id="invoiceYear" class="form-select">
                                        </select>
                                    </div>
                                    <div class="d-flex align-items-center my-2">
                                        <h5 class="fw-bold my-0" style="min-width:100px">月　　份</h5>
                                        <select name="invoiceTerm" id="invoiceTerm" class="form-select">
                                            <option value="1">1月-2月</option>
                                            <option value="2">3月-4月</option>
                                            <option value="3">5月-6月</option>
                                            <option value="4">7月-8月</option>
                                            <option value="5">9月-10月</option>
                                            <option value="6">11月-12月</option>
                                        </select>
                                    </div>
                                    <div class="d-flex align-items-center my-2">
                                        <h5 class="fw-bold my-0" style="min-width:100px">字　　軌</h5>
                                        <input type="text" id="invoiceHeader" name="invoiceHeader" class="form-control" placeholder="輸入發票字軌" autocomplete="off" required="required">
                                    </div>
                                    <div class="d-flex align-items-center my-2">
                                        <h5 class="fw-bold my-0" style="min-width:100px">起始號碼</h5>
                                        <input type="number" id="invoiceStart" name="invoiceStart" class="form-control" placeholder="輸入發票起始號碼" autocomplete="off" required="required">
                                    </div>
                                    <div class="d-flex align-items-center my-2">
                                        <h5 class="fw-bold my-0" style="min-width:100px">結束號碼</h5>
                                        <input type="number" id="invoiceEnd" name="invoiceEnd" class="form-control" placeholder="輸入發票結束號碼" autocomplete="off" required="required">
                                    </div>
                                    <div class="d-flex align-items-center my-2">
                                        <h5 class="fw-bold my-0" style="min-width:100px">發票匯入</h5>
                                        <input type="file" name="invoice_excel" class="form-control" id="invoice_excel">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                                    <button type="button" class="btn btn-primary" onclick="submitInvoice()">送出</button>
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
            <!-- 發票列表 -->
            <table class="table table-bordered mb-0" id="invoice">
                <!-- 外框欄位 -->
                <thead>
                    <tr>
                        <th scope="col" width="9%" class="text-center fs-4">發票年度</th>
                        <th scope="col" width="9%" class="text-center fs-4">發票期別</th>
                        <th scope="col" width="9%" class="text-center fs-4">發票類別</th>
                        <th scope="col" width="15%" class="text-center fs-4">字軌類別</th>
                        <th scope="col" width="9%" class="text-center fs-4">字軌名稱</th>
                        <th scope="col" width="10%" class="text-center fs-4">起始號碼</th>
                        <th scope="col" width="10%" class="text-center fs-4">結束號碼</th>
                        <th scope="col" width="12%" class="text-center fs-4">目前已使用號碼</th>
                        <th scope="col" width="9%" class="text-center fs-4">使用狀態</th>
                        <th scope="col" width="8%" class="text-center fs-4">更改狀態</th>
                    </tr>
                </thead>
                <!-- 發票內容 -->
                <tbody id="invoiceData" class="align-middle text-center">
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

    // 手動新增發票內的年份填入選項
    $('select#invoiceYear').html(
            `<option value="${year}">${year}</option>
            <option value="${year+1}">${year+1}</option>`
        )
    
    // 手動新增發票內的月份填入選項
    switch (month) {
        case '01':
        case '02':
            $("select#invoiceTerm").val(1).attr("selected", "true");
        break;

        case '03':
        case '04':
            $("select#invoiceTerm").val(2).attr("selected", "true");
        break;

        case '05':
        case '06':
            $("select#invoiceTerm").val(3).attr("selected", "true");
        break;

        case '07':
        case '08':
            $("select#invoiceTerm").val(4).attr("selected", "true");
        break;

        case '09':
        case '10':
            $("select#invoiceTerm").val(5).attr("selected", "true");
        break;

        case '11':
        case '12':
            $("select#invoiceTerm").val(5).attr("selected", "true");
        break;
    }

/**************************************發票讀取csv檔案************************************ */
    $('#invoice_excel').change(function() {
        var fd = new FormData();
        let file = $('#invoice_excel')[0].files[0];
        fd.append('file', file); // since this is your file input
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: "{{route('import_invoice_excel_csv')}}", //讀入invoice.csv檔
            dataType: 'json',
            processData: false, // important
            contentType: false, // important
            mimeType: 'multipart/form-data',
            data: fd,
            success: function(res) {
                let invoiceYear = res.invoiceYear;
                let invoiceTerm = res.invoiceTerm;
                let invoiceHeader = res.invoiceHeader;
                let invoiceStart = res.invoiceStart;
                let invoiceEnd = res.invoiceEnd;
                
                $("select#invoiceYear").val(invoiceYear)
                $("select#invoiceTerm").val(invoiceTerm)
                $("input#invoiceHeader").val(invoiceHeader)
                $("input#invoiceStart").val(invoiceStart)
                $("input#invoiceEnd").val(invoiceEnd)            
            }
        });
    })
/**************************************發票讀取csv檔案************************************ */

/**************************************抓發票資料************************************ */
    // 查詢目前已登入發票
    const getInvoiceData = async () => {
        let nowTime = $("#date").val()
        let time = nowTime.split('-');
        let year = time[0] - 1911
        let month = time[1]
        let invoiceTerm = 0

        switch (month) {
            case '01':
            case '02':
                invoiceTerm = 1
            break;

            case '03':
            case '04':
                invoiceTerm = 2
            break;

            case '05':
            case '06':
                invoiceTerm = 3
            break;

            case '07':
            case '08':
                invoiceTerm = 4
            break;

            case '09':
            case '10':
                invoiceTerm = 5
            break;

            case '11':
            case '12':
                invoiceTerm = 6
            break;
        }
        
        let response = await axios.post("{{route('invoice')}}",{
            'year': year,
            'invoiceTerm': invoiceTerm
        })
        return response.data.invoice.Data.InvoiceInfo
    }

    // 發票資料html
    const invoiceHtml = async () => {
        let invoices = await getInvoiceData()
        let html = ``
        if (invoices) {
            invoices.forEach((invoice) => {
                // 發票id
                let TrackID = invoice.TrackID 

                // 判斷發票期別
                let invoiceTerm = invoice.InvoiceTerm
                let month = invoiceMonth(invoiceTerm)

                // 發票使用狀態判斷
                let UseStatus = invoice.UseStatus
                let status = invoiceStatus(UseStatus)                           

                html += `<tr>
                            <th scope="row">${invoice.InvoiceYear}</th>
                            <td>${month}</td>
                            <td>B2C</td>
                            <td>07-一般稅額計算之電子發票</td>
                            <td>${invoice.InvoiceHeader}</td>
                            <td>${invoice.InvoiceStart}</td>
                            <td>${invoice.InvoiceEnd}</td>
                            <td>${invoice.InvoiceNo}</td>
                            <td>${status}</td>
                            <td>
                                <select name="invoiceStatus${TrackID}" id="invoiceStatus${TrackID}" class="form-select" onchange="updateInvoicStatus('${TrackID}')">
                                    <option value="1">暫停</option>
                                    <option value="2">啟用</option>
                                </select>
                            </td>
                        </tr>`
            })
        }
        return html
    }

    // 判斷發票期別
    const invoiceMonth = (invoiceTerm) => {
        let month = ''
        switch(invoiceTerm){
            case 1:
                month = "1月 - 2月"
            break;

            case 2:
                month = "3月 - 4月"
            break;

            case 3:
                month = "5月 - 6月"
            break;

            case 4:
                month = "7月 - 8月"
            break;

            case 5:
                month = "9月 - 10月"
            break;

            case 6:
                month = "11月 - 12月"
            break;
        }
        return month
    }

    // 發票使用狀態判斷
    const invoiceStatus = (UseStatus) => {
        let status = ``
        switch(UseStatus){
            case 1:
                status = "未啟用"
            break;

            case 2:
                status = "使用中"
            break;

            case 3:
                status = "已停用"
            break;
                
            case 4:
                status = "暫停中"
            break;

            case 5:
                status = "待審核"
            break;

            case 6:
                status = "審核不通過"
            break;
        }
        return status
    }

    // 發票資料html塞入table
    const invoiceHtmlInsert = async () => {
        let html = await invoiceHtml()
        $("tBody#invoiceData").html(html)
    }

    invoiceHtmlInsert()
/**************************************抓發票資料************************************ */

/**************************************發票送出************************************ */
    const submitInvoice = async () => {
        // 字軌格式判斷
        let invoiceHeader =  $("input#invoiceHeader").val()
        let regExp = /[a-z A-Z]/;
        if(!invoiceHeader || invoiceHeader.length !==2 || !regExp.test(invoiceHeader)){
            alert('字軌格式錯誤')
            return null;
        }
        invoiceHeader = invoiceHeader.toUpperCase()

        // 發票起始號碼判斷
        let invoiceStart = $("input#invoiceStart").val()
        if(!invoiceStart || invoiceStart.trim().length !== 8){
            alert('發票起始號碼錯誤')
            return null;
        }

        //發票結束號碼判斷
        let invoiceEnd  = $("input#invoiceEnd").val()
        if(!invoiceEnd || invoiceEnd.trim().length !== 8){
            alert('發票結束號碼錯誤')
            return null;
        }
        
        $('#loading').css({
            'display': 'block'
        });
        $('#invoiceModal button').attr("disabled", true);

        let invoiceYear = $("select#invoiceYear").val() - 1911
        let invoiceTerm = $("select#invoiceTerm").val()

        let response = await axios.post("{{route('addInvoice')}}",{
            'invoiceYear': invoiceYear,
            'invoiceTerm': invoiceTerm,
            'invoiceHeader': invoiceHeader,
            'invoiceStart': invoiceStart,
            'invoiceEnd': invoiceEnd,
        })

        if(response.data.response.Data.RtnCode != 1){
            alert(response.data.response.Data.RtnMsg)
        }
        window.location.reload()       
    }
/**************************************發票送出************************************ */

// 發票更改狀態
const updateInvoicStatus = async (TrackID) => {
    let invoiceStatus = 'invoiceStatus' + TrackID
    let status = $(`select#${invoiceStatus}`).val()
    let response = await axios.post("{{route('updateInvoicStatus')}}",{
        'trackId': TrackID,
        'invoiceStatus': status
    })
    console.log(response);
    invoiceHtmlInsert()
}


</script>
@endsection