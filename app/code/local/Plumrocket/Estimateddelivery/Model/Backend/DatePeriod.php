<?php

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.

@package    Plumrocket_Estimated_Delivery_Date-v1.1.x
@copyright  Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
@license    http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/

class Plumrocket_Estimateddelivery_Model_Backend_DatePeriod extends Mage_Core_Model_Config_Data
{

    protected function _afterLoad()
    {
        if($value = json_decode($this->getValue(), true)) {
            $this->setValue($value);
        }
		parent::_afterLoad();
    }
 
    protected function _beforeSave()
    {
        if($value = $this->getValue()) {
            if(is_array($value)) {
                array_walk_recursive($value, array('self', '_convertDate'));
            }
        }
        $this->setValue(json_encode($value));
        parent::_beforeSave();
    }

    protected function _convertDate(&$val, $key)
    {
        if(is_string($val) && strlen($val) == strlen(Mage::helper('estimateddelivery')->getDateTimeFormat()) && false !== strpos($val, '-')) {
            list($month, $day, $year) = explode('-', trim($val), 3);
            if(checkdate($month, $day, $year)) {
                $val = strtotime("$year-$month-$day");
            }
        }
    }

}