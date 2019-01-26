<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:26 PM
 */
$manager = @@USER_LOGGED;
$managerData = PMFInformationUser($manager);
@@ManagerName = $managerData['firstname'].' '.$managerData['lastname'];