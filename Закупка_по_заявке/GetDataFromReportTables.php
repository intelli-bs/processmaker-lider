<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 10:56 PM
 */
$userId = @@USER_LOGGED;
$query = "SELECT ACCESS_TOKEN FROM OAUTH_ACCESS_TOKENS WHERE USER_ID='".$userId."' ORDER BY EXPIRES DESC";
try{
    $result = executeQuery($query);
    $accessToken = $result[1]['ACCESS_TOKEN'];

}catch (Exception $e){
    $g = new G();
    $g->SendMessageText("Execution of query ".$query." failed!","ERROR");
    return;
}
//now call a REST endpoint using the access token
$pmServer    = 'https://'.$_SERVER['SERVER_NAME'];//name of domaine
$pmWorkspace = 'lider';
$html = "<div class='panel-group' id='accordion'>";
//request to api
function pmRestRequest($method, $url, $param = null, $token, $pmServer){
    $ch = curl_init($pmServer . $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $token));
    //curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    if($method == "POST"){
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = json_decode(curl_exec($ch));
    return $result;

    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($statusCode != 200) {
        if (isset ($result) and isset($result->error))
            print "Error code: {$result->error->code}\nMessage: {$result->error->message}\n";
        else
            print "Error: HTTP status code: $statusCode\n";
    }else {
        return $result->response;
    }

}

$sqlSuppliers = "SELECT * FROM PMT_PURCHASE_SUPPLIER_MONITORING
WHERE PARENT_PROCESS_ID = '" . @@APPLICATION . "' AND PERFORMERID = '".@@PerformerId."'";

try
{
    $suppliers = executeQuery($sqlSuppliers);
}catch (Exception $e)
{
    $g = new G();
    $g->SendMessageText("Execution of query ".$sqlSuppliers."failed!","ERROR");
    return;
}
$suppliers_qty = count($suppliers);
$completed = 0;
if ($suppliers_qty == 0)
{
    $g = new G();
    $g->SendMessageText("Отсутствуют сведенья о процессе в базе данных","ERROR");
    return true;
}else{
    @@SupplyProcessComplete = 'inprogress';
    $i = 1;
    foreach($suppliers as $supplier)
    {
        $class = 'panel-info';
        $varName = 'ItemsList';
        $url = "/api/1.0/lider/case/".$supplier['APP_UID']."/".$supplier['DEL_INDEX']."/variable/".$varName;

        $items_list = get_object_vars(pmRestRequest("GET", $url, null, $accessToken, $pmServer));
        $task = $supplier['TASK_NAME'];
        $created_at = $supplier['CREATED_AT'];
        $time = "Время начала: ".substr($created_at, 0, -3);
        switch ($supplier['APP_STATUS']) {
            case 'TO_DO':
                $status = 'В процессе';
                break;
            case 'PAUSED':
                $class = 'panel-warning';
                $status = 'Приостановлено';
                break;
            case 'DRAFT':
                $class = 'panel-warning';
                $status = 'Черновик';
                break;
            case 'CANCELLED':
                $class = 'panel-warning';
                $status = 'Отменено';
                $completed++;
                break;
            case 'COMPLETED':
                $class = 'panel-success';
                $completed++;
                $status = 'Выполнено';
                $task = '-';
                $time = 'Время отгрузки: '.substr($supplier['ACTUAL_SHIPMENT_DATE'], 0, -3);
                break;
            case 'DELETED':
                $class = 'panel-warning';
                $completed++;
                $status = 'Удалено';
                break;
        }
        if($supplier['TASK_NAME'] == 'Процесс прерван. Счет не соответсвует заказу'){
            $status = 'Прервано';
            $task = $supplier['TASK_NAME'];
            $class = 'panel-warning';
            $time = "Время начала: ".substr($created_at, 0, -3);
        }
        $html .= "<div class='panel ".$class."'>
				<div class='panel-heading '>
					<h4 class='panel-title'>
						<div style='font-weight:bold;'>";

        $html .= "<a data-toggle='collapse' data-parent='#accordion' href='#collapse".$i."'><div class='row '>
									  <div class='col-sm-3 col-md-3 col-lg-3'>
							<span class='glyphicon glyphicon-plus'></span>".$supplier['SUPPLIER_NAME']."</div>
									  <div class='col-sm-4 col-md-4 col-lg-4'>Текущая задача: ".$task."</div>
									  <div class='col-sm-2 col-md-2 col-lg-2'>".$time."</div>
									   <div class='col-sm-2 col-md-2 col-lg-2'>".$status."</div>
								   </div>";

        $html .= "</thead>
				</table>
				</div> 
			  </a>
			</h4>
		</div>
				<div id='collapse".$i."' class='panel-collapse collapse'>
					<div class='panel-body' style='background-color:#ededed;'>
						<table class='table table-bordered table-condensed' style='background-color:#fff;'><thead><tr>
									  <th scope='col'>Код</th>
									  <th scope='col'>Наименование</th>
									  <th scope='col'>Ед.изм.</th>
									  <th scope='col'>Кол-во</th>
									  <th scope='col'>Цена</th>
									</tr>
	  </thead><tbody>";
        for ($j = 1; $j <= count($items_list); $j++){
            $html .= "<tr><td>".$items_list[$j]->code."</td>
			<td>".$items_list[$j]->title."</td>
			<td>".$items_list[$j]->unit."</td>
			<td>".$items_list[$j]->quantity."</td>
			<td>".$items_list[$j]->price."</td>";
        }

        $html .= "</tr></tbody></table></div>
			</div></div>";

        $i++;
    }
    $html .= "</div>";


}
if($completed === $suppliers_qty){
    @@SupplyProcessComplete = 'complete';
}
@@html = $html;