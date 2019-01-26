<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:32 PM
 */
$items = @=AnaloguesList;
$result = false;
foreach($items as $item)
{
    if($item['tech_check_res'] === 'recheck')
    {
        $result = true;
        break;
    }
}
@=DataClarificationRequired = $result;