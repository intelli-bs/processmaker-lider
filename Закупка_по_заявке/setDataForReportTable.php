<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:00 PM
 */
@@created_at = date('Y-m-d h:i:s');
@%del_index = @%INDEX;
$task = PMFGetTaskName(@@TASK,'en');
if($task == 'Ознакомление с заявкой'){
    if(@@SearchForAnalogues == true){
        @@task_name = "Поиск новых ценовых предложений";
        @@process_name = "Поиск новых предложений";
    }else{
        @@task_name = "Подготовка тендерной документации";
    }
}else if($task == 'Тендерная проверка руководителем отдела'){
    if(!@=TenderPassed){
        @@task_name = "Ознакомление с результатми проверки";
    }else{
        if(@=TopMgrApproveRequired){
            @@task_name = "Тендерная проверка руководителем предприятия";
        }
    }
}else if($task == 'Ознакомление с результатми проверки'){
    @@task_name = "Поиск новых предложений";
}else if($task == 'Подготовка тендерной документации'){
    @@task_name = "Тендерная проверка руководителем отдела";
}else if($task == 'Тендерная проверка руководителем предприятия'){
    if(!@=TenderPassed){
        @@task_name = "Ознакомление с результатми проверки";
    }
}

