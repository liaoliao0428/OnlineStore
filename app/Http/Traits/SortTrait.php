<?

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\SortModel;

trait SortTrait
{
    #排序控制模組
    /**
     * 
     * 排序更動  方法 : 1->新增、2->編輯、3->刪除 
     * 欄位(方法、資料表、分組欄位、排序欄位、id、舊的分組id(編輯才會用到)、舊的排序(編輯才會用到))
     * 新增資料取得id方法 寫入使用 ( return DB::table('資料表')->insertGetId(寫入的資料); ) 或是 使用id = model::create(寫入的資料) 取得$id->id
     * 
     * 新增、編輯時此語句使用在程式碼的最後面，資料新增上去以及資料更新完後才插入此語句
     * 刪除時此語句使用在刪除語句的前面，否則將會報錯
     * 
     * 新增使用sortArrange(1,'資料表','分組欄位','排序欄位',需更動資料的id);
     * 編輯使用sortArrange(2,'資料表','分組欄位','排序欄位',需更動資料的id,舊的分組id(編輯才會用到),舊的排序(編輯才會用到));
     * 刪除使用sortArrange(3,'資料表','分組欄位','排序欄位',需更動資料的id);
     * 
     */   
    public function sortArrange($method,$table,$identifyField,$sortField,$uniqueIdField,$uniqueId,$oldIdentify=null,$oldSort=null)
    {
        $dataNeedToChange = SortModel::select_table_with_id_db($table,$identifyField,$sortField,$uniqueIdField,$uniqueId);   //抓出要更動的資料基本內容
        $id = $dataNeedToChange[0]->id;
        $identify = $dataNeedToChange[0]->$identifyField;
        $sort = $dataNeedToChange[0]->$sortField;
        
        switch($method){
            case 1:
                $this->insertSort($table,$identifyField,$sortField,$identify,$sort,$id);    //新增時排序
            break;

            case 2:
                $this->updateSort($table,$identifyField,$sortField,$identify,$sort,$oldIdentify,$oldSort,$id);  //編輯時排序
            break;

            case 3:
                $this->deleteSort($table,$identifyField,$sortField,$identify,$sort,$id);    //刪除時排序
            break;
        }
    }

    //新增時排序
    public function insertSort($table,$identifyField,$sortField,$identify,$sort,$id)
    {
        SortModel::update_table_sort_need_to_arrange_db($table,$identifyField,$sortField,$identify,$sort,1,$id);  //內容依照新增內容的排訊向下遞減重新排序
    }

    //刪除時排序
    public function deleteSort($table,$identifyField,$sortField,$identify,$sort,$id)
    {
        SortModel::update_table_sort_need_to_arrange_db($table,$identifyField,$sortField,$identify,$sort,-1,$id); //需要重新安排的排序 刪除動作，剩下的課程順序往前排 排序-1
    }

    //編輯時排序
    public function updateSort($table,$identifyField,$sortField,$identify,$sort,$oldIdentify,$oldSort,$id)
    {
        if($identify == $oldIdentify){ 
            if($sort < $oldSort){  //新的排序小於舊的排序 排序往前 要編輯的項目要往前排的話就用這個
                SortModel::update_table_sort_forward_interval_need_to_arrange_db($table,$identifyField,$sortField,$identify,$sort,$oldSort,1,$id);
            }elseif($sort > $oldSort){  //新的排序大於舊的排序 排序往後 要編輯的項目要往後排的話就用這個
                SortModel::update_table_sort_back_interval_need_to_arrange_db($table,$identifyField,$sortField,$identify,$sort,$oldSort,-1,$id);
            }
        }else{
            SortModel::update_table_sort_need_to_arrange_db($table,$identifyField,$sortField,$identify,$sort,1,$id); //課程依照新增課程的排訊往後排 排序+1
            SortModel::update_table_sort_need_to_arrange_db($table,$identifyField,$sortField,$oldIdentify,$oldSort,-1,$id);    //舊的品牌分類的課程自動排序 剩下的往前排 排序-1
        }
    }
    #排序控制模組
}
