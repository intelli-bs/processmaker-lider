<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 10:43 PM
 */
$caseId = @@APPLICATION;
$inDocDef = @@ContractUID;
$sql = "SELECT APP_DOC_UID FROM APP_DOCUMENT WHERE APP_UID='$caseId' AND
  DOC_UID='$inDocDef' AND APP_DOC_TYPE='INPUT' AND APP_DOC_STATUS='ACTIVE'
  AND DOC_VERSION = (SELECT MAX(DOC_VERSION) FROM APP_DOCUMENT WHERE APP_UID='$caseId' AND
  DOC_UID='$inDocDef' AND APP_DOC_TYPE='INPUT' AND APP_DOC_STATUS='ACTIVE') LIMIT 1";
$id = executeQuery($sql);
@@VA_LinkToContract='https://'.$_SERVER['SERVER_NAME'].'/syslider/en/neoclassic/cases/cases_ShowDocument?a='.$id[1]['APP_DOC_UID'];