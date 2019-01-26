<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 10:56 PM
 */
try{
    @=CurrentItems = array();
    $supplierIdx = @%SuppliersToBeProceed;
    $currentItems = array();
    $suppliers = @=SuppliersList;
    $supplier =  $suppliers[$supplierIdx];
    @@current_supplier_code = $supplier;

    //filling items of curent supplier
    $idx = 1;
    $supplierIsSet = false;
    foreach(@=IngoingItemsList as $item)
    {
        if($item['supplier_code'] == $supplier)
        {
            if(!$supplierIsSet)
            {
                @@CurrentSupplier = $item['supplier'];
                $supplierIsSet = true;
            }
            $currentItems[$idx] = $item;
            $idx++;
        }
    }
    @=CurrentItems = $currentItems;
    @%SuppliersToBeProceed--;
    @@task_name = "Закупка у поставщика";
    @@NextTask = "Проверка необходимости заключения договора";
    @%NextDelIndex = 1;
    @@NextCreatedAt = date('Y-m-d h:i:s');
    $userUID = @@PerformerId;
    $aUser = PMFInformationUser($userUID);
    @@performer_name = $aUser['firstname'].' '.$aUser['lastname'];
    @%items_list_corrected = 0;



}catch(Exception $e){
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    exit(0);
}