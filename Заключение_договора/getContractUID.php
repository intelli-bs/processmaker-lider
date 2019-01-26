<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 10:45 PM
 */
$doc = 'VA_DI_Agreement';
$aTasks = PMFGetUidFromText($doc, 'INP_DOC_TITLE', @@PROCESS, 'en');
if (count($aTasks) == 0) {
    throw new Exception("Error: Task '$doc' is not found.");
}
@@ContractUID = $aTasks[0];