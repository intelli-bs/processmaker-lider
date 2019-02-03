<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:30 PM
 */
$purchases_user = [];
$suppliers = [];
$purchases = [];
$search = [];
$sqlPurchases = "SELECT * FROM PMT_PURCHASE_MONITORING WHERE APP_STATUS = 'TO_DO'";
$sqlPurchasesUser = "SELECT * FROM PMT_PURCHASE_USER_MONITORING WHERE PARENT_PROCESS_ID IN (SELECT APP_UID FROM PMT_PURCHASE_MONITORING WHERE APP_STATUS = 'TO_DO')";
$sqlSuppliers = "SELECT * FROM PMT_PURCHASE_SUPPLIER_MONITORING
WHERE MAIN_PARENT_PROCESS_ID  IN  (SELECT APP_UID FROM PMT_PURCHASE_MONITORING WHERE APP_STATUS = 'TO_DO')";
$sqlSearch = "SELECT * FROM PMT_PURCHASE_USER_SEARCH_MONITORING
WHERE APP_STATUS = 'TO_DO'";

try{
    $purchases = executeQuery($sqlPurchases);
    $purchases_user = executeQuery($sqlPurchasesUser);
    $suppliers = executeQuery($sqlSuppliers);
    $search = executeQuery($sqlSearch);
}catch (Exception $e){
    $g = new G();
    $g->SendMessageText("Execution of queries ".$sqlPurchases.",".$sqlPurchasesUser.",".$sqlSuppliers." failed!","ERROR");
    return;
}

