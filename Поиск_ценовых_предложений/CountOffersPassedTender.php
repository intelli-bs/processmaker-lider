<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:36 PM
 */
$items = @=AnaloguesList;
$count = 0;
foreach($items as $item)
{
    if($item['tender_passed'] === 'yes')
    {
        $count++;
    }
}

@%OffersPassedTender = $count;