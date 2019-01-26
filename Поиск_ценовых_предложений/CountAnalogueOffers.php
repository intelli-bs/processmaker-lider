<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:32 PM
 */
$items = @=AnaloguesList;
$count = 0;
$techCount = 0;
$itemsCount = count($items);
for($i = 1; $i <= $itemsCount; $i++)
{
    $item = $items[$i];
    $offer = $item['offer_type'];
    if(!empty($offer) && $offer !== '' && $offer !== 'none')
    {
        $count++;
    }
    $techCheck = $item['analogue_tech_check'];
    if($techCheck === 'yes')
    {
        $techCount++;
    }
}

@%AnalogueOffersCount = $count;
@%OffersForTechCheckCount = $techCount;
if($techCount == 0)
{
    @%OffersForTenderCount = $count;
}

