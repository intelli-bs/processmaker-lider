<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 10:53 PM
 */
$suppliers = [];
$purchases = [];

$sqlPurchases = "SELECT * FROM PMT_PURCHASE_SUPPLIER_MONITORING ORDER BY APP_STATUS DESC";
$sqlSuppliers = "SELECT * FROM PMT_SUPPLIERS";
$sqlcountActive = "SELECT COUNT(*) AS QTY,SUM(INVOICE_AMOUNT) AS SUM, SUPPLIER_NAME,SUPPLIER_CODE FROM PMT_PURCHASE_SUPPLIER_MONITORING WHERE APP_STATUS = 'TO_DO' GROUP BY SUPPLIER_CODE ORDER BY QTY DESC";
$sqlcountComplete = "SELECT COUNT(*) AS QTY,SUM(INVOICE_AMOUNT) AS SUM, SUPPLIER_NAME,SUPPLIER_CODE FROM PMT_PURCHASE_SUPPLIER_MONITORING WHERE NOT APP_STATUS = 'TO_DO' GROUP BY SUPPLIER_CODE";
try{
    $purchases = executeQuery($sqlPurchases);
    $suppliers = executeQuery($sqlSuppliers);
    $countActive =  executeQuery($sqlcountActive);
    $countComplete = executeQuery($sqlcountComplete);
}catch (Exception $e){
    $g = new G();
    $g->SendMessageText("Execution of queries ".$sqlPurchases.",".$sqlSuppliers." failed!","ERROR");
    return;
}

