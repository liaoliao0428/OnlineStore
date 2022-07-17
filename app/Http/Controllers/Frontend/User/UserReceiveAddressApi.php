<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\UserReceiveAddressModel;

use App\Http\Traits\Ecpay\LogisticsTrait;
use App\Http\Traits\Ecpay\EcpayEncryptDecryptTrait;

use App\Http\Traits\ToolTrait;



class UserReceiveAddressApi extends Controller
{   
    //建構子 middleware設定
    public function __construct()
    {
        $this->middleware('frontAuthCheck')->except('ecpayLogisticsSelectionResponse');
    }

    // 撈資料
    public function userReceiveAddress(Request $request)
    {
        $userId = $request->userId;
        $userReceiveAddresss = UserReceiveAddressModel::select_user_receive_address_db($userId);

        // 判斷物流類型
        $this->setreceiverStoreType($userReceiveAddresss);

        // 回傳
        if($userReceiveAddresss){
            return response()->json(['userReceiveAddress' => $userReceiveAddresss], Response::HTTP_OK);
        }else{
            return response()->json(['userReceiveAddress' => null], Response::HTTP_OK);
        }
    }

    // 判斷物流類型
    public function setreceiverStoreType($userReceiveAddresss)
    {
        foreach($userReceiveAddresss as $userReceiveAddress){
            $receiverStoreType = $userReceiveAddress->receiverStoreType;
            switch($receiverStoreType){
                case 'FAMI': case 'FAMIC2C':
                    $userReceiveAddress->receiverStoreType = '全家';
                break;

                case 'UNIMART': case 'UNIMARTFREEZE': case 'UNIMARTC2C':
                    $userReceiveAddress->receiverStoreType = '7-11';
                break;

                case 'HILIFE': case 'HILIFEC2C': case 'OKMARTC2C':
                    $userReceiveAddress->receiverStoreType = '萊爾富';
                break;

                case 'OKMARTC2C': 
                    $userReceiveAddress->receiverStoreType = 'OK';
                break;
            }
        }
    }

    // 新增
    public function insert($userReceiveAddress)
    {
        UserReceiveAddressModel::insert_user_receive_address_db($userReceiveAddress);
    }

    // 刪除
    public function delete(Request $request)
    {
        $receiveAddressId = $request->receiveAddressId;

        UserReceiveAddressModel::delete_user_receive_address_db($receiveAddressId);
        return response()->json([true], Response::HTTP_OK);
    }

    // 改變預設地址
    public function changeDefaultReceiveAddress(Request $request)
    {
        $userId = $request->userId;
        $receiveAddressId = $request->receiveAddressId;
        $userReceiveAddress['defaultAddress'] = 1;

        UserReceiveAddressModel::update_user_receive_address_where_defaultAddress1_db($userId);
        UserReceiveAddressModel::update_user_receive_address_db($receiveAddressId , $userReceiveAddress);

        return response()->json([true], Response::HTTP_OK);
    }

    // 綠界物流開啟地圖選擇寄送地址
    public function ecpayLogisticsSelection(Request $request)
    {
        $userId = $request->userId;
        $userIdBase64Encode = base64_encode($userId);
        
        $clientReplyUrl = env('NGROK_TEST_DOMAIN') . '/OnlineStore/Backend/public/api/frontend/userReceiveAddress/ecpayLogisticsSelectionResponse/' . $userIdBase64Encode;
        
        LogisticsTrait::redirectToLogisticsSelection($clientReplyUrl);
    }

    // 綠界物流地址選擇結果回傳
    public function ecpayLogisticsSelectionResponse(Request $request , $userIdEncode)
    {
        $userId = base64_decode($userIdEncode);

        // 綠界回傳格式為json格式字串 所以先做 json_decode轉陣列
        $logisticsResponse = json_decode($request['ResultData'],true);

        // 裡面這一串資料有做aes加密 所以要在aes解密 並json_decode轉成陣列
        $logisticsData = EcpayEncryptDecryptTrait::ecpayAesDecrypt($logisticsResponse['Data']);
        $logisticsData = json_decode($logisticsData , true);

        // RtnCode == 1 代表選擇成功 寫入資料庫
        if($logisticsData['RtnCode'] == 1){

            $userReceiveAddress['receiveAddressId'] = ToolTrait::randomString(13);
            $userReceiveAddress['userId'] = $userId;
            $userReceiveAddress['receiverName'] = $logisticsData['ReceiverName'];
            $userReceiveAddress['receiverCellPhone'] = $logisticsData['ReceiverCellPhone'];
            $userReceiveAddress['receiverStoreType'] = $logisticsData['LogisticsSubType'];
            $userReceiveAddress['receiverStoreName'] = $logisticsData['ReceiverStoreName'];
            $userReceiveAddress['receiverStoreID'] = $logisticsData['ReceiverStoreID'];
            $userReceiveAddress['receiverAddress'] = $logisticsData['ReceiverAddress'];

            $this->insert($userReceiveAddress);
        }
        
        $clientBackUrl = 'http://localhost:3000/user/address';
        return redirect($clientBackUrl);
    }
}
