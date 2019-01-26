<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 10:44 PM
 */
if (@@VA_Source == 'vendor') {
    @@VA_HeaderContent = "<h4>Внесение изменений после корректировок Поставщика</h4>";
}else{
    @@VA_HeaderContent = "<h4>Внесение изменений после правок юр. отдела</h4>";
}