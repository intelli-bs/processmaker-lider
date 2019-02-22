$('.btn-uploadfile').html('Загрузить файл');
$('.title-column').css('white-space','pre-line');

const normalHover = '#ededed';
const normal = '#fff';
const id_idx = 1;
const code_idx = 2;
const title_idx = 3;
const unit_idx = 4;
const quantity_idx = 5;
const price_idx = 6;
const supplier_selector_idx = 7;
const performer_idx = 7;
const supplier_code_idx = 9;
const current_process_task_idx = 10;
const source_var_url_idx = 11;
const purchaseByRequestProcessName = "Закупка по заявке";
const purchaseFromSupplierProcessName = 'Закупка у поставщика';
const offersSearchProcessName = 'Поиск ценовых предложений';
const purchaseFromSupplierGridName = 'ItemsList';
const offersSearchGridName = 'AnaloguesList';
const purchaseByRequestGridName = 'IngoingItemsList';

let gridId = 'items_for_correction';
let formId = '1037506985c59af930f0236099497788';
let rowsNum = $("#"+gridId).getNumberRows();
let rows  = $("#"+gridId).find("div.pmdynaform-grid-row");
let tempGrid = [];
let changes = [];
rows.mouseover(mouseoverHandler);
rows.mouseout(mouseoutHandler);

function mouseoverHandler() 
{ 
  checkInput();
  $(this).css("background-color", normalHover); 
}

function mouseoutHandler() 
{
  checkInput();
  $(this).css("background-color", '');
}
function checkInput()
{
  rowsNum = $("#"+gridId).getNumberRows();
  for (var i=1; i <= rowsNum; i++)
  {
    let supplier =  $("#"+gridId).getText(i,supplier_selector_idx);
    if(supplier !== null && supplier.trim() !== '')
    {
      let supplier_code =  $("#"+gridId).getValue(i,supplier_selector_idx);
      if(supplier !== supplier_code)
      {
        $("#"+gridId).setValue(supplier_code,i,supplier_code_idx);
      }
      else
      {
        $("#"+gridId).setValue('',i,supplier_code_idx);
      }
    }
  }
}

$(`#${gridId}`).onAddRow(function(aNewRow, oGrid, newIndex) { 
    rowsNum = $(`#${gridId}`).getNumberRows();
    rows  = $(`#${gridId}`).find("div.pmdynaform-grid-row");
    rows.mouseover(mouseoverHandler);
    rows.mouseout(mouseoutHandler);
    let temp = {'id_idx' : $(`#${gridId}`).getNumberRows()+1, 'action' : 'add'};
  	if(changes['new'] != undefined){
		let item = changes['new'];
		item.push(temp);
		changes['new'] = item;
	}else{
		changes['new'] = [temp];
	}
});


$(`#${gridId}`).onDeleteRow(function(aNewRow, oGrid, newIndex) { 
    rowsNum = $(`#${gridId}`).getNumberRows();
    rows  = $(`#${gridId}`).find("div.pmdynaform-grid-row");
    rows.mouseover(mouseoverHandler);
    rows.mouseout(mouseoutHandler);
  	let temp = {'id_idx': oGrid[0][0].getValue(), 'action' : 'delete'};
	if(changes[oGrid[0][10].getValue()] != undefined){
		let item = changes[oGrid[0][10].getValue()];
		item.push(temp);
		changes[oGrid[0][10].getValue()] = item;
	}else{
        changes[oGrid[0][10].getValue()] = [temp];
	}
});

/*
 * Back to top button
 */

$("#back-to-top").css( "display" , "none");
$("#back-to-top").css( "position" , "fixed");
$("#back-to-top").css( "bottom" , "42px");
$("#back-to-top").css( "right" , "25px");
$("#back-to-top").css( "text-align" , "center");
$("#back-to-top").css( "height" , "32px");
$("#back-to-top").css( "width" , "32px");
$("#back-to-top").css( "cursor" , "pointer");
$("#back-to-top").css( "border" , "0");
$("#back-to-top").css( "border-radius" , "2px");
$("#back-to-top").css( "text-decoration" , "none");
$("#back-to-top").css( "padding-left" , "9px");
$("#back-to-top").click(function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: 0
            }, 100);
       });
