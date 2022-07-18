<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class UserReceiveAddress extends Model
{
    //指定資料表
    protected $table = 'user_receive_address';

    //時間戳欄位
    const CREATED_AT = 'createTime';
    const UPDATED_AT = 'updateTime';

    /**
     * 可以被批量賦值的屬性
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'receiveAddressId',
        'userId',
        'receiverName',
        'receiverCellPhone',
        'receiverStoreType',
        'receiverStoreName',
        'receiverStoreID',
        'receiverAddress',
        'defaultAddress',
    ];
}

class UserReceiveAddressModel
{
    // 新增
    public static function insert_user_receive_address_db($userReceiveAddress)
    {
        UserReceiveAddress::create($userReceiveAddress);
    }

    // 編輯
    public static function update_user_receive_address_db($receiveAddressId , $userReceiveAddress)
    {
        UserReceiveAddress::where('receiveAddressId',$receiveAddressId)->update($userReceiveAddress);
    }

    // 刪除
    public static function delete_user_receive_address_db($receiveAddressId)
    {
        UserReceiveAddress::where('receiveAddressId',$receiveAddressId)->delete();
    }

    // 把原本的預設的變成0
    public static function update_user_receive_address_where_defaultAddress1_db($userId)
    {
        UserReceiveAddress::where('userId',$userId)->where('defaultAddress',1)->update(['defaultAddress' => 0]);
    }

    // 撈資料
    public static function select_user_receive_address_db($userId)
    {
        return DB::select("SELECT * FROM user_receive_address WHERE userId = '$userId' ORDER BY createTime ASC");
    }

    // 撈使用者預設地址
    public static function select_user_receive_address_default_db($userId)
    {
        return DB::select("SELECT * FROM user_receive_address WHERE userId = '$userId' AND defaultAddress = 1");
    }

    // 撈指定receiveAddressId的地址
    public static function selete_user_receive_address_where_receiveAddressId_db($receiveAddressId)
    {
        return DB::select("SELECT * FROM user_receive_address WHERE receiveAddressId = '$receiveAddressId'");
    }
}


