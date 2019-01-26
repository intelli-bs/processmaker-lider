<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:09 PM
 */
$performerData = @=PerformersList;
$currentItems = array();
$i = 1;
foreach($performerData as $key => $value){
    @@PerformerId = $key;
    $currentItems =  $value;
    break;
}

array_shift($performerData);
$userUID = @@PerformerId;
$aUser = PMFInformationUser($userUID);
@@CurrentPerformerName  = $aUser['firstname'].' '.$aUser['lastname'];
@=PerformersList = $performerData;
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