$purchases_qty = count($purchases);
$completed = 0;
$status = '';
$color = '';
$html = "<div class='panel-group' id='accordion'>";
if ($purchases_qty == 0){
    $html = "<h4>Отсутствуют сведения о заявках в базе данных<h4/>";
}else{
    try{
        $i = 0;
        foreach($purchases as $purchase)
        {
            $html .= "<div class='panel panel-primary'>
				<div class='panel-heading'>
					<h4 class='panel-title'>
						<a data-toggle='collapse' data-parent='#accordion' href='#collapse".$i."'>
							<span class='glyphicon glyphicon-plus'></span>
							<span style='font-weight:bold;'>№ заказа: ". $purchase['REQUESTID']."</span> ( № Процесса: ".$purchase['APP_NUMBER'].")
						</a>
						<span class='pull-right'>".$purchase['CREATED_AT']."</span>
					</h4>
				</div>
				<div id='collapse".$i."' class='panel-collapse collapse'>
					<div class='panel-body' style='background-color:#ededed;'>
						<table class='table table-bordered table-condensed' style='background-color:#fff;'>
							<thead><tr>
									  <th scope='col'>Исполнитель</th>
									  <th scope='col'>Текущий процесс</th>
									  <th scope='col'>Текущий этап</th>
									  <th scope='col'>Начало</th>
									  <th scope='col'>Статус</th>
									</tr>
	                        </thead><tbody>";
            foreach($purchases_user as $purchase_user){
                if($purchase_user['PARENT_PROCESS_ID'] == $purchase['APP_UID'] ){
                    switch ($purchase_user['APP_STATUS']) {
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
                    if($purchase_user['PROCESS_NAME'] == 'Поиск новых предложений' && (is_array($search) && count($search) >0)){
                        foreach($search as $item){
                            if($item['PARENT_PROCESS_ID'] == $purchase_user['APP_UID']){
                                $html .= " 
 									<tr>
									  <th scope='row'>".$purchase_user['PERFORMER_NAME']."</th>
									  <td>".$purchase_user['PROCESS_NAME']."</td>
									  <td>".$item['TASK_NAME']."</td>
									  <td>".$item['CREATED_AT']."</td>
									   <td>".$status."</td>
								   </tr>";
                            }
                        }
                    }else{
                        $html .= " 
 									<tr>
									  <th scope='row'>".$purchase_user['PERFORMER_NAME']."</th>
									  <td>".$purchase_user['PROCESS_NAME']."</td>
									  <td>".$purchase_user['TASK_NAME']."</td>
									  <td>".$purchase_user['CREATED_AT']."</td>
									   <td>".$status."</td>
								   </tr>";
                    }
                }
            }
            $html .= "</tbody></table>";

            if(count($suppliers)> 0){
                $html .= "<table class='table table-bordered table-condensed' style='background-color:#fff;'>
										<caption>Мониторинг работы по поставщикам</caption>
										<thead><tr>
												  <th scope='col'>Поставщик (Исполнитель)</th>
												  <th scope='col'>Текущий этап</th>
												  <th scope='col'>Начало</th>
												  <th scope='col'>Поставка</th>
												  <th scope='col'>Оплаты</th>
												</tr>
										</thead><tbody>";
                foreach($suppliers as $supplier){
                    if($supplier['MAIN_PARENT_PROCESS_ID'] == $purchase['APP_UID'] ){
                        switch ($supplier['APP_STATUS']) {
                            case 'TO_DO':
                                $color = '#fcf2f2';
                                $status = 'В процессе';
                                break;
                            case 'PAUSED':
                                $color = '#ffffc0';
                                $status = 'Приостановлено';
                                break;
                            case 'DRAFT':
                                $color = '##ffffc0';
                                $status = 'Черновик';
                                break;
                            case 'CANCELLED':
                                $color = '#ffffc0';
                                $status = 'Отменено';
                                break;
                            case 'COMPLETED':
                                $color = '#dff0d8';
                                $status = 'Выполнено';
                                break;
                            case 'DELETED':
                                $color = '##ffffc0';
                                $status = 'Удалено';
                                break;
                        }
                        $html .= " <tr>
							<th colspan = '5' style='background-color:".$color."'>".$status."</th>
						</tr>
						    <tr>
										  <th scope='row'><div>".$supplier['SUPPLIER_NAME']."</div>
														  <div>(".$supplier['PERFORMER_NAME'].")</div>
										  </th>
										  <td>".$supplier['TASK_NAME']."</td>
										  <td><small>".$supplier['CREATED_AT']."</small></td>
										   <td>
											<dl>
												<dt>План</dt>
												<dd>- <small>".str_replace("00:00:00", "", $supplier['EXPECTED_SHIPMENT_DATE'])."</small></dd>
												<dt>Факт</dt>
												<dd>- <small>".str_replace("00:00:00", "", $supplier['ACTUAL_SHIPMENT_DATE'])."</small></dd>
											  </dl>  
										   </td>
										   <td>
												<table class='table table-bordered table-condensed'>
														  <tr>
														    <th rowspan = '2'>Вид оплаты</th>
															<th colspan = '2'>Дата</th>
															<th rowspan = '2'><div>Сумма счёта</div>
															<div>".$supplier['INVOICE_AMOUNT']."</div>
															</th>
														  </tr>
														   <tr>
															<th>План</th>
															<th>Факт</th> 
														  </tr>
														  <tr>
														    <td>Предоплата</td>
															<td><small style='font-size: 70%;'>".str_replace("00:00:00", "", $supplier['PREPAYMENT_DATE'])."</small></td>
															<td><small style='font-size: 70%;'>".str_replace("00:00:00", "", $supplier['PREPAYMENT_ACTUAL_DATE'])."</small></td> 
															<td><small style='font-size: 70%;'>".$supplier['PREPAYMENT_AMOUNT']."</small></td>
														  </tr>
														  <tr>
														    <td>Оплата по готовности</td>
															<td><small style='font-size: 70%;'>".str_replace("00:00:00", "", $supplier['PAYMENT_ON_READINESS_DATE'])."</small></td>
															<td><small style='font-size: 70%;'>".str_replace("00:00:00", "", $supplier['PAYMENT_ON_READINESS_ACTUAL_DATE'])."</small></td> 
															<td><small style='font-size: 70%;'>".$supplier['PAYMENT_ON_READINESS_AMOUNT']."</small></td>
														  </tr>
														  <tr>
														    <td>Оплата</td>
															<td><small style='font-size: 70%;'>".str_replace("00:00:00", "", $supplier['PAYMENT_DATE'])."</small></td>
															<td><small style='font-size: 70%;'>".str_replace("00:00:00", "", $supplier['PAYMENT_ACTUAL_DATE'])."</small></td> 
															<td><small style='font-size: 70%;'>".$supplier['PAYMENT_AMOUNT']."</small></td>
														  </tr>
													</table></td>
									   </tr>";
                    }
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
