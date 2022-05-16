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
                        <p>訂單編號 : <input type="text" id="" value="" disabled></p>
                    </div>
                    <div id="date" class="col-12">
                        <p>訂單日期 : <input type="text" id="" value="" disabled></p>
                    </div>
                    <div id="" class="col-12">
                        <p>收件人 : <input type="text" id="" value="" style="margin-left: 14px;"></p>
                    </div>
                    <div id="date" class="col-12">
                        <p>收件電話 : <input type="text" id="" value=""></p>
                    </div>
                    <div id="" class="col-12">
                        <p>收件地址 : <input type="text" id="" value=""></p>
                    </div>
                    <div id="" class="col-12" >
                        <p>物流 :
                            <select type="text" id="" value="" style="margin-left: 28px;">
                                <option value="">自取</option>
                                <option value="">超商取貨</option>
                                <option value="">黑貓</option>
                            </select>
                        </p>
                    </div>
                    <div id="" class="col-12">
                        <p>寄送運費 : <input type="number" id="" value=""></p>
                    </div>
                    <div id="" class="col-12">
                        <p>訂單備註 : </p>
                        <textarea type="text" id="" value="" style="width: 48%;"></textarea>
                    </div>
                </div>
                <div class="col-6 px-4">
                    <div id="" class="col-12">
                        <p>訂單金額 : <input type="number" id="" value=""></p>
                    </div>
                    <div id="" class="col-12">
                        <p>付款方式 :
                            <select type="text" id="" value="">
                                <option value="">綠界信用卡</option>
                            </select>
                        </p>
                    </div>
                    <div id="" class="col-12">
                        <p>付款狀態 :
                            <select type="text" id="" value="">
                                <option value="">綠界信用卡</option>
                            </select>
                        </p>
                    </div>
                    <div id="" class="col-12">
                        <p>發票號碼 : <input type="text" id="" value="" disabled></p>
                    </div>
                    <div id="" class="col-12">
                        <p>發票統編 : <input type="text" id="" value=""></p>
                    </div>
                    <div id="" class="col-12">
                        <p>發票載具 : <input type="text" id="" value=""></p>
                    </div>
                    <div id="" class="col-12">
                        <p>發票捐贈 :
                            <select type="text" id="" value="">
                                <option value="">不捐贈</option>
                                <option value="">捐贈</option>
                            </select>
                        </p>
                    </div>
                    <div id="" class="col-12">
                        <p>訂單狀態 :
                            <select type="text" id="" value="">
                                <option value="">不捐贈</option>
                                <option value="">捐贈</option>
                            </select>
                        </p>
                    </div>
                    <div class="col-12">
                        <div class="col-4">
                            <button class="btn btn-outline-success" style="width: 100%;">訂單成立</button>
                        </div>
                        <div class="col-4 mt-3">
                            <button class="btn btn-outline-danger" style="width: 100%;">訂單取消</button>
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
                        <tbody id="order" class="align-middle text-center">
                            <tr>
                                <td>牛逼</td>
                                <td>笨蛋衣服</td>
                                <td>X</td>
                                <td>$100</td>
                                <td>10</td>
                                <td>$100000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>


@endsection
@section('script')

<script>

</script>

@endsection