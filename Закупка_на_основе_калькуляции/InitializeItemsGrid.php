<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:06 PM
 */
function checkHeaders($row)
{
    if(trim($row['B']) != '№'
        || trim($row['C']) != 'Код'
        || trim($row['D']) != 'Артикул'
        || trim($row['E']) != 'Полка'
        || trim($row['F']) != 'Наименование'
        || trim($row['G']) != 'Примечание'
        || trim($row['H']) != 'Ед. изм.'
        || trim($row['I']) != 'Кво'
        || trim($row['J']) != 'Цена'
        || trim($row['K']) != 'Заказать'
        || trim($row['L']) != 'Цена по счету'
        || trim($row['M']) != 'Кво по счету'
        || trim($row['N']) != 'Сумм по счету'
        || trim($row['O']) != 'Дата пост.'
        || trim($row['P']) != 'Кво дн.'
        || trim($row['Q']) != 'Ответственный'
        || trim($row['R']) != 'e-mail'
        || trim($row['S']) != 'Поставщик'
        || trim($row['T']) != 'Код поставщика'){
        return false;
    }
    return true;
}
function getFilePath($appUid, $appDocUid)
{
    $appDocument = new AppDocument();
    $result = $appDocument->load($appDocUid);
    $ext = pathinfo($result["APP_DOC_FILENAME"], PATHINFO_EXTENSION);

    $path = PATH_DOCUMENT
        . G::getPathFromUID($appUid)
        . PATH_SEP . $appDocUid
        . '_'
        . $result['DOC_VERSION']
        . '.'
        . $ext;
    return $path;
}

function getDataUsingPhpSpreadsheet($path)
{
    try{
        $spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($path);
    }catch(Exception $e){
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }


    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray(null, true, true, true);
    return $data;
}

function getData($appUid, $appDocUid)
{
    $path = getFilePath($appUid, $appDocUid);
    $data = getDataUsingPhpSpreadsheet($path);
    $itemsGrid = [];
    $performersItems = [];
    $performers = [];
    $codes = [];
    $suppliers = [];

    if(count($data) == 0){
        $error = '<div class="alert alert-danger" role="alert">Файл пустой. Загрузите файл с информацией</div>';
        return $error;
    }
    $i = 6;//empty rows before table
    foreach ($data as $key => $value) {
        if($key == $i){
            $check = checkHeaders($value);
            if(!$check){
                $error = '<div class="alert alert-danger" role="alert">Столбцы в файле Excel не соответсвуют стандарту. Загрузите файл в правильном формате</div>';
                return $error;
            }
        }elseif($key > $i){
            if(trim($value['B']) == null
                && trim($value['C']) == null
                && trim($value['F']) == null){
                break;
            }

            $aUser = ['firstname' => 'N/A', 'lastname' => 'N/A', 'username' => 'N/A'];
            if(!array_key_exists(trim($value['R']),$performers)){
                try{
                    $result = executeQuery("SELECT USR_UID FROM USERS WHERE USR_EMAIL='".trim($value['R'])."' AND USR_STATUS = 'ACTIVE'");
                    if (is_array($result) and count($result) > 0)
                    {
                        $performers[trim($value['R'])] = $result[1]['USR_UID'];
                    }
                }catch(Exception $e){
                    $error = '<div class="alert alert-danger" role="alert">Ошибка при попытке получить исполнителя из базы</div>';
                    return $error;
                }
            }
            /*if(in_array( $value['C'], $codes)){
                $error = '<div class="alert alert-danger" role="alert">Код наименования дублируется в файле. Исправьте ошибку и попробуйте снова</div>';
                        return $error;
            }*/
            if(!array_key_exists( $value['T'], $suppliers)){
                $suppliers[$value['T']] = $value['S'];
            }
            array_push($codes, $value['C']);
            $itemsGrid[$key-$i] = ['code' => $value['C'],
                'arthuikul' => $value['D'],
                'title' => $value['F'],
                'unit' => $value['H'],
                'quantity' => $value['K'],
                'price' => $value['J'],
                'sum' => $value['N'],
                'supplier' => $value['S'],
                'performer' => $performers[trim($value['R'])],
                'supplier_code'=> $value['T']];
        }
    }
    foreach ($suppliers as $key=>$value) {
        $supplier = addslashes(trim($value));
        $code = $key;
        $date = date('Y-m-d');
        $query = "INSERT INTO PMT_SUPPLIERS (NAME,CREATED_AT,CODE) VALUES ('$supplier','$date','$code')";
        $query_delete = "DELETE FROM PMT_SUPPLIERS WHERE CODE='$code'";
        $sqlSupplier = "SELECT * FROM PMT_SUPPLIERS WHERE CODE ='$code'";
        $Supplier = executeQuery($sqlSupplier);
        if(count($Supplier) < 1){
            if (executeQuery($query) == 0){
                $error = '<div class="alert alert-danger" role="alert">Ошибка при попытке записать поставщика в базу</div>';
                return $error;
            }
        }else{
            if (executeQuery($query_delete) == 0){
                $error = '<div class="alert alert-danger" role="alert">Ошибка при попытке обновить наименование поставщика в базе</div>';
                return $error;
            }
            if (executeQuery($query) == 0){
                $error = '<div class="alert alert-danger" role="alert">Ошибка при попытке обновить наименование поставщика в базе</div>';
                return $error;
            }
        }
    }

    return [$itemsGrid,$performersItems,count($performersItems)];
}


@@ParsingResult = true;
$appUid = @@APPLICATION;
$index = @@INDEX;
$clientsFile = @@CalculationFileMult[0]['appDocUid'];
$clientsFile = '["'.$clientsFile.'"]';
//set to UID of form where file is uploaded
$dynTitle = 'RequestCreation';
$dynUid = PMFGetUidFromText($dynTitle, 'DYN_TITLE', @@PROCESS, 'en');
//validations
$clientsFile = json_decode($clientsFile);

//Important to put the correct path in order to work properly
$file1 = '/opt/processmaker/shared/PhpSpreadsheet-develop/src/Bootstrap.php';
if (empty($clientsFile[0])) {
    @@ParsingResult = false;
    @@ParsingError =  '<div class="alert alert-danger" role="alert">Файл не выбран</div>';
    return true;
}

if (!file_exists($file1)) {
    @@ParsingResult = false;
    @@ParsingError = '<div class="alert alert-danger" role="alert">Не загрузилась библиотека PhpSpreadsheet. Попробуйте снова или обратитесь к Администратору</div>';
    return true;
}

//process
require_once $file1;

$items = getData($appUid, $clientsFile[0]);
if(is_array($items)){
    @=ItemsList = $items[0];
    @=PerformersList = $items[1];
    @%PerformersToProceedLeft =  $items[2];
    @@created_at = date('Y-m-d h:i:s');

}else{
    @@ParsingResult = false;
    @@ParsingError = $items;
    @@Error = "Ошибка при загрузке файла";
    return true;
}

