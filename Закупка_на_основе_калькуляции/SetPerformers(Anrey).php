<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/29/2019
 * Time: 1:06 AM
 */
$performers = [];
$performersCount = 0;
$i = 0;
foreach(@=ItemsList as $item)
{
    $performer = $item['performer'];
    if(!in_array($performer,$performers))
    {
        array_push($performers,$performer);
        $i++;
    }
}

@=PerformersList = $performers;
@%PerformersToProceedLeft = $i;