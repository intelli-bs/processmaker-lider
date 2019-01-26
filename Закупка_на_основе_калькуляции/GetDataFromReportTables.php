<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:05 PM
 */
//get token
@@ProcessComplete = 'inprogress';
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


$purchases = [];
$suppliers = [];
$search = [];
$sqlPurchase = "SELECT * FROM PMT_PURCHASE_USER_MONITORING
WHERE PARENT_PROCESS_ID = '".@@APPLICATION."'";

$sqlSuppliers = "SELECT * FROM PMT_PURCHASE_SUPPLIER_MONITORING
WHERE MAIN_PARENT_PROCESS_ID = '".@@APPLICATION."'";

$sqlSearch = "SELECT * FROM PMT_PURCHASE_USER_SEARCH_MONITORING
WHERE MAIN_PARENT_PROCESS_ID = '".@@APPLICATION."' AND APP_STATUS = 'TO_DO'";

try{
    $purchases = executeQuery($sqlPurchase);
    $suppliers = executeQuery($sqlSuppliers);
    $search = executeQuery($sqlSearch);
}catch (Exception $e){
    $g = new G();
    $g->SendMessageText("Execution of queries ".$sqlPurchase." or ".$sqlSuppliers." failed!","ERROR");
    return;
}

$purchases_qty = count($purchases);
$completed = 0;

if ($purchases_qty == 0){
    $g = new G();
    $g->SendMessageText("Отсутствуют сведенья о процессе в базе данных","ERROR");
    return true;
}else{
    $i = 0;
    $status = '';
    foreach($purchases as $purchase)
    {
        $items_list = [];

        $class = 'panel-info';
        $corrected = false;
        $query_check_corrected = "SELECT ITEMS_LIST_CORRECTED FROM PMT_PURCHASE_SUPPLIER_MONITORING WHERE PERFORMERID = '".$purchase['PERFORMERID']."' AND ITEMS_LIST_CORRECTED=1 AND MAIN_PARENT_PROCESS_ID = '".@@APPLICATION."' LIMIT 1";
        $check_corrected = executeQuery($query_check_corrected);
        if(count($check_corrected) == 1){
            $corrected = true;
        }
        if($corrected){
            $varName = 'ItemsList';
            $number = 0;
            foreach($suppliers as $supplier){
                $url = "/api/1.0/lider/case/".$supplier['APP_UID']."/".$supplier['DEL_INDEX']."/variable/".$varName;
                if(count($items_list) == 0){
                    $items_list = get_object_vars(pmRestRequest("GET", $url, null, $accessToken, $pmServer));
                }else{
                    //both arrays will be merged including duplicates
                    $items_list = array_merge( $items_list, get_object_vars(pmRestRequest("GET", $url, null, $accessToken, $pmServer)) );
//duplicate objects will be removed
                    $items_list = array_map("unserialize", array_unique(array_map("serialize", $items_list)));
                }
            }
            $count = count($items_list) - 1;
        }else{

            $number = 1;
            $varName = 'IngoingItemsList';
            $url = "/api/1.0/lider/case/".$purchase['APP_UID']."/".$purchase['DEL_INDEX']."/variable/".$varName;
            $items_list = get_object_vars(pmRestRequest("GET", $url, null, $accessToken, $pmServer));
            $count = count($items_list);
        }

        $task = $purchase['TASK_NAME'];
        $created_at = $purchase['CREATED_AT'];
        switch ($purchase['APP_STATUS']) {
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
                $created_at = '-';
                break;
            case 'DELETED':
                $class = 'panel-warning';
                $status = 'Удалено';
                $completed++;
                break;
        }
        if($purchase['TASK_NAME'] == 'Процесс прерван. Счет не соответсвует заказу'){
            $status = 'Прервано';
            $task = $purchase['TASK_NAME'];
            $class = 'panel-warning';
        }
        $html .= "<div class='panel ".$class."'>
				<div class='panel-heading '>
					<h4 class='panel-title'>
						<div style='font-weight:bold;'>";
        if($purchase['PROCESS_NAME'] == 'Поиск новых предложений' && (is_array($search) && count($search) >0)){
            foreach($search as $item){
                if($item['PARENT_PROCESS_ID'] == $purchase['APP_UID']){
                    $task = $item['TASK_NAME'];
                    $created_at = $item['CREATED_AT'];
                    $html .= " <div class='row '>
									  <div class='col-sm-2 col-md-2 col-lg-2'><a data-toggle='collapse' data-parent='#accordion' href='#collapse".$i."'>
							<span class='glyphicon glyphicon-plus'></span>".$purchase['PERFORMER_NAME']."</div>
									  <div class='col-sm-2 col-md-2 col-lg-2'>Процесс: ".$purchase['PROCESS_NAME']."</div>
									  <div class='col-sm-4 col-md-4 col-lg-4'>Текущая задача: ".$task."</div>
									  <div class='col-sm-2 col-md-2 col-lg-2'>Время начала:  ".substr($created_at, 0, -3)."</div>
									   <div class='col-sm-2 col-md-3 col-lg-2'>".$status."</div>
								   </div>";
                }
            }

        }else{
            $html .= "<a data-toggle='collapse' data-parent='#accordion' href='#collapse".$i."'><div class='row '>
									  <div class='col-sm-2 col-md-2 col-lg-2'>
							<span class='glyphicon glyphicon-plus'></span>".$purchase['PERFORMER_NAME']."</div>
									  <div class='col-sm-2 col-md-2 col-lg-2'>Процесс: ".$purchase['PROCESS_NAME']."</div>
									  <div class='col-sm-4 col-md-4 col-lg-4'>Текущая задача: ".$task."</div>
									  <div class='col-sm-2 col-md-2 col-lg-2'>Время начала: ".substr($created_at, 0, -3)."</div>
									   <div class='col-sm-2 col-md-2 col-lg-2'>".$status."</div>
								   </div>";

        }
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
									  <th scope='col'>Поставщик</th>
									</tr>
	  </thead><tbody>";
        for ($j = $number; $j <= $count; $j++){
            $html .= "<tr><td>".$items_list[$j]->code."</td>
			<td>".$items_list[$j]->title."</td>
			<td>".$items_list[$j]->unit."</td>
			<td>".$items_list[$j]->quantity."</td>
			<td>".$items_list[$j]->price."</td>
			<td>".$items_list[$j]->supplier."</td>";
        }

        $html .= "</tr></tbody></table></div></div>
			</div>";
        $i++;
    }
    $html .= "</div>";

}
if($completed == $purchases_qty){
    @@ProcessComplete = 'complete';
}

