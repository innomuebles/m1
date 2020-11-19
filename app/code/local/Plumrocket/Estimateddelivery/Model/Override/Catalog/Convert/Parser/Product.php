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

class Plumrocket_Estimateddelivery_Model_Override_Catalog_Convert_Parser_Product 
    extends Mage_Catalog_Model_Convert_Parser_Product
{

    public function unparse()
    {
        $entityIds = $this->getData();

        foreach ($entityIds as $i => $entityId) {
            $product = $this->getProductModel()
                ->setStoreId($this->getStoreId())
                ->load($entityId);
            $this->setProductTypeInstance($product);
            /* @var $product Mage_Catalog_Model_Product */

            $position = Mage::helper('catalog')->__('Line %d, SKU: %s', ($i+1), $product->getSku());
            $this->setPosition($position);

            $row = array(
                'store'         => $this->getStore()->getCode(),
                'websites'      => '',
                'attribute_set' => $this->getAttributeSetName($product->getEntityTypeId(),
                                        $product->getAttributeSetId()),
                'type'          => $product->getTypeId(),
                'category_ids'  => join(',', $product->getCategoryIds())
            );

            if ($this->getStore()->getCode() == Mage_Core_Model_Store::ADMIN_CODE) {
                $websiteCodes = array();
                foreach ($product->getWebsiteIds() as $websiteId) {
                    $websiteCode = Mage::app()->getWebsite($websiteId)->getCode();
                    $websiteCodes[$websiteCode] = $websiteCode;
                }
                $row['websites'] = join(',', $websiteCodes);
            } else {
                $row['websites'] = $this->getStore()->getWebsite()->getCode();
                if ($this->getVar('url_field')) {
                    $row['url'] = $product->getProductUrl(false);
                }
            }

            foreach ($product->getData() as $field => $value) {
                if (in_array($field, $this->_systemFields) || is_object($value)) {
                    continue;
                }

                $attribute = $this->getAttribute($field);
                if (!$attribute) {
                    continue;
                }

                if ($attribute->usesSource()) {
                    $option = $attribute->getSource()->getOptionText($value);
                    if ($value && empty($option) && $option != '0') {
                        $this->addException(
                            Mage::helper('catalog')->__('Invalid option ID specified for %s (%s), skipping the record.', $field, $value),
                            Mage_Dataflow_Model_Convert_Exception::ERROR
                        );
                        continue;
                    }
                    if (is_array($option)) {
                        $value = join(self::MULTI_DELIMITER, $option);
                    } else {
                        $value = $option;
                    }
                    unset($option);
                } elseif (is_array($value)) {
                    continue;
                }

                $row[$field] = $value;
            }

            if ($stockItem = $product->getStockItem()) {
                foreach ($stockItem->getData() as $field => $value) {
                    if (in_array($field, $this->_systemFields) || is_object($value)) {
                        continue;
                    }
                    $row[$field] = $value;
                }
            }

            foreach ($this->_imageFields as $field) {
                if (isset($row[$field]) && $row[$field] == 'no_selection') {
                    $row[$field] = null;
                }
            }
            // @added by Plumrocket
            if (Mage::helper('estimateddelivery')->moduleEnabled()) {
                $row = $this->_processEstimatedData($row, $product);
            }
            // -----

            $batchExport = $this->getBatchExportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData($row)
                ->setStatus(1)
                ->save();
            $product->reset();
        }

        return $this;
    }

    private function _processEstimatedData($row, $product)
    {
        // Delivery
        if (
            // If product has not disabled specially for him..
            (! isset($row['estimated_delivery_enable'])
                || ($row['estimated_delivery_enable'] != 'Disabled')
            )
            // ... and date(s) and text fields is empty
            && empty($row['estimated_delivery_text'])
            && empty($row['estimated_delivery_date'])
        ) {
            // process
            $data = $this->_getEstimatedData($product);
            if (isset($data['delivery']) && $data['delivery']['enable']) {
                $value = $data['delivery']['value'];

                if ($data['delivery']['is_date']) {
                    $row['estimated_delivery_date'] = $this->_specialFormatDate(strtotime($value));
                } elseif ($value != Mage::getStoreConfig('estimateddelivery/delivery/default_text')) {
                    $row['estimated_delivery_text'] = $value;
                }
            }
        } elseif (! empty($row['estimated_delivery_date'])) {
            $row['estimated_delivery_date'] = $this->_specialFormatDate(strtotime($row['estimated_delivery_date']));
        }

        // Shipping
        if (
            // If product has not disabled specially for him..
            (! isset($row['estimated_shipping_enable'])
                || ($row['estimated_shipping_enable'] != 'Disabled')
            )
            // ... and date(s) and text fields is empty
            && empty($row['estimated_shipping_text'])
            && empty($row['estimated_shipping_date_from'])
            && empty($row['estimated_shipping_date_to'])
        ) {
            // process
            foreach (array('from', 'to') as $type) {
                $data = $this->_getEstimatedData($product);
                if (isset($data['shipping']) && $data['shipping'][$type]['enable']) {
                    $value = $data['shipping'][$type]['value'];

                    if ($data['shipping'][$type]['is_date']) {
                        $row['estimated_shipping_date_' . $type] = $this->_specialFormatDate(strtotime($value));
                    } elseif ($value != Mage::getStoreConfig('estimateddelivery/shipping/default_text')) {
                        $row['estimated_shipping_text'] = $value;
                    }
                }
            }
        } else {
            if (! empty($row['estimated_shipping_date_from'])) {
                $row['estimated_shipping_date_from'] = $this->_specialFormatDate(strtotime($row['estimated_shipping_date_from']));
            }
            if (! empty($row['estimated_shipping_date_to'])) {
                $row['estimated_shipping_date_to'] = $this->_specialFormatDate(strtotime($row['estimated_shipping_date_to']));
            }
        }    
        return $row;
    }

    private $_cacheForEstimatedData = array();

    private function _getEstimatedData($product)
    {
        if (Mage::registry('product')) {
            if (Mage::registry('product')->getId() == $product->getId()) {
                return $this->_cacheForEstimatedData;
            }
            Mage::unregister('product');
        }
        Mage::register('product', $product);

        $data = Mage::getModel('estimateddelivery/productCategory')->_getSourceData();
        $this->_cacheForEstimatedData = $data;
    }

    public function _specialFormatDate($time)
    {
        $locale = new Zend_Locale(Mage::app()->getLocale()->getLocaleCode());
        $date = new Zend_Date($time, false, $locale);
        
        return $date->toString(
            Mage::app()->getLocale()->getDateFormat(
                Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
    }
}
