<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:31 PM
 */
$items = @=AnaloguesList;
$result = true;
foreach($items as $item)
{
    if($item['tech_check_res'] === 'none'
        || $item['tech_check_res'] === 'recheck')
    {
        $result = false;
        break;
    }
}

@=AllItemsCheckedByMrg = $result;

if(@@RequiredCheckByTechEmpl == 'false')
{
    @=RequiredCheckByTechEmpl = false;
}
if(@@RequiredCheckByTechEmpl == 'true')
{
    @=RequiredCheckByTechEmpl = true;
}

