<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:13 PM
 */
$performers = [];
$performersCount = 0;

foreach(@=ItemsList as $item)
{
    $performer = $item['performer'];
    $performerItems = [];
    if(!array_key_exists($performer,$performers))
    {
        $performersCount++;
        $nextIdx = 1;
    }
    else
    {
        $performerItems = $performers[$performer];
        $nextIdx = max(array_keys($performerItems)) + 1;
    }

    $performerItems[$nextIdx] = $item;
    $performers[$performer] = $performerItems;
}

@=PerformersList = $performers;
@%PerformersToProceedLeft = $performersCount;