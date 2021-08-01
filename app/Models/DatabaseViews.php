<?php
namespace App\Models;

class DatabaseViews{
    public static function shipments_view() {
        return "(SELECT shipments.*, customers.*, client_name, client_code, client_followupName, client_followupPhone, zone_name, zone_fees, driver_name,method_code,method_name,method_manualCollect,
        (SELECT COUNT(*) FROM shipment_items WHERE shItem_shipmentId = shipment_id) AS itemCount,
        IFNULL((SELECT SUM(IFNULL(shItem_price,0)*IFNULL(shItem_qty,0)) FROM shipment_items WHERE shItem_shipmentId = shipment_id ),0) AS totalItemValue,
        IFNULL((SELECT SUM(IFNULL(shItem_price,0)*IFNULL(shItem_qty,0)) FROM shipment_items WHERE shItem_shipmentId = shipment_id AND shItem_status = 1),0) AS totalDeliveredItemValue,
        (CASE WHEN IFNULL(shipment_driverId,0) > 0 THEN 1 ELSE 0 END) AS isPlanned
        FROM shipments
        LEFT JOIN customers ON shipment_customerId = customer_id
        LEFT JOIN clients ON shipment_clientId = client_id
        LEFT JOIN zones ON shipment_zoneId = zone_id
        LEFT JOIN def_payment_methods ON shipment_paymentMethodId = method_id
        LEFT JOIN drivers ON shipment_driverId = driver_id) AS shipments_view";
    }

    // public static function plans_view() {
    //     return "(SELECT shipments_view.*, plans.*, driver_name, driver_phone, driver_email
    //     FROM ".self::shipments_view()."
    //     LEFT JOIN plans ON shipment_id = plan_shipmentId
    //     LEFT JOIN drivers ON plan_driverId = driver_id) AS plans_view";
    // }

    public static function drivers_view() {
        return "(SELECT *,
        IFNULL((SELECT SUM(remainingQty) FROM ".self::custody_view()." WHERE custody_driverId=driver_id),0) AS totalCustody,
        IFNULL((SELECT SUM(totalDeliveredItemValue)+SUM(shipment_fees)-SUM(IFNULL(shipment_driverPaid,0))
            FROM ".self::shipments_view()."
            WHERE shipment_driverId = driver_id AND shipment_status = 3 AND shipment_paymentMethodId <> 3),0) AS totalCash
        FROM drivers) AS drivers_view";
    }

    public static function custody_view() {
        return "(SELECT *,
        IFNULL(SUM(IFNULL(custody_qty,0)-IFNULL(custody_usedQty,0)),0) AS remainingQty
        FROM custody
        GROUP BY custody_id,custody_driverId,custody_sku,custody_usedQty,custody_qty) AS custody_view";
    }

    public static function custody_items_view() {
        return "(SELECT *
        FROM ".self::custody_view()."
        LEFT JOIN ". self::items_view() ." ON custody_sku = sku) AS custody_items_view";
    }

    public static function shipment_items_view() {
        return "(SELECT shipment_id, shipment_status, shipment_driverId, shipment_clientId, shipment_customerId, shipment_items.*,items_view.*,
        (SELECT SUM(IFNULL(custody_qty,0)-IFNULL(custody_usedQty,0)) FROM custody WHERE custody_driverId = shipment_driverId AND custody_sku = sku) AS custody
        FROM shipment_items
        LEFT JOIN ". self::items_view() ." ON shItem_sku = sku
        LEFT JOIN ".self::shipments_view()." ON shItem_shipmentId = shipment_id) AS shipment_items_view";
    }

    public static function clients_view() {
        return "(SELECT *,
        (SELECT COUNT(*) FROM shipments WHERE shipment_clientId = client_id) AS shipmentCount
        FROM clients) AS clients_view";
    }

    public static function zones_view() {
        return "(SELECT *,
        IFNULL((SELECT COUNT(*) FROM zone_regions WHERE region_zoneId = zone_id),0) AS regionCount,
        IFNULL((SELECT COUNT(*) FROM shipments WHERE shipment_zoneId = zone_id),0) AS shipmentCount
        FROM zones) AS zones_view";
    }

    public static function customers_view() {
        return "(SELECT *,
        (SELECT COUNT(*) FROM shipments WHERE shipment_customerId = customer_id) AS shipmentCount
        FROM customers) AS customers_view";
    }

    public static function items_view() {
        return "(SELECT items.*,client_name,variation_name ,
        (CASE WHEN iv_variationSku IS NULL THEN item_sku ELSE iv_variationSku END) AS sku,
        (CASE WHEN variation_name IS NULL OR trim(variation_name) = '' THEN item_name ELSE CONCAT(item_name,' - ',variation_name) END) AS fullItemName
        FROM items
        LEFT JOIN clients ON item_clientId = client_id
        LEFT JOIN item_variations ON item_sku = iv_itemSku
        LEFT JOIN variations ON iv_variationId = variation_id) AS items_view";
    }

    public static function item_variations_view() {
        return "(SELECT * FROM item_variations
        LEFT JOIN variations ON iv_variationId = variation_id) AS items_view";
    }

}



?>
