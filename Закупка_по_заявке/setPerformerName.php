<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:01 PM
 */
$userUID = @@PerformerId;
$aUser = PMFInformationUser($userUID);
@@performer_name = $aUser['firstname'].' '.$aUser['lastname'];