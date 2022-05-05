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
    <hr/>
    <div class="col-8 mx-auto">
        <div class="card">
            <div class="card-body row">
                <div class="col-6 border-end">
                    <p>123</p>
                </div>
                <div class="col-6">
                    <p>456</p>
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