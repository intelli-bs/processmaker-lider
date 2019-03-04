<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/29/2019
 * Time: 1:08 AM
 */
$performersList = @=PerformersList;
$currentItems = array();
$performer = $performersList[@%PerformersToProceedLeft-1];
$i = 1;
foreach(@=ItemsList as $item)
{
    if($item['performer'] === $performer)
    {
        $currentItems[$i] = $item;
        $i++;
    }
}
@@PerformerId = $performer;
$userUID = @@PerformerId;
$aUser = PMFInformationUser($userUID);
@@CurrentPerformerName  = $aUser['firstname'].' '.$aUser['lastname'];
@=CurrentItemsList = $currentItems;
@%PerformersToProceedLeft--;
//for subprocess
@@NextTask = "Ознакомление с заявкой";
@%NextDelIndex = 1;
@@created_at_next = date('Y-m-d h:i:s');
@@process_name = 'Закупка по заявке';
if(@=RequestPriority == 3){
    @@RequestPriorityText = 'Нормальный';
}else if(@=RequestPriority == 4){
    @@RequestPriorityText = 'Высокий';
}else if(@=RequestPriority == 5){
    @@RequestPriorityText = 'Наивысший';
}