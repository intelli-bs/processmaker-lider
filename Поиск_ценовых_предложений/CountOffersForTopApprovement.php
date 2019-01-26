<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:35 PM
 */
$items = @=AnaloguesList;
$count = 0;
foreach($items as $item)
{
    if($item['mgr_tender_res'] === 'passed')
    {
        $count++;
    }
}

if($count == 0)
{
    @=TopMgrApproveRequired = false;
}


@%OffersForTopMgrApprovement = $count;

$manager = @@USER_LOGGED;
$managerData = PMFInformationUser($manager);
@@PurchasingDepartmentManager = $managerData['firstname'].' '.$managerData['lastname'];

@=ApprovedByPDMgr = getCurrentDate();
