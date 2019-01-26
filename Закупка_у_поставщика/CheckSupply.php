<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:14 PM
 */
@=SupplyCorrect = true;
$problematicItems = array();
$validItems = array();
$i = 1;
$j = 1;
foreach(@=ItemsList as $item)
{
    if($item['supply_state'] != 'correct')
    {
        $problematicItems[$i]['p_code'] = $item['code'];
        $problematicItems[$i]['p_title'] = $item['title'];
        $problematicItems[$i]['p_unit'] = $item['unit'];
        $problematicItems[$i]['p_quantity'] = $item['quantity'];
        $problematicItems[$i]['p_supplied'] = $item['supplied'];
        $problematicItems[$i]['p_price'] = $item['price'];
        $problematicItems[$i]['p_supply_state'] = $item['supply_state'];
        $problematicItems[$i]['p_supply_state_label'] = $item['supply_state_label'];
        $problematicItems[$i]['p_supply_comment'] = $item['supply_comment'];
        $i++;
    }
    else
    {
        $validItems[$j] = $item;
        $j++;
    }
}

if(count($problematicItems) > 0)
{
    @=SupplyCorrect = false;
    @#payment_amount = 0;
}
else
{
    @#payment_amount = @#invoice_amount - @#prepayment_amount - @#payment_on_readiness_amount;
}

@=ValidItemsList = $validItems;
@=ProblematicItemsList = $problematicItems;