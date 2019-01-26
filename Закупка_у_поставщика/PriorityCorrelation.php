<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:15 PM
 */
$caseId = @@APPLICATION;
$sql = "SELECT APP_UID FROM SUB_APPLICATION WHERE APP_PARENT='$caseId'";
$result = executeQuery($sql);

$subcaseId = $result[1]['APP_UID'];
$sql2 = "UPDATE APP_DELEGATION SET DEL_PRIORITY=".@%RequestPriority." WHERE APP_UID='$subcaseId' AND DEL_INDEX=1";
executeQuery($sql2);