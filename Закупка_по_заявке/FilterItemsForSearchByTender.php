<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 10:55 PM
 */
$i = 1;
$items = @=IngoingItemsList;
$itemsForSearch = array();
foreach($items as $item)
{
    if($item['top_passed'] === 'no')
    {
        $itemsForSearch[$i] = $item;
        $i++;
    }
    else if($item['top_passed'] === 'none')
    {
        if($item['mgr_passed'] === 'no')
        {
            $itemsForSearch[$i] = $item;
            $i++;
        }
    }
}

@=ItemsForOffersSearch = $itemsForSearch;
