<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:17 PM
 */
$aAttachFiles = array();
$caseUID = @@APPLICATION;

//get doc uid
$doc = 'Invoice';
$aTasks = PMFGetUidFromText($doc, 'INP_DOC_TITLE', @@PROCESS, 'en');
if (count($aTasks) == 0) {
    throw new Exception("Error: Task '$doc' is not found.");
}
$inDocDef = $aTasks[0];
$inDocQuery = "SELECT AD.APP_DOC_UID, AD.DOC_VERSION, C.CON_VALUE AS FILENAME FROM APP_DOCUMENT AD, CONTENT C
   WHERE AD.APP_UID='$caseUID' AND AD.DOC_UID='$inDocDef' AND AD.APP_DOC_STATUS='ACTIVE' AND
   AD.APP_DOC_UID = C.CON_ID AND C.CON_CATEGORY = 'APP_DOC_FILENAME'";

$inDocs = executeQuery($inDocQuery);
$g = new G();
if (is_array($inDocs) && count($inDocs) > 0)
{
    foreach ($inDocs as $inDoc)
    {
        $aAttachFiles[$inDoc['FILENAME']] = PATH_DOCUMENT . $g->getPathFromUID($caseUID) . 			PATH_SEP . $inDoc['APP_DOC_UID'] . '_' . $inDoc['DOC_VERSION'] . '.' . 			pathinfo($inDoc['FILENAME'], PATHINFO_EXTENSION);
    }
}

if(isset(@@ContractAppId) && @@ContractAppId != "")
{
    $contractCaseUID = @@ContractAppId;
    $contractDocDef = @@ContractUID;
    $contractDocQuery = "SELECT AD.APP_DOC_UID, AD.DOC_VERSION, C.CON_VALUE AS FILENAME FROM APP_DOCUMENT AD, CONTENT C
   WHERE AD.APP_UID='$contractCaseUID' AND AD.DOC_UID='$contractDocDef' AND AD.APP_DOC_STATUS='ACTIVE' AND
   AD.APP_DOC_UID = C.CON_ID AND C.CON_CATEGORY = 'APP_DOC_FILENAME'";

    $contractDoc = executeQuery($contractDocQuery);
    $g = new G();
    if (is_array($contractDoc) && count($contractDoc) > 0)
    {
        foreach ($contractDoc as $inDoc)
        {
            $aAttachFiles[$inDoc['FILENAME']] = PATH_DOCUMENT . $g->getPathFromUID($contractCaseUID) . 			PATH_SEP . $inDoc['APP_DOC_UID'] . '_' . $inDoc['DOC_VERSION'] . '.' . 			pathinfo($inDoc['FILENAME'], PATHINFO_EXTENSION);
        }
    }
}

PMFSendMessage(@@APPLICATION, 'lider.elektrik.cloud@gmail.com', 'lider.elektrik.cloud+cto@gmail.com', '', '','Отчет об обработке проблемной поставки', 'ReportProblematicSupplySolution.html', array(), $aAttachFiles);

PMFSendMessage(@@APPLICATION, 'lider.elektrik.cloud@gmail.com', 'lider.elektrik.cloud+ceo@gmail.com', '', '','Отчет об обработке проблемной поставки', 'ReportProblematicSupplySolution.html', array(), $aAttachFiles);