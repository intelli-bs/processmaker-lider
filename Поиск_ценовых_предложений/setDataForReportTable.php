<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:39 PM
 */
@@created_at = date('Y-m-d h:i:s');
$task = PMFGetTaskName(@@TASK,'en');
@@process_name = 'Поиск ценовых предложений';
if($task == 'Поиск новых ценовых предложений'){
    if(@%AnalogueOffersCount  > 0){
        if(@%OffersForTechCheckCount > 0){
            @@task_name = 'Проверка технических параметров';
        }else{
            @@task_name = 'Тендерная проверка руководителем отдела';
        }
    }else if(@%AnalogueOffersCount  == 0){
        @@process_name = 'Закупка по заявке склада';
    }
}else if($task == 'Проверка технических параметров'){
    if(@=DataClarificationRequired == true){
        @@task_name = 'Уточнение технических параметров';
    }else{
        if(@%OffersForTenderCount <= 0){
            @@task_name = 'Итог поиска новых наименований/поставщиков, запись в 1С';
        }else{
            @@task_name = 'Тендерная проверка руководителем отдела';
        }
    }
}else if($task == 'Тендерная проверка руководителем отдела'){
    if(@=TopMgrApproveRequired == true){
        @@task_name = 'Тендерная проверка руководителем компании';
    }else{
        @@task_name = 'Итог поиска новых наименований/поставщиков, запись в 1С';
    }
}else if($task == 'Тендерная проверка руководителем компании'){
    @@task_name = 'Итог поиска новых наименований/поставщиков, запись в 1С';
}else if($task == 'Итог поиска новых  наименований и/или поставщиков, запись в 1С'){
    @@process_name = 'Закупка по заявке склада';
    @@task_name = 'Подготовка тендерной документации';
}else if($task == 'Уточнение технических параметров'){
    @@task_name = 'Проверка технических параметров';
}

if(!empty(@@ProcessTitleComment))
{
    @@process_name = @@process_name.' '.@@ProcessTitleComment;
}

