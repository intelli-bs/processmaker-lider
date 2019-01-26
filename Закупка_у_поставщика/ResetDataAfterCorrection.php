<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:15 PM
 */
$items = @=ItemsList;
$i = 1;
foreach($items as $item)
{
    if($items[$i]['new_quantity'] != '')
    {
        @%items_list_corrected = 1;
        $items[$i]['quantity'] = $items[$i]['new_quantity'];
    }

    if($items[$i]['new_price'] != '')
    {
        $items[$i]['price'] = $items[$i]['new_price'];
        @%items_list_corrected = 1;
    }
    $i++;
}
@=ItemsList = $items;
@@InvoiceCompliance = 'valid';