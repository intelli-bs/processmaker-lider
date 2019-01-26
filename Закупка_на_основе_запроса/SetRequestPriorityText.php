<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:13 PM
 */
if(@%RequestPriority == 3)
{
    @@RequestPriorityText = 'Нормальный';
}
else if(@%RequestPriority == 4)
{
    @@RequestPriorityText = 'Высокий';
}
else if(@%RequestPriority == 5)
{
    @@RequestPriorityText =='Наивысший';
}