<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:26 PM
 */
$performer = @@USER_LOGGED;
$performerData = PMFInformationUser($performer);
@@PerformerName = $performerData['firstname'].' '.$performerData['lastname'];