<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 10:54 PM
 */
@=TenderPassed = true;
foreach(@=IngoingItemsList as $item)
{
    if($item['mgr_passed'] !== 'yes')
    {
        @=TenderPassed = false;
    }
}
