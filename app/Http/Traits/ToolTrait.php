<?

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait ToolTrait
{
    /*********************************隨機數"****************************************** */
    //隨機數
    public function randomNumber($y)
    {
        $random_num = '';
        for ($i = 0; $i < $y; $i++) {
            $random_num = $random_num . rand(0, 9);
        }
        return $random_num;
    }
    /*********************************隨機數"****************************************** */

    /*********************************隨機字串"****************************************** */
    //隨機字串
    public function bin2hex($i)
    {
        $bin2hex = bin2hex(random_bytes($i));
        return $bin2hex;
    }

    // function randomString($length){
    //     $rand_string = '';
    //     for($i = 0; $i < $length; $i++) {
    //         $number = random_int(0, 36);
    //         $character = base_convert($number, 10, 36);
    //         $rand_string .= $character;
    //     }
     
    //     return $rand_string;      
    // }

    public function randomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if (!is_int($length) || $length < 0) {
            return false;
        }
        $characters_length = strlen($characters) - 1;
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, $characters_length)];
        }
        return $string;
    }
    /*********************************隨機字串"****************************************** */





    /*********************************檔案動作(上傳刪除更新判斷) filePath example : "exampleUrl1/exampleUrl2"****************************************** */
    //檔案動作
    public function file(Request $request)
    {
        /*******************上傳題目檔案 filePath example : "exampleUrl1/exampleUrl2" ********************* */
        $file = $request->file('fileName'); //取得上傳之圖片
        $filePath = "exampleUrl1/exampleUrl2";
        $fileName = $this->fileAction(1, $file, $filePath); //檔案動作(上傳刪除更新判斷) 回傳檔案名稱
    }

    public function fileAction($type, $file, $filePath, $oldFileName = null) //1->上傳、2->更新、3->刪除
    {
        $fileName = null;
        switch ($type) {
            case 1: //檔案上傳
                if (!empty($file)) {
                    $file->store("public/" . $filePath); //檔案存入資料夾
                    $fileName = $file->hashname(); //取得檔名
                }
                break;

            case 2: //檔案更新
                if (!empty($file)) {
                    $file->store("public/" . $filePath); //檔案存入資料夾
                    $fileName = $file->hashname(); //取得檔名
                    // rename("$old_path_name","$new_path_name");    //改檔名
                    storage::delete("/public/" . $filePath . '/' . $oldFileName); //舊檔案刪除
                } else {
                    $fileName = $oldFileName;
                }
                break;

            case 3: //檔案刪除
                storage::delete("/public/" . $filePath . '/' . $oldFileName); //檔案刪除
                break;
        }
        return $fileName;
    }
    /*********************************檔案上傳刪除更新判斷****************************************** */

    /*********************************頁面跳轉****************************************** */
    public function talk($mess, $url, $method)  //$mess要show出來的話、$url要跳轉的頁面、$method方法
    {
        echo '<!doctype html><meta charset="utf-8">';
        switch ($method) {
            case 1:
                echo "<script>alert('$mess');window.history.back();</script>";
                break;

            case 2:
                echo "<script>location.href='" . $url . "';</script>";
                break;

            case 3:
                echo "<script>alert('$mess');location.href='" . $url . "';</script>";
                break;

            case 4:
                echo "<script>alert('$mess');window.top.location.reload();</script>";
                break;

            case 5:
                echo "<script>alert('$mess');window.top.location.href='" . $url . "';</script>";
                break;

            case 6:
                echo "<script>alert('$mess')</script>";
                break;

            case 7:
                echo "<script>window.history.back();</script>";
                break;
        }
    }
    /*********************************頁面跳轉****************************************** */

    /*********************************base64圖片上傳****************************************** */
    /**
     * base64 圖片解碼上傳
     * 
     * base64Upload($base64File,$fileName,$filePath);
     * 
     * $base64File => base64檔案
     * $fileName  example: 'exampleFileName'
     * $filePath  example: 'example1Folder/example2Folder'
     */
    public function base64Upload($base64File, $fileName, $filePath)
    {
        //取出base64 副檔名
        $extension = explode('/', explode(':', substr($base64File, 0, strpos($base64File, ';')))[1])[1];
        // find substring fro replace here eg: data:image/png;base64,
        $replace = substr($base64File, 0, strpos($base64File, ',') + 1);
        $file = str_replace($replace, '', $base64File);
        $file = str_replace(' ', '+', $file);
        $file = base64_decode($file);

        //把檔名跟檔案路徑組合
        $combineFileName = $fileName . '.' . $extension;
        $combineFilePath = $filePath . '/' . $combineFileName;

        //上傳base64檔案 並回傳檔名
        Storage::disk('public')->put($combineFilePath, $file);    //到目錄底下建立資料夾並存入發票圖片
        // $finalFilename = Storage::disk('public')->path($combineFilePath);    //取出圖片完整路徑 看情況使用
        return $combineFileName;
    }
    /*********************************base64圖片上傳****************************************** */

    //抓hostName
    public function defineHostName(Request $request)
    {
        $host = $request->getSchemeAndHttpHost();
        return $host;
    }
}
