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

class Plumrocket_Estimateddelivery_Model_ProductCategory extends Mage_Core_Model_Abstract
{
    const INHERITED = 0;
    const DISABLED = 1;
    const DYNAMIC_DATE = 2;
    const DYNAMIC_RANGE = 3;
    const STATIC_DATE = 4;
    const STATIC_RANGE = 5;
    const TEXT = 6;

    private $_result = null;
    private $_dateEnd = '';

    private $_product = null;
    private $_category = null;

    public function getProduct()
    {
        if (is_null($this->_product)) {
            $this->_product = Mage::registry('product');

            if (!$this->_product || !$this->_product->getId()) {
                $this->_product = Mage::getModel('catalog/product');
            }
        }
        return $this->_product;
    }

    public function getCategory()
    {
        if (is_null($this->_category)) {
            $this->_category = Mage::registry('current_category');

            if (!$this->_category || !$this->_category->getId()) {
                $this->_category = Mage::getModel('catalog/category');
            }
        }
        return $this->_category;   
    }

    public function setProduct($product)
    {
        $this->reset();

        if(is_null($product->getData('estimated_delivery_enable')) && is_null($product->getData('estimated_shipping_enable')) && $product->getId()) {
            $product = Mage::getModel('catalog/product')->load($product->getId());
        }
        
        $this->_product = $product;
    }

    public function setCategory($category)
    {
        $this->reset();
        $this->_category = $category;
    }

    public function reset()
    {
        $this->_result = null;
        $this->_dateEnd = '';
        $this->_category = null;
        $this->_product = null;
    }

    public function getSourceData()
    {   
        if (!$this->_result) {
            $this->_result = array();

            if ((Mage::getSingleton('plumbase/observer')->customer() == Mage::getSingleton('plumbase/product')->currentCustomer())
                && Mage::helper('estimateddelivery')->moduleEnabled()
            ) {
                $this->_result = array(
                    'delivery' => $this->_getData('delivery'),
                    'shipping' => $this->_getData('shipping')
                );
            }
        }
        return $this->_result;
    }
    
    // ---- Private functions
    protected function _getData($type)
    {
        $product = $this->getProduct();
        if ($product && $product->getId()) {
            $result = $this->_getDataFromProduct($product, $type);
        } else {
            $category = $this->getCategory();
            $result = $this->_getDataFromCategory($category, $type);
        }

        // 0 or False
        if (!$result) {
            $inherited = $result === self::INHERITED;
            $result = array('from' => '', 'to' => '', 'text' => '');

            if ($inherited) {
                $privatesaleDays = (int)Mage::getStoreConfig('estimateddelivery/'. $type .'/default_days');
                if ($this->_dateEnd && $privatesaleDays) {
                    $result['from'] = $this->_formatDate($this->_dateEnd, $type);
                }
            }
        }

        return $result;
    }

    protected function _getDataFromProduct($product, $type)
    {
        $result = self::INHERITED;

        if ($this->_value($product, $type, 'enable') != self::INHERITED) {
            return $this->_parseData($product, $type);
        } else {
            // scan categories
            $cIds = $product->getCategoryIds();
            if ($cIds) {
                // foreach by all parents' categories of product and check if any parent set or him parents
                foreach ($cIds as $cid) {

                    $cat = Mage::getModel('catalog/category')->load($cid);
                    $res = $this->_getDataFromCategory($cat, $type);

                    // if at least parent is enabled then product is enabled
                    // else return will be 0 - inherited or False - disable
                    if ($res) {
                        $result = $res;
                        break;
                    }
                    // If at end all parents will be inherited exept one or each disabled 
                    // then product will be disabled
                    if ($res === false) {
                        $result = false;
                    }
                }
            }
        }
        return $result;
    }

    protected function _getDataFromCategory($cat, $type)
    {
        $result = self::INHERITED;
        $parentIds = $cat->getParentIds();
        
        do {
            if ($cat && $cat->getId() && $cat->getIsActive()) {
                if (!$this->_dateEnd) {
                    // Keep its' date end if earlier we have'nt assign it. 
                    // * If Plumrocket_Privatesales has been installed.
                    // If in future we will be use this variable then because
                    // all categories was scanned - and this param will full loaded
                    $this->_dateEnd = $cat->getData('privatesale_date_end');
                }
                if ($this->_value($cat, $type, 'enable') != self::INHERITED) {
                    $result = $this->_parseData($cat, $type);    
                    break;
                }
            }

            $pid = array_pop($parentIds);
            if ($pid) {
                $cat = Mage::getModel('catalog/category')->load($pid);
            }
        } while ($pid);

        return $result;
    }

    protected function _parseData($object, $type)
    {
        $result = array('from' => '', 'to' => '', 'text' => '');
        $enable = $this->_value($object, $type, 'enable');

        switch ($enable) {
            case self::DYNAMIC_RANGE:
                $result['to'] = $this->_formatDate( $this->_value($object, $type, 'days_to'), $type );
            case self::DYNAMIC_DATE:
                $result['from'] = $this->_formatDate( $this->_value($object, $type, 'days_from'), $type );
                break;

            case self::STATIC_RANGE:
                $result['to'] = $this->_value($object, $type, 'date_to');
            case self::STATIC_DATE:
                $result['from'] = $this->_value($object, $type, 'date_from');
                break;

            case self::TEXT:
                $result['text'] = $this->_value($object, $type, 'text');
                break;

            case self::DISABLED:
            default:
                $result = false;
                break;
        }

        if(false !== $result) {
            if (empty($result['text']) && Mage::getStoreConfig('estimateddelivery/' . $type . '/default_text_enable')) {
                $result['text'] = trim(Mage::getStoreConfig('estimateddelivery/' . $type . '/default_text'));
            }
        }
        
        return $result;
    }

    protected function _value($object, $type, $param)
    {
        return $object->getData( $this->_param($type, $param) );
    }

    protected function _param($type, $param)
    {
        return 'estimated_' . $type . '_' . $param;
    }

    protected function _formatDate($value, $type)
    {
        $time = Mage::app()->getLocale()->storeTimeStamp();
        return strftime(
             '%Y-%m-%d %H:%M:%S',
            Mage::helper('estimateddelivery/bankday')->getEndDate($type, $time, (int)$value)
        );
    }

    // @deprecated
    public function _getSourceData() { return $this->getSourceData(); }
}
