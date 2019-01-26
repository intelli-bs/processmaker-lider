<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:25 PM
 */
@@created_at = date('Y-m-d h:i:s');
@%del_index = @%INDEX;
$task = PMFGetTaskName(@@TASK,'en');
if($task == 'Проверка необходимости заключения договора')
{
    if(@@ContractType == "no")
    {
        @@task_name = "Получение счета";
    }
    else
    {
        @@task_name = "Заключение Договора";
    }
}
else if($task == 'Получение счета')
{
    if(@@InvoiceCompliance == 'valid')
    {
        if(@#prepayment_amount <= 0)
		{
            if(@#payment_on_readiness_amount >0)
			{
                @@task_name = "Ожидание готовности";
            }
			else
            {
                @@task_name = "Ожидание поставки";
            }
        }else{
            @@task_name = "Проведение предоплаты";
        }
    }else if(@@InvoiceCompliance == 'invalid')
    {
        @@task_name = "Процесс прерван. Счет не соответсвует заказу";
        @@process_name = 'Закупка по заявке склада';
    }else if(@@InvoiceCompliance == 'correction')
    {
        @@task_name = "Коррекция данных";
    }

}else if($task == 'Ожидание готовности')
{
    if(@=OrderIsReady == true){
        @@task_name = "Проведение оплаты по готовности";
    }
}else if($task == 'Ожидание поставки')
{
    if(@=OrderShipped){
        @@task_name = "Проверка комплектации заказа";
        @@actual_shipment_date = date('Y-m-d h:i:s');
    }
}else if(strpos($task,'Проверка комплектации заказа') !== false)
{
    $stockEmpl = strpos($task,'руководитель') !== false
        && @=RequiredCheckByStockEmpl === true;
    if(!$stockEmpl)
    {
        $check = @=SupplyCorrect;
        $checkPay = @#payment_amount;
	  if(!$check){@@task_name = "Обработка проблемной поставки"; }
      else if($checkPay <= 0){@@task_name = "Завершение закупки";}
      else{@@task_name = "Проведение оплаты";}
	}
}else if($task == 'Обработка проблемной поставки')
{
    if(@#payment_amount <= 0)
	{
        @@task_name = "Завершение закупки";
    }else{
        @@task_name = "Проведение оплаты";
    }
}
else if($task == 'Коррекция данных')
{
    @@task_name = "Утверждение скорректированных данных";
}
else if($task == 'Утверждение скорректированных данных')
{
    if(@=DataCorrectionApproved == true)
    {
        @@task_name = "Получение счета";
    }
    else
    {
        @@task_name = "Процесс прерван. Счет не соответсвует заказу";
        @@process_name = 'Закупка по заявке склада';
    }
}