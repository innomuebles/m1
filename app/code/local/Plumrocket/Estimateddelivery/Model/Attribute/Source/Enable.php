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


class Plumrocket_Estimateddelivery_Model_Attribute_Source_Enable extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('estimateddelivery')->__('Inherited'),
                    'value' => Plumrocket_Estimateddelivery_Model_ProductCategory::INHERITED
                ),
				array(
                    'label' => Mage::helper('estimateddelivery')->__('Disabled (do not show)'),
                    'value' => Plumrocket_Estimateddelivery_Model_ProductCategory::DISABLED
                ),
				array(
                    'label' => Mage::helper('estimateddelivery')->__("Dynamic Date (\"n\" days from today's date)"),
                    'value' => Plumrocket_Estimateddelivery_Model_ProductCategory::DYNAMIC_DATE
                ),
                array(
                    'label' => Mage::helper('estimateddelivery')->__("Dynamic Date Range (\"n - m\" days from today's date)"),
                    'value' => Plumrocket_Estimateddelivery_Model_ProductCategory::DYNAMIC_RANGE
                ),
                array(
                    'label' => Mage::helper('estimateddelivery')->__('Static Date'),
                    'value' => Plumrocket_Estimateddelivery_Model_ProductCategory::STATIC_DATE
                ),
                array(
                    'label' => Mage::helper('estimateddelivery')->__('Static Date Range'),
                    'value' => Plumrocket_Estimateddelivery_Model_ProductCategory::STATIC_RANGE
                ),
                array(
                    'label' => Mage::helper('estimateddelivery')->__('Static Text'),
                    'value' => Plumrocket_Estimateddelivery_Model_ProductCategory::TEXT
                ),
            );
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
