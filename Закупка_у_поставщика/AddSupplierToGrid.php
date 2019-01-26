<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:14 PM
 */
$items = @=ItemsList;
foreach($items as $item)
{
    $item['supplier'] = @@supplier_name;
}
@=ItemsList = $items;