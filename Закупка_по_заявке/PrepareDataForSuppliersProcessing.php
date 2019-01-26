<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 10:57 PM
 */
try{
    @%SuppliersToBeProceed = 0;
    $i = 1;
    $suppliers = [];

    foreach(@=IngoingItemsList as $item)
    {
        $supplier = $item['supplier_code'];
        if($supplier != null && $supplier !== ''
            && !in_array($supplier, $suppliers))
        {
            @=SuppliersList[$i] = $supplier;
            $i++;
            array_push($suppliers,$supplier);
        }
    }
    @%SuppliersToBeProceed = count($suppliers);
}catch(Exception $e){
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    exit(0);
}