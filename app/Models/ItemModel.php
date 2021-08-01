<?php

namespace App\Models;

use App\Helpers\AppHelper;
use Exception;
use Illuminate\Support\Facades\DB;

class ItemModel
{

    // Read data using username and password

    public static function getAllItems($clientId = 0)
    {
        $query = DB::table(DB::raw(DatabaseViews::items_view()))
            ->select('*')
            ->orderBy('item_id', 'DESC');

        if (!empty($clientId)){
            $query->where("item_clientId",$clientId);
        }

        $query = $query->get();
        foreach($query as $item){
            AppHelper::setItemImage($item);
        }

        return $query;
    }

    public static function getItemByID($itemID)
    {
        $query = DB::table('items')
            ->select('*')
            //				 ->join('lawyer_profile lp', 'u.user_id = lp.lawyer_userID', 'left')
            ->where("item_id", "=", $itemID)
            ->first();

        if (!empty($query)) {
            AppHelper::setItemImage($query);
            return $query;
        } else {
            return "Error Getting item!";
        }
    }

    public static function getItemBySku($itemSku)
    {
        $query = DB::table('items')
            ->select('*')
            //				 ->join('lawyer_profile lp', 'u.user_id = lp.lawyer_userID', 'left')
            ->where("item_sku", "=", $itemSku)
            ->first();

        if (!empty($query)) {
            AppHelper::setItemImage($query);
            return $query;
        } else {
            return "Error Getting item!";
        }
    }

    public static function getItemVariationsBySku($itemSku)
    {
        $query = DB::table(DB::raw(DatabaseViews::item_variations_view()))
            ->select('*')
            ->where("iv_itemSku", "=", $itemSku)
            ->get();

        if (!empty($query)) {
            return $query;
        } else {
            return "Error Getting item Variations!";
        }
    }
    public static function insertItem(array $itemdata)
    {
        try {

            $query = DB::table(DB::raw(DatabaseViews::items_view()))
                ->select('item_id')
                ->where('item_sku' , $itemdata['item_sku'])
                ->where('item_clientId',$itemdata['item_clientId'])
                ->first();

            if (!empty($query->item_id)){
                DB::table('items')
                ->where('item_id', $query->item_id)
                ->update($itemdata);

                return $query->item_id;
            }
            else{
                $itemdata['item_created'] = date("Y-m-d H:i:s");
                return DB::table('items')->insertGetId($itemdata);
            }

        } catch (Exception $e) {
            return "Error Saving item!";
        }
    }

    public static function insertItemVariation(array $variationdata){

        try {

            $itemBySku = self::skuExists($variationdata['iv_variationSku']);
            if ($itemBySku !== false){
                return "SKU already exists!";
            }

            $result = DB::table('item_variations')->insertGetId($variationdata);

            return (empty($result) ? "Error Saving Variation!" : $result);

        } catch (Exception $e) {
            return "Error Saving item variation!";
        }

    }

    public static function saveItem(array $itemdata)
    {
        try {

            $isNew = empty($itemdata['item_id']) || $itemdata['item_id'] == 0;

            if ($isNew) {
                // $query = DB::table('items')
                //     ->select('item_id')
                //     ->where("item_name", "=", $itemdata['item_name'])
                //     ->first();

                if (self::skuExists($itemdata['item_sku']) != false){
                    return "SKU already exists!";
                }
            }

            $result = "";
            if ($isNew) {
                $item['item_created'] = date("Y-m-d H:i:s");
                $result = DB::table('items')->insertGetId($itemdata);
            } else {
                $item['item_updated'] = date("Y-m-d H:i:s");
                $affected = DB::table('items')
                ->where('item_id', $itemdata['item_id'])
                ->update($itemdata);
                $result = $itemdata['item_id'];
            }

            return $result;
        } catch (Exception $e) {
            return "Error Saving item!";
        }
    }

    public static function saveItemVariations($itemSku, array $variations){

        DB::beginTransaction();
        try {
            $rr = DB::table('item_variations')
            ->where('iv_itemSku',"=", $itemSku)
            ->delete();
            // if (!$result) {
            //     DB::rollBack();
            //     return $result;
            // }

            foreach($variations as $variationdata){
                if (self::skuExists($variationdata['iv_variationSku']) != false){
                    return "SKU already exists!";
                }
                $result = DB::table('item_variations')->insertGetId($variationdata);

                if (empty($result)){
                    DB::rollBack();
                    return "Error Saving item variation!";
                }

            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return "Error Saving item variation!";
        }


    }

    public static function deleteItem($id)
    {

        $result = DB::table('items')
            ->where('item_id', $id)
            ->delete();
        if (!$result) {
            return $result;
        }

        return true;
    }

    public static function skuExists($sku){
        $query = DB::table(DB::raw(DatabaseViews::items_view()))
        ->select('*')
        ->where("item_sku", "=", $sku)
        ->orWhere("iv_variationSku", "=", $sku)
        ->first();

        if (!empty($query)) {
            return $query;
        } else {
            return false;
        }
    }
}
