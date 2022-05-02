<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SortModel extends Model
{      
    // 抓出要更動的資料基本內容
    public static function select_table_with_id_db($table,$identifyField,$sortField,$uniqueIdField,$uniqueId)
    {
        return DB::select("SELECT id , $identifyField , $sortField FROM $table WHERE $uniqueIdField = '$uniqueId'");
    }

    // 需要重新安排的排序 有指定區間 //撈出符合往前排需要改動排序的課程 *往前排 新排序 <= (courseId != $courseId)的排序 < 舊排序 (要編輯的項目要往前排的話就用這個)
    public static function update_table_sort_forward_interval_need_to_arrange_db($table,$identifyField,$sortField,$identify,$sort,$oldSort,$moveType,$id)
    {
        DB::select("UPDATE $table
                    SET $sortField = $sortField + $moveType
                    WHERE $identifyField = '$identify' AND $sortField >= $sort AND $sortField < $oldSort AND id != $id");
    }

    // 需要重新安排的排序 有指定區間 //撈出符合往前排需要改動排序的課程 *往後排 新排序 >= (courseId != $courseId)的排序 > 舊排序 (要編輯的項目要往後排的話就用這個)
    public static function update_table_sort_back_interval_need_to_arrange_db($table,$identifyField,$sortField,$identify,$sort,$oldSort,$moveType,$id)
    {
        DB::select("UPDATE $table
                    SET $sortField = $sortField + $moveType
                    WHERE $identifyField = '$identify' AND $sortField <= $sort AND $sortField > $oldSort AND id != $id");
    }

    // 需要重新安排的排序
    public static function update_table_sort_need_to_arrange_db($table,$identifyField,$sortField,$identify,$sort,$moveType,$id)
    {
        DB::select("UPDATE $table
                    SET $sortField = $sortField + $moveType
                    WHERE $identifyField = '$identify' AND $sortField >= $sort AND id != $id");
    }
    
}
