<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\DB;

class DefinitionModel
{

    public static function getAllPaymentMethods()
    {
        $query = DB::table('def_payment_methods')
            ->select('*')->get();

        return $query;
    }

    public static function getPaymentMethodByCode($code)
    {
        $query = DB::table('def_payment_methods')
            ->select('*')
            ->where("method_code","=",$code)
            ->first();

        return $query;
    }

    public static function savePaymentMethod(array $payMethod)
    {
        try {

            $isNew = empty($data->deftitle_id);

            $result = "";
            if ($isNew) {
                // if ($this->db->insert('def_course_titles', $data))
                //     $result = $this->db->insert_id();

                $titleid = DB::table('def_course_titles')->insertGetId($data);
            } else {
                // $this->db->where('deftitle_id', $data->deftitle_id);
                // if ($this->db->update('def_course_titles', $data))
                //     $result = $data->deftitle_id;

                $affected = DB::table('def_course_titles')
                    ->where('deftitle_id', $data->deftitle_id)
                    ->update($data);

                $result = $data->deftitle_id;
            }

            return (empty($result) ? "Error Saving Course!" : true);
        } catch (Exception $e) {
            return "Error Saving Course!";
        }
    }

    public static function isTitleInUse($courseTitleId)
    {
        $row = DB::table(DB::raw(DatabaseViews::sessions_view()))
            ->selectRaw('COUNT(*) AS c')
            ->where("course_titleId", "=", $courseTitleId)
            ->first();

        //		$test = $this->db->get_compiled_select();

        return ($row->c > 0);
    }

    public static function deleteCourseTitle($id)
    {

        if (self::isTitleInUse($id)) {
            return "Can't delete. Course Title In Use!";
        }
        $result = DB::table('def_course_titles')
            ->where('deftitle_id', $id)
            ->delete();
        if (!$result) {
            return $result;
        }

        return true;
    }

}
