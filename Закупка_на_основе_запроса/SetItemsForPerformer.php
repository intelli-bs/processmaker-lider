<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:12 PM
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
@@created_at_next = date('Y-m-d h:i:s');
@@process_name = 'Закупка по заявке склада';