$(window).scroll(function () {
            if ($(this).scrollTop() > 500) {
                $('#back-to-top').fadeIn();
            } else {
                $('#back-to-top').fadeOut();
            }
        });


$($("#"+gridId).find("div.pmdynaform-grid-row")).hover(function(){
     $(this).css("background-color", normalHover);
     $($("#"+gridId).find("div.pmdynaform-grid-row:eq("+($("div.pmdynaform-grid-row").index(this))+") .form-control")).css("background-color", normalHover);
  }, function(){
     $(this).css("background-color", normal);
      $($("#"+gridId).find("div.pmdynaform-grid-row:eq("+($("div.pmdynaform-grid-row").index(this))+") .form-control")).css("background-color", normal);

});

const  user = "system";
const  password = "password";
const  workspace = 'lider';
const  server = 'https://test.intellibs.net/'
const  projectsRoute = '/project';  
const  method = "GET";
const paramsSuffix = '';
getActualItemsList();
getFormById(formId).setOnSubmit(updateVariablesInProcesses);
function updateVariablesInProcesses(){
	let newGrid = [];	
	let id = 0;
  	let temp = [];
	newGrid = $(`#${gridId}`).getValue();
	//find added and updated
  	let k = 1;
	newGrid.forEach(function(element){
		tempGrid.forEach(function(element1) {
		if(element[id] == element1[id]){
			if(element[1] !== element1[1] ||
              element[2] !== element1[2] ||
              element[3] !== element1[3] ||
              element[4] !== element1[4] ||
              element[5] !== element1[5]){
              temp = {'id_idx' : element[id], 'action' : 'update'};
              $(`#${gridId}`).setValue("",k, id)
              if(changes[element[10]] != undefined){
				let item = changes[element[10]];
                item.push(temp);
				changes[element[10]] = item;
			  }else{
				changes[element[10]] = [temp];
			  }
            }
          return;
		}
	});
      k++;
	});
  
  let grid = [];
  let update_url = '';
  Object.keys(changes).forEach(function(key) {
  let value = changes[key];
  let array = key.split(['/']);
  value.forEach(function(element){
	if(element.action === 'delete' || element.action === 'update'){
      	getData('GET',server, workspace, user, password, key, '', getItemsForUpdate);
      	for(let j = 1; j <= Object.keys(grid).length; j++)
        {
            var item = grid[j];
            if(item.id === element.id_idx){
              delete grid[j];
            }
        }
      let new_grid = [];
      let idx = 1;
      Object.keys(grid).forEach(function(key) {
        new_grid[idx.toString()] = grid[key];
        idx++;
      })
      
      console.log(new_grid);
	  update_url = `/variable/${array[2]}/${array[3]}/variable/${array[5]}`;
	  updateData('PUT',server, workspace, user, password, update_url, new_grid);
   }
   })
 
});
  function getItemsForUpdate(data)
  {
    grid =  data;
  }
  function updateData(method, server, workspace, user, password, route, params)
{
  accessToken = getToken(server,workspace,user,password); 
  apiSuffix = 'api/1.0/';
  url = server + apiSuffix + workspace + route + paramsSuffix;
  callbackError = function (data){
          console.error(data);
  }
  console.log(params);
  $.ajax({
      url: url,
      type: method,
      async: false, // true
      dataType: "json",
      data: params,
      beforeSend: function (xhr) {
          xhr.setRequestHeader("Authorization", "Bearer " + accessToken);
      },
      contentType: "application/json",
      success: function (data, textStatus) {
          console.log(data);
      },
      error: function (xhr, textStatus, errorThrown) {
          callbackError(textStatus);
      }
  });
}
	return false;
}


