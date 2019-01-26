<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 10:43 PM
 */
$caseUID = @@APPLICATION; //Unique ID for the current case
$inDocDef = @@ContractUID; //change for the Input Document definition's unique ID
$aAttachFiles = array();

$inDocQuery = "SELECT AD.APP_DOC_UID, AD.DOC_VERSION, APP_DOC_FILENAME AS FILENAME FROM APP_DOCUMENT AD
   WHERE AD.APP_UID='$caseUID' AND AD.DOC_UID='$inDocDef' AND AD.APP_DOC_STATUS='ACTIVE' AND AD.APP_DOC_TYPE='INPUT' ORDER BY AD.DOC_VERSION DESC LIMIT 1";

$inDocs = executeQuery($inDocQuery);
$g = new G();
if (is_array($inDocs)) {
    foreach ($inDocs as $inDoc) {
        $aAttachFiles[$inDoc['FILENAME']] = PATH_DOCUMENT . $g->getPathFromUID($caseUID) . PATH_SEP .
            $inDoc['APP_DOC_UID'] . '_' . $inDoc['DOC_VERSION'] . '.' . pathinfo($inDoc['FILENAME'], PATHINFO_EXTENSION);
    }
}

PMFSendMessage(@@APPLICATION, 'lider.elektrik.cloud@gmail.com', @@VA_VendorMail, '', '','Договор', 'VA_VendorEmail.html', [], $aAttachFiles);
