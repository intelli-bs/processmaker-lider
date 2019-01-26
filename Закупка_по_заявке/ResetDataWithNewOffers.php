<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 10:59 PM
 */
$items = @=IngoingItemsList;
$newOffers = @=NewOffersList;
$itemsCount = count($items);
$topPassedCount = 0;
$mgrPassedCount = 0;

for($i = 1; $i <= $itemsCount; $i++)
{
    $item = $items[$i];
    $code = $item['code'];
    foreach($newOffers as $newOffer)
    {
        $newCode = $newOffer['code'];
        $isSameItem = false;
        if($code == null || $code == '')
        {
            $itemTitle = $item['title'];
            $newItemTitle = $newOffer['title'];
            $isSameItem = $itemTitle == $newItemTitle;
        }
        else
        {
            $isSameItem = $code == $newCode;
        }

        $tender = $newOffer['tender_passed'];
        if($tender === 'yes' && $isSameItem)
        {
            $item['price'] = $newOffer['analogue_price'];
            if($newOffer['offer_type'] !== 'correction')
            {
                $item['supplier'] = $newOffer['analogue_supplier'];
                $item['supplier_code'] = $new_offer['new_supplier_code'];
            }
            if($newOffer['offer_type'] === 'analogue')
            {
                $item['title'] = $newOffer['analogue_title'];
                $item['code'] = $newOffer['ang_item_1s_code'];
            }
            $item['mgr_passed'] = 'yes';
            if($newOffer['top_mgr_tender_res'] == 'passed')
            {
                $item['top_passed'] = 'yes';
            }
            else
            {
                $item['top_passed'] = 'none';
            }

            $items[$i] = $item;
            break;
        }
    }

    if($item['top_passed'] == 'yes') $topPassedCount++;
    if($item['mgr_passed'] == 'yes') $mgrPassedCount++;
}

if($mgrPassedCount == $itemsCount){
    @=MgrTenderPassed = true;
}

if($topPassedCount == $itemsCount){
    @=TenderPassed = true;
}

$items = orderGrid($items, 'tender_passed', 'ASC');
@=IngoingItemsList = orderGrid($items, 'supplier', 'ASC');