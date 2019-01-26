<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:28 PM
 */
$aAttachFiles = array();
$caseUID = @@APPLICATION;
$inDocDef = "7185418275bb25726d05f22033581485";
$inDocQuery = "SELECT AD.APP_DOC_UID, AD.DOC_VERSION, C.CON_VALUE AS FILENAME FROM APP_DOCUMENT AD, CONTENT C
   WHERE AD.APP_UID='$caseUID' AND AD.DOC_UID='$inDocDef' AND AD.APP_DOC_STATUS='ACTIVE' AND
   AD.APP_DOC_UID = C.CON_ID AND C.CON_CATEGORY = 'APP_DOC_FILENAME'";

$inDocs = executeQuery($inDocQuery);
$g = new G();
if (is_array($inDocs))
{
    foreach ($inDocs as $inDoc)
    {
        $aAttachFiles[$inDoc['FILENAME']] = PATH_DOCUMENT . $g->getPathFromUID($caseUID) . 			PATH_SEP . $inDoc['APP_DOC_UID'] . '_' . $inDoc['DOC_VERSION'] . '.' . 			pathinfo($inDoc['FILENAME'], PATHINFO_EXTENSION);
    }
}

PMFSendMessage(@@APPLICATION, 'lider.elektrik.cloud@gmail.com', @@AccountantEmail, '', '','оплата по счету', 'RequestForPayment.html', array(), $aAttachFiles);