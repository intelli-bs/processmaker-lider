<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:34 PM
 */
$items = @=AnaloguesList;
$count = 0;
foreach($items as $item)
{
    $offer = $item['offer_type'];
    $techCheck = $item['analogue_tech_check'];
    $techCheckRes = $item['tech_check_res'];
    $tenderNotNeeded = ($offer !== 'new' && $offer !== 'analogue')
        || ($techCheck === 'yes' && $techCheckRes !== 'yes');
    if(!$tenderNotNeeded)
    {
        $count++;
    }
}

@%OffersForTenderCount = $count;