<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:29 PM
 */
if(@@PaymentType == 'Предоплата'){
    if(@#PaymentOnReadinessAmount > 0){
    @@task_name = 'Ожидание готовности';
}else{
    @@task_name = 'Ожидание поставки';
}
}else if(@@PaymentType == 'Оплата'){
    @@task_name = 'Завершение закупки';
}else{
    @@task_name = 'Ожидание поставки';
}