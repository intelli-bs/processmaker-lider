<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:38 PM
 */
$suppliers = @=AnaloguesList;
foreach ($suppliers as $supplier) {
    if($supplier['offer_type'] == 'new' || $supplier['offer_type'] =='analogue'){
        $new = preg_replace('/\s\s+/', ' ', $supplier['analogue_supplier']);
        if($new != $supplier['supplier']){
            $new = addslashes(trim($new));
            $date = date('Y-m-d');
            $code = $supplier['ang_suppl_1s_code'];
            $query = "INSERT INTO PMT_SUPPLIERS (NAME,CREATED_AT,CODE) VALUES ('$new','$date','$code')";
            $sqlSupplier = "SELECT * FROM PMT_SUPPLIERS WHERE NAME ='$new'";
            $Supplier = executeQuery($sqlSupplier);
            if(count($Supplier) < 1){
                executeQuery($query);
            }
        }

    }
}