function getToken(server, workspace, user, password)
{
    const clientID = "PJCOFVBCBOXUCJKFOHLVVXXYNBXFVDNS";
    const clientSecret = "1000357045c35472661d665017368577";
	const suffix = '/oauth2/token'
	let url = server + workspace + suffix;
	let accessToken = '';
	$.ajax({
      url: url,
      type: "POST",
      data: JSON.stringify({
          "grant_type": "password",
                  "scope": "",
                  "client_id": clientID,
                  "client_secret":clientSecret,
                  "username": user,
                  "password": password
      }),
      async: false, //with false => sync ; true => async
      contentType: "application/json",
      success: function (data, textStatus) {
         accessToken = data.access_token;
      },
      error: function (xhr, textStatus, errorThrown) {
          console.log(textStatus);
      }
    });
	
	return accessToken;
}

function getActualItemsList()
{
  let purchaseByRequestProjectUid = '';
  getData('GET',server, workspace, user, password, projectsRoute, '', getPurchaseByRequestProjectUid);  
  
  let purchaseUserMonitoringTableUid  = '';
  tablesRoute = '/project/'+purchaseByRequestProjectUid+'/report-tables';
  getData('GET',server, workspace, user, password, tablesRoute, '', getPurchaseUserMonitoringTableUid);
	// resolving cases from 'Закупка по заявке'
  let purchaseUserMonitoringTableData = [];
  let purchaseUserMonitoringTableRoute = `/project/${purchaseByRequestProjectUid}/report-table/${ purchaseUserMonitoringTableUid}/data`;
  getData('GET',server, workspace, user, password, purchaseUserMonitoringTableRoute, '', getPurchaseUserMonitoringTableData);
  if(purchaseUserMonitoringTableData.length == 0)
  {//means that the case is still in 'Закупка на основе калькуляции' process 
    return;
  }  
  
  let replacedIds = [];
  //checking if each 'Закупка по заявке' process is already on 'Закупка у поставщика' stage, if yes resolving data from there
  let purchaseFromSupplierProjectUid = '';
  getData('GET',server, workspace, user, password, projectsRoute, paramsSuffix, getPurchaseFromSupplierProjectUid);
  
  let purchaseSupplierMonitoringTableUid  = '';
  tablesRoute = '/project/'+purchaseFromSupplierProjectUid+'/report-tables';
  getData('GET',server, workspace, user, password, tablesRoute, paramsSuffix, getPurchaseSupplierMonitoringTableUid);  
  
  let purchaseSupplierMonitoringTableData = [];
  let purchaseSuplierMonitoringTableRoute = '/project/'+purchaseFromSupplierProjectUid+'/report-table/'+ purchaseSupplierMonitoringTableUid +'/data';
  getData('GET',server, workspace, user, password, purchaseSuplierMonitoringTableRoute, '', getPurchaseSupplierMonitoringTableData);
  let itemsPerSupplier = [];  
  for(var i = 0; i < purchaseUserMonitoringTableData.length; i++)
  {
    let currentPurchaseProcessId = purchaseUserMonitoringTableData[i].app_uid;
    for(var j = 0; j < purchaseSupplierMonitoringTableData.length; j++)
    {
      if(purchaseSupplierMonitoringTableData[j].parent_process_id == currentPurchaseProcessId)
      {
        let processRecord = purchaseSupplierMonitoringTableData[j];
        let itemsPerSupplierUrl = '/case/' + processRecord.app_uid + '/' + processRecord.del_index + '/variable/' + purchaseFromSupplierGridName;
        getData('GET',server, workspace, user, password, itemsPerSupplierUrl, '', getItemsPerSupplier);
        rewriteItems(itemsPerSupplier,purchaseFromSupplierProcessName, processRecord.task_name, itemsPerSupplierUrl);
        itemsPerSupplier = [];
      }
    }  
  }
  
  //checking if each 'Закупка по заявке' process is on 'Поиск ценовых предложений' stage, if yes resolving data from there  
  let offersSearchProjectUid = '';
  getData('GET',server, workspace, user, password, projectsRoute, paramsSuffix, getOffersSearchProjectUid);
  let offersSearchTableUid  = '';
  tablesRoute = '/project/'+offersSearchProjectUid+'/report-tables';
  getData('GET',server, workspace, user, password, tablesRoute, paramsSuffix, getOffersSearchTableUid);
  let purchaseUserSearchTableData = [];
  let purchaseUserSearchTableRoute = '/project/'+offersSearchProjectUid+'/report-table/'+ offersSearchTableUid +'/data';
  getData('GET',server, workspace, user, password, purchaseUserSearchTableRoute, '', getPurchaseUserSearchTableData);
  let itemsInSearch = [];   
  for(var i = 0; i < purchaseUserMonitoringTableData.length; i++)
  {
    let currentPurchaseProcessId = purchaseUserMonitoringTableData[i].app_uid;
    for(var j = 0; j < purchaseUserSearchTableData.length; j++)
    {
      if(purchaseUserSearchTableData[j].parent_process_id == currentPurchaseProcessId)
      {
        let processRecord = purchaseUserSearchTableData[j];
        let itemsInSearchUrl = '/case/' + processRecord.app_uid + '/' + processRecord.del_index + '/variable/' + offersSearchGridName;
        getData('GET',server, workspace, user, password, itemsInSearchUrl, '', getItemsInSearch);
        rewriteItems(itemsInSearch,offersSearchProcessName, processRecord.task_name, itemsInSearchUrl);
        itemsInSearch = [];
      }
    }  
  }
  
  //resolving items from 'Закупка по заявке' process, which were not resolved previously  
  let itemsInPurchase = [];   
  for(var i = 0; i < purchaseUserMonitoringTableData.length; i++)
  {
    let record = purchaseUserMonitoringTableData[i];
    let itemsInPurchaseUrl = '/case/' + record.app_uid + '/' + record.del_index + '/variable/' + purchaseByRequestGridName;
    getData('GET',server, workspace, user, password, itemsInPurchaseUrl, '', getItemsInPurchase);
    rewriteItems(itemsInPurchase,purchaseByRequestProcessName, record.task_name, itemsInPurchaseUrl);
    itemsInPurchase = []; 
  }
	tempGrid = $('#'+gridId).getValue();
	console.log(tempGrid);
  function rewriteItems(items, actualProcess, actualTask, sourceVarUrl)
  {
    for (let i=1; i <= rowsNum; i++)
    {
      let idInGrid = $('#'+gridId).getValue(i, id_idx);
      for(let j = 1; j <= Object.keys(items).length; j++)
      {
        var item = items[j];
        if(!replacedIds.includes(item.id) && item.id == idInGrid)
        {
          replacedIds.push(item.id);
          $('#'+gridId).setValue(item.title,i, title_idx);
          $('#'+gridId).setValue(item.code,i, code_idx);
          $('#'+gridId).setValue(item.price,i, price_idx);
          $('#'+gridId).setValue(item.supplier,i, supplier_selector_idx);
          $('#'+gridId).setValue(item.supplier_code,i, supplier_code_idx);
          $('#'+gridId).setValue(actualProcess + ' .' + actualTask,i, current_process_task_idx);
          $('#'+gridId).setValue(sourceVarUrl,i, source_var_url_idx);
          if(checkIfRowImmutable(actualProcess, actualTask))
          {
            $("#"+gridId).getControl(i,title_idx).attr("readOnly", true);
            $("#"+gridId).getControl(i,code_idx).attr("readOnly", true);
            $("#"+gridId).getControl(i,price_idx).attr("readOnly", true);
            $("#"+gridId).getControl(i,supplier_selector_idx).attr("readOnly", true);
            $("#"+gridId).getControl(i,supplier_code_idx).attr("readOnly", true);
          }
        }
      }
    }
  }
  
  function checkIfRowImmutable(actualProcess, actualTask)
  {
    if(actualProcess == purchaseFromSupplierProcessName
      && !(actualTask == 'Проверка необходимости заключения договора' 
          || actualTask == 'Получение счета'))
    {
      return true;
    }
    return false;
  }
  //callbacks region 
  function getItemsInPurchase(data)
  {
    itemsInPurchase = data;
  }
  
  function getItemsPerSupplier(data)
  {
    itemsPerSupplier = data;
    //console.log(JSON.stringify(itemsPerSupplier));//так надо обїект дебажить
  }
  
  
  function getItemsInSearch(data)
  {
    itemsInSearch = data;
  }
  
  function getPurchaseByRequestProjectUid(data)
  {
    for (var i = 0; i < data.length; i++)
    {
        if (data[i].prj_name == purchaseByRequestProcessName) 
        {
          purchaseByRequestProjectUid = data[i].prj_uid;
          break;
        }
    }
  }
  
  function getPurchaseFromSupplierProjectUid(data)
  {
    for (var i = 0; i < data.length; i++)
    {
        if (data[i].prj_name == purchaseFromSupplierProcessName) 
        {
          purchaseFromSupplierProjectUid = data[i].prj_uid;
          break;
        }
    }
  }
  
   function getOffersSearchProjectUid(data)
  {
    for (var i = 0; i < data.length; i++)
    {
        if (data[i].prj_name == offersSearchProcessName) 
        {
          offersSearchProjectUid = data[i].prj_uid;
          break;
        }
    }
  }
  
  function getPurchaseUserMonitoringTableUid(data)
  {
    for (var i = 0; i < data.length; i++)
    {
        if (data[i].rep_tab_name == "PMT_PURCHASE_USER_MONITORING") 
        {
          purchaseUserMonitoringTableUid = data[i].rep_uid;
          break;
        }
    }   
  }

  function getPurchaseSupplierMonitoringTableUid(data)
  {
    for (var i = 0; i < data.length; i++)
    {
        if (data[i].rep_tab_name == "PMT_PURCHASE_SUPPLIER_MONITORING") 
        {
          purchaseSupplierMonitoringTableUid = data[i].rep_uid;
          break;
        }
    }   
  }
  
  function getOffersSearchTableUid(data)
  {
    for (var i = 0; i < data.length; i++)
    {
        if (data[i].rep_tab_name == "PMT_PURCHASE_USER_SEARCH_MONITORING") 
        {
          offersSearchTableUid = data[i].rep_uid;
          break;
        }
    }   
  }


  function getPurchaseUserMonitoringTableData(data)
  {
    let parentCaseUid = $('#PurchaseForCorrection').getValue();
    for (var i = 0; i < data.rows.length; i++)
    {
        if (data.rows[i].parent_process_id == parentCaseUid
           && data.rows[i].app_status == 'TO_DO') 
        {
          purchaseUserMonitoringTableData.push(data.rows[i]);
        }
    }   
  }
  
  function getPurchaseSupplierMonitoringTableData(data)
  {
    for (var i = 0; i < data.rows.length; i++)
    {
      if(data.rows[i].app_status == 'TO_DO')
      {
        purchaseSupplierMonitoringTableData.push(data.rows[i]);
      }        
    } 
  }
  
  function getPurchaseUserSearchTableData(data)
  {
    for (var i = 0; i < data.rows.length; i++)
    {
      if(data.rows[i].app_status == 'TO_DO')
      {
        purchaseUserSearchTableData.push(data.rows[i]);
      }        
    } 
  }
}


function getData(method, server, workspace, user, password, route, paramsSuffix, callbackProcessor)
{
  accessToken = getToken(server,workspace,user,password); 
  apiSuffix = 'api/1.0/';
  url = server + apiSuffix + workspace + route + paramsSuffix;

  callbackSuccess = callbackProcessor;
  callbackError = function (data){
          console.error(data);
  }

  $.ajax({
      url: url,
      type: method,
      async: false, // true
      beforeSend: function (xhr) {
          xhr.setRequestHeader("Authorization", "Bearer " + accessToken);
      },
      contentType: "application/json",
      success: function (data, textStatus) {
          callbackSuccess(data);
      },
      error: function (xhr, textStatus, errorThrown) {
          callbackError(textStatus);
      }
  });
}