$suppliers_qty = count($suppliers);
$completed = 0;
$status = '';
$suppliers_sorted = [];
$html = "<div class='panel-group' id='accordion'>";
if ($suppliers_qty == 0){
    $html = "<h4>Отсутствуют сведения о поставщиках в базе данных<h4/>";
}else{
    try{
        foreach($countActive as $Active){
            $j = 0;
            foreach($suppliers as $supplier)
            {
                if($Active['SUPPLIER_CODE'] == $supplier['CODE']){
                    $suppliers_sorted[$supplier['ID']]['active'] = $Active['QTY'];
                    $suppliers_sorted[$supplier['ID']]['supplier'] = $supplier;
                    $suppliers_sorted[$supplier['ID']]['active_sum'] = $Active['SUM'];
                    array_splice($suppliers, $j, 1);
                }
                $j++;
            }
        }

        @@sorted = $suppliers_sorted;

        $i = 0;
        foreach($suppliers_sorted as $key=>$value)
        {
            $activeRequests = 0;
            $activeSum = 0;
            $completeRequests = 0;
            $completeSum = 0;

            foreach($countComplete as $Complete){
                if($Complete['SUPPLIER_CODE'] == $value['supplier']['CODE']){
                    $completeRequests = $Complete['QTY'];
                    $completeSum = $Complete['SUM'];
                }
            }

            $html .= "<div id='code".$value['supplier']['CODE']."' class='panel panel-primary' data-supplier='".$value['supplier']['NAME']."'>
				<div class='panel-heading'>
					<h4 class='panel-title'>
						<a data-toggle='collapse' data-parent='#accordion' href='#collapse".$i."'>
							<span class='glyphicon glyphicon-plus'></span>". $value['supplier']['NAME']."
                        </a>
						<span class='pull-right glyphicon glyphicon-tag'>Активные закупки: ".$value['active']."(Сумма: ".$value['active_sum']." грн)&nbsp</span>
						<span class='pull-right glyphicon glyphicon-tag'>Завершенные закупки: ".$completeRequests."(Сумма: ".$completeSum." грн)</span>
					</h4>
				</div>
				<div id='collapse".$i."' class='panel-collapse collapse'>
					<div class='panel-body' style='background-color:#ededed;'>
						<table class='table table-bordered table-condensed' style='background-color:#fff;'>
							<thead><tr>
									  <th scope='col'>№ заявки (№ процесса)</th>
									  <th scope='col'>Текущий этап</th>
									  <th scope='col'>Отгрузка</th>
									  <th scope='col'>Оплаты</th>
									  <th scope='col'>Статус</th>
									</tr>
	  </thead><tbody>";

            foreach($purchases as $purchase){
                if($purchase['SUPPLIER_CODE'] == $value['supplier']['CODE'] ){
                    switch ($purchase['APP_STATUS']) {
                        case 'TO_DO':
                            $status = 'В процессе';
                            break;
                        case 'PAUSED':
                            $status = 'Приостановлено';
                            break;
                        case 'DRAFT':
                            $status = 'Черновик';
                            break;
                        case 'CANCELLED':
                            $status = 'Отменено';
                            break;
                        case 'COMPLETED':
                            $status = 'Выполнено';
                            break;
                        case 'DELETED':
                            $status = 'Удалено';
                            break;
                    }
                    $html .= " <tr>
									  <th scope='row'>".$purchase['REQUESTID']." 
									  (".$purchase['APP_NUMBER'].")
									  </th>
									  <td>".$purchase['TASK_NAME']."</td>
									   <td>
										<dl>
											<dt>План</dt>
											<dd>- <small>".str_replace("00:00:00", "", $purchase['EXPECTED_SHIPMENT_DATE'])."</small></dd>
											<dt>Факт</dt>
											<dd>- <small>".str_replace("00:00:00", "", $purchase['ACTUAL_SHIPMENT_DATE'])."</small></dd>
										  </dl>  
									   </td>
									   <td>
											<table class='table table-bordered table-condensed'>
														  <tr>
														    <th rowspan = '2'>Вид оплаты</th>
															<th colspan = '2'>Дата</th>
															<th rowspan = '2'><div>Сумма счёта</div>
															<div>".$purchase['INVOICE_AMOUNT']."грн.</div>
															</th>
														  </tr>
														   <tr>
															<th>План</th>
															<th>Факт</th> 
														  </tr>
														  <tr>
														    <td>Предоплата</td>
															<td><small style='font-size: 70%;'>".str_replace("00:00:00", "", $purchase['PREPAYMENT_DATE'])."</small></td>
															<td><small style='font-size: 70%;'>".str_replace("00:00:00", "", $purchase['PREPAYMENT_ACTUAL_DATE'])."</small></td> 
															<td><small style='font-size: 70%;'>".$purchase['PREPAYMENT_AMOUNT']."</small></td>
														  </tr>
														  <tr>
														    <td>Оплата по готовности</td>
															<td><small style='font-size: 70%;'>".str_replace("00:00:00", "", $purchase['PAYMENT_ON_READINESS_DATE'])."</small></td>
															<td><small style='font-size: 70%;'>".str_replace("00:00:00", "", $purchase['PAYMENT_ON_READINESS_ACTUAL_DATE'])."</small></td> 
															<td><small style='font-size: 70%;'>".$purchase['PAYMENT_ON_READINESS_AMOUNT']."</small></td>
														  </tr>
														  <tr>
														    <td>Оплата</td>
															<td><small style='font-size: 70%;'>".str_replace("00:00:00", "", $purchase['PAYMENT_DATE'])."</small></td>
															<td><small style='font-size: 70%;'>".str_replace("00:00:00", "", $purchase['PAYMENT_ACTUAL_DATE'])."</small></td> 
															<td><small style='font-size: 70%;'>".$purchase['PAYMENT_AMOUNT']."</small></td>
														  </tr>
													</table></td></td>
									   <td>".$status."</td>
								   </tr>";
                }
            }
            $html .= "</tbody></table>";
            $html .= "</div></div></div>";
            $i++;
        }

        foreach($suppliers as $supplier)
        {
            $activeRequests = 0;
            $activeSum = 0;
            $completeRequests = 0;
            $completeSum = 0;
            foreach($countActive as $Active){
                if($Active['SUPPLIER_CODE'] == $supplier['CODE']){
                    $activeRequests = $Active['QTY'];
                    $activeSum = $Active['SUM'];
                }
            }
            foreach($countComplete as $Complete){
                if($Complete['SUPPLIER_CODE'] == $supplier['CODE']){
                    $completeRequests = $Complete['QTY'];
                    $completeSum = $Complete['SUM'];
                }
            }
            $html .= "<div class='panel panel-primary' id='code".$supplier['CODE']."' data-supplier='".$supplier['NAME']."'>
				<div class='panel-heading'>
					<h4 class='panel-title'>
						<a data-toggle='collapse' data-parent='#accordion' href='#collapse".$i."'>
							<span class='glyphicon glyphicon-plus'></span>". $supplier['NAME']."</a>
						<span class='pull-right glyphicon glyphicon-tag'>Активные закупки: ".$activeRequests."(Сумма: ".$activeSum." грн)&nbsp</span>
						<span class='pull-right glyphicon glyphicon-tag'>Завершенные закупки: ".$completeRequests."(Сумма: ".$completeSum." грн)</span>
					</h4>
				</div>
				<div id='collapse".$i."' class='panel-collapse collapse'>
					<div class='panel-body' style='background-color:#ededed;'>
						<table class='table table-bordered table-condensed' style='background-color:#fff;'>
							<thead><tr>
									  <th scope='col'>№ заявки (№ процесса)</th>
									  <th scope='col'>Текущий этап</th>
									  <th scope='col'>Отгрузка</th>
									  <th scope='col'>Оплаты</th>
									  <th scope='col'>Статус</th>
									</tr>
	  </thead><tbody>";

            foreach($purchases as $purchase){
                if($purchase['SUPPLIER_CODE'] == $supplier['CODE'] ){
                    switch ($purchase['APP_STATUS']) {
                        case 'TO_DO':
                            $status = 'В процессе';
                            break;
                        case 'PAUSED':
                            $status = 'Приостановлено';
                            break;
                        case 'DRAFT':
                            $status = 'Черновик';
                            break;
                        case 'CANCELLED':
                            $status = 'Отменено';
                            break;
                        case 'COMPLETED':
                            $status = 'Выполнено';
                            break;
                        case 'DELETED':
                            $status = 'Удалено';
                            break;
                    }
                    $html .= " <tr>
									  <th scope='row'>".$purchase['REQUESTID']." 
									  (".$purchase['APP_NUMBER'].")
									  </th>
									  <td>".$purchase['TASK_NAME']."</td>
									   <td>
										<dl>
											<dt>План</dt>
											<dd>- <small>".str_replace("00:00:00", "", $purchase['EXPECTED_SHIPMENT_DATE'])."</small></dd>
											<dt>Факт</dt>
											<dd>- <small>".str_replace("00:00:00", "", $purchase['ACTUAL_SHIPMENT_DATE'])."</small></dd>
										  </dl>  
									   </td>
									   <td>
											<table class='table table-bordered table-condensed'>
														  <tr>
														    <th rowspan = '2'>Вид оплаты</th>
															<th colspan = '2'>Дата</th>
															<th rowspan = '2'><div>Сумма счёта</div>
															<div>".$purchase['INVOICE_AMOUNT']."грн.</div>
															</th>
														  </tr>
														   <tr>
															<th>План</th>
															<th>Факт</th> 
														  </tr>
														  <tr>
														    <td>Предоплата</td>
															<td><small style='font-size: 70%;'>".str_replace("00:00:00", "", $purchase['PREPAYMENT_DATE'])."</small></td>
															<td><small style='font-size: 70%;'>".str_replace("00:00:00", "", $purchase['PREPAYMENT_ACTUAL_DATE'])."</small></td> 
															<td><small style='font-size: 70%;'>".$purchase['PREPAYMENT_AMOUNT']."</small></td>
														  </tr>
														  <tr>
														    <td>Оплата по готовности</td>
															<td><small style='font-size: 70%;'>".str_replace("00:00:00", "", $purchase['PAYMENT_ON_READINESS_DATE'])."</small></td>
															<td><small style='font-size: 70%;'>".str_replace("00:00:00", "", $purchase['PAYMENT_ON_READINESS_ACTUAL_DATE'])."</small></td> 
															<td><small style='font-size: 70%;'>".$purchase['PAYMENT_ON_READINESS_AMOUNT']."</small></td>
														  </tr>
														  <tr>
														    <td>Оплата</td>
															<td><small style='font-size: 70%;'>".str_replace("00:00:00", "", $purchase['PAYMENT_DATE'])."</small></td>
															<td><small style='font-size: 70%;'>".str_replace("00:00:00", "", $purchase['PAYMENT_ACTUAL_DATE'])."</small></td> 
															<td><small style='font-size: 70%;'>".$purchase['PAYMENT_AMOUNT']."</small></td>
														  </tr>
													</table></td></td>
									   <td>".$status."</td>
								   </tr>";
                }
            }
            $html .= "</tbody></table>";
            $html .= "</div></div></div>";
            $i++;
        }
        $html .= "</div>";
    }catch (Exception $e){
        $g = new G();
        $g->SendMessageText($e->getMessage(),"ERROR");
        return;
    }

}

@@html = $html;