if (count($suppliers) !== 0){
    $html .= "<h4 class ='center-block' >Мониторинг закупки по поставщикам</h4>";
    foreach($suppliers as $supplier){
        $class = 'panel-info';
        $varName = 'ItemsList';
        $url = "/api/1.0/lider/case/".$supplier['APP_UID']."/".$supplier['DEL_INDEX']."/variable/".$varName;
        $items_list = get_object_vars(pmRestRequest("GET", $url, null, $accessToken, $pmServer));
        $status = '';
        $task = $supplier['TASK_NAME'];
        $created_at = $supplier['CREATED_AT'];
        $time = "Время начала: ".substr($created_at, 0, -3);
        switch ($supplier['APP_STATUS']) {
            case 'TO_DO':
                $status  = 'В процессе';
                break;
            case 'PAUSED':
                $class = 'panel-warning';
                $status  = 'Приостановлено';
                break;
            case 'DRAFT':
                $class = 'panel-warning';
                $status  = 'Черновик';
                break;
            case 'CANCELLED':
                $class = 'panel-warning';
                $status   = 'Отменено';
                break;
            case 'COMPLETED':
                $class = 'panel-success';
                $status  = 'Выполнено';
                $created_at = substr($supplier['ACTUAL_SHIPMENT_DATE'], 0, -3);
                $task = '-';
                $time = "Время отгрузки: ".substr($created_at, 0, -3);
                break;
            case 'DELETED':
                $class = 'panel-warning';
                $status  = 'Удалено';
                break;
        }

        if($supplier['TASK_NAME'] == 'Процесс прерван. Счет не соответсвует заказу'){
            $status = 'Прервано';
            $task = $supplier['TASK_NAME'];
            $class = 'panel-warning';
            $time = "Время начала: ".substr($created_at, 0, -3);
        }

        $html .= "<div class='panel-group' id='accordion1'>
						<div class='panel ".$class."'>
							<div class='panel-heading'>
								<h4 class='panel-title'>
									<a data-toggle='collapse' data-parent='#accordion1' href='#collapse".$i."'>
										<div class='row'>";


        $html .= "<div class='col-sm-3 col-md-3 col-lg-3'><span class='glyphicon glyphicon-plus'></span>".$supplier['SUPPLIER_NAME']."</div>
									  <div class='col-sm-2 col-md-2 col-lg-2'>".$supplier['PERFORMER_NAME']."</div>
									  <div class='col-sm-3 col-md-3 col-lg-3'>Текущая задача:  ".$task."</div>
									  <div class='col-sm-2 col-md-2 col-lg-2'>".$time."</div>
									   <div class='col-sm-2 col-md-2 col-lg-2'>".$status."</div>";
        $html .= "
				</div> 
			  </a>
			</h4>
		</div>
				<div id='collapse".$i."' class='panel-collapse collapse'>
					<div class='panel-body' style='background-color:#ededed;'>
						<table class='table table-bordered table-condensed' style='background-color:#fff;'>
							<thead><tr>
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

        $html .= "</table></div></div>
			</div>";

        $i++;
    }
    $html .= "</div>";
}
@@html = $html;