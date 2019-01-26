<?php
/**
 * Created by PhpStorm.
 * User: Ksenia
 * Date: 1/26/2019
 * Time: 11:04 PM
 */
@=IngoingItemsList = orderGrid(@=IngoingItemsList, 'tender_passed', 'DESC');
@=IngoingItemsList = orderGrid(@=IngoingItemsList, 'supplier', 'DESC');