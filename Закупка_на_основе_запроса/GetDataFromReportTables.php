<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:11 PM
 */
$purchases = [];
$suppliers = [];
$search = [];
@@ProcessComplete = 'inprogress';
$sqlPurchase = "SELECT * FROM PMT_PURCHASE_USER_MONITORING
WHERE PARENT_PROCESS_ID = '".@@APPLICATION."'";

$sqlSuppliers = "SELECT * FROM PMT_PURCHASE_SUPPLIER_MONITORING
WHERE MAIN_PARENT_PROCESS_ID = '".@@APPLICATION."'";

$sqlSearch = "SELECT * FROM PMT_PURCHASE_USER_SEARCH_MONITORING
WHERE MAIN_PARENT_PROCESS_ID = '".@@APPLICATION."' AND APP_STATUS = 'TO_DO'";

try{
    $purchases = executeQuery($sqlPurchase);
    $suppliers = executeQuery($sqlSuppliers);
    $search = executeQuery($sqlSearch);

}catch (Exception $e){
    $g = new G();
    $g->SendMessageText("Execution of queries ".$sqlPurchase." or ".$sqlSuppliers." failed!","ERROR");
    return;
}

$purchases_qty = count($purchases);
$completed = 0;
if ($purchases_qty == 0){
    $g = new G();
    $g->SendMessageText("Отсутствуют сведенья о процессе в базе данных","ERROR");
    return true;
}else{
    $i = 1;
    foreach($purchases as $purchase)
    {
        @=PurchaseProgress[$i]["performer"] = $purchase['PERFORMER_NAME'];
        @=PurchaseProgress[$i]["process"] = $purchase['PROCESS_NAME'];
        switch ($purchase['APP_STATUS']) {
            case 'TO_DO':
                @=PurchaseProgress[$i]["status"] = 'В процессе';
                break;
            case 'PAUSED':
                @=PurchaseProgress[$i]["status"] = 'Приостановлено';
                break;
            case 'DRAFT':
                @=PurchaseProgress[$i]["status"] = 'Черновик';
                break;
            case 'CANCELLED':
                @=PurchaseProgress[$i]["status"] = 'Отменено';
                $completed++;
                break;
            case 'COMPLETED':
                $completed++;
                @=PurchaseProgress[$i]["status"] = 'Выполнено';
                @=PurchaseProgress[$i]["task"] = '-';
                break;
            case 'DELETED':
                @=PurchaseProgress[$i]["status"] = 'Удалено';
                $completed++;
                break;
        }
        if($purchase['PROCESS_NAME'] == 'Поиск новых предложений' && (is_array($search) && count($search) >0)){
            foreach($search as $item){
                if($item['PARENT_PROCESS_ID'] == $purchase['APP_UID']){
                    @=PurchaseProgress[$i]["task"] = $item['TASK_NAME'];
                    @=PurchaseProgress[$i]["created_at"] = $item['CREATED_AT'];
                }
            }
        }else{
            @=PurchaseProgress[$i]["task"] = $purchase['TASK_NAME'];
            @=PurchaseProgress[$i]["created_at"] = $purchase['CREATED_AT'];
        }

        $i++;

    }
}
if($completed === $purchases_qty){
    @@ProcessComplete = 'completed';
}

if (count($suppliers) !== 0){
    @@ProcessSupplierExist = 'existSuppliers';
    $i = 1;
    foreach($suppliers as $supplier){
        @=SuppliersProgress[$i]["performer_suppliers"] = $supplier['PERFORMER_NAME'];
        @=SuppliersProgress[$i]["task_suppliers"] = $supplier['TASK_NAME'];
        @=SuppliersProgress[$i]["created_at_suppliers"] = $supplier['CREATED_AT'];
        @=SuppliersProgress[$i]["supplier"] = $supplier['SUPPLIER_NAME'];
        switch ($supplier['APP_STATUS']) {
            case 'TO_DO':
                @=SuppliersProgress[$i]["status_suppliers"] = 'В процессе';
                break;
            case 'PAUSED':
                @=SuppliersProgress[$i]["status_suppliers"] = 'Приостановлено';
                break;
            case 'DRAFT':
                @=SuppliersProgress[$i]["status_suppliers"] = 'Черновик';
                break;
            case 'CANCELLED':
                @=SuppliersProgress[$i]["status_suppliers"] = 'Отменено';
                break;
            case 'COMPLETED':
                @=SuppliersProgress[$i]["status_suppliers"] = 'Выполнено';
                @=SuppliersProgress[$i]["task_suppliers"] = '-';
                break;
            case 'DELETED':
                @=SuppliersProgress[$i]["status_suppliers"] = 'Удалено';
                break;
        }
        $i++;
    }
}else{
    @@ProcessSupplierExist = 'noSuppliers';
}
