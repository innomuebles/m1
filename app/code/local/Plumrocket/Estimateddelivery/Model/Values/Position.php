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
@copyright  Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
@license    http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/

class Plumrocket_Estimateddelivery_Model_Values_Position
{
    const CATEGORY              = 'category';
    const PRODUCT               = 'product';
    const SHOPPING_CART         = 'shopping_cart';
    const CHECKOUT              = 'checkout';
    const PM_ORDER_SUCCESS      = 'pm_order_success';
    const CUSTOMER_ORDER        = 'customer_order';
    const ORDER_CONFIRMATION    = 'order_confirmation';
    const INVOICE               = 'invoice';
    const SHIPMENT              = 'shipment';
    const ADMINPANEL_ORDER      = 'adminpanel_order';

    protected $_options = null;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_getOptions();
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = array();
        foreach ($this->_getOptions() as $option) {
            $options[ $option['value'] ] = $option['label'];
        }

        return $options;
    }

    protected function _getOptions()
    {
        if(is_null($this->_options)) {
            $checkoutspageEnabled = Mage::helper('estimateddelivery')->moduleCheckoutspageEnabled();

            $this->_options = array(
                array('label' => Mage::helper('estimateddelivery')->__('Frontend Pages'), 'value' => array(
                    array('value' => self::CATEGORY,            'label' => Mage::helper('estimateddelivery')->__('Category Page') ),
                    array('value' => self::PRODUCT,             'label' => Mage::helper('estimateddelivery')->__('Product Page') ),
                    array('value' => self::SHOPPING_CART,       'label' => Mage::helper('estimateddelivery')->__('Shopping Cart Page') ),
                    array('value' => self::CHECKOUT,            'label' => Mage::helper('estimateddelivery')->__('Checkout Page') ),
                    array('value' => self::PM_ORDER_SUCCESS,    'label' => Mage::helper('estimateddelivery')->__('Plumrocket Checkout Success Page'. (!$checkoutspageEnabled? ' (Not installed)' : '')), 'style' => (!$checkoutspageEnabled? 'color: #999;' : '') ),
                    array('value' => self::CUSTOMER_ORDER,      'label' => Mage::helper('estimateddelivery')->__('Customer Account > Order Page') ),
                )),
                array('label' => Mage::helper('estimateddelivery')->__('Emails'), 'value' => array(
                    array('value' => self::ORDER_CONFIRMATION,  'label' => Mage::helper('estimateddelivery')->__('Order Confirmation') ),
                    array('value' => self::INVOICE,             'label' => Mage::helper('estimateddelivery')->__('Invoice') ),
                    array('value' => self::SHIPMENT,            'label' => Mage::helper('estimateddelivery')->__('Shipment') ), 
                )),
                array('label' => Mage::helper('estimateddelivery')->__('Admin Panel'), 'value' => array(
                    array('value' => self::ADMINPANEL_ORDER,    'label' => Mage::helper('estimateddelivery')->__('Order Page') ),
                )),
            );
        }

        return $this->_options;
    }

}
