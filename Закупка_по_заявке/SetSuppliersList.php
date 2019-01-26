<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:02 PM
 */
@=SuppliersList = array();
$i = 1;
$suppliers = array();
foreach(@=IngoingItemsList as $item)
{
    $supplier = $item['item_supplier'];
    if(!in_array($supplier, $suppliers))
    {
        @=SuppliersList[$i] = array('supplier_name' => $supplier);
        $i++;
        array_push($suppliers,$supplier);
    }
}
unset($suppliers);