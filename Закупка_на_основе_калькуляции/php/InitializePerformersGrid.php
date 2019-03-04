<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:06 PM
 */
$performers = array();
$performersList = array();
$i = 1;
foreach(@=ItemsList as $item)
{
    $performer = $item['performer_id'];
    if(!in_array($performer, $performers))
    {
        array_push($performers,$performer);
        $performersList[$i] = array(
            'performer_id' => $performer,
            'performer_email' => $item['performer_email'],
            'performer_name' => $item['performer_name']);
        $i++;
    }
}

@=PerformersList = $performersList;
@%PerformersToProceedLeft = count($performersList);