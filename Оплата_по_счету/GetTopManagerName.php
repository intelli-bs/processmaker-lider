<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:27 PM
 */
$topManager = @@USER_LOGGED;
$topManagerData = PMFInformationUser($topManager);
@@TopManagerName = $topManagerData['firstname'].' '.$topManagerData['lastname'];