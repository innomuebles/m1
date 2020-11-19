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

$installer = $this;
$installer->startSetup();

class EstimatedDeliveryInstallClass
{
    protected $_model = null;
    protected $_type = null;
    protected $_setup = null;

    protected $_oldAttributes = array();
    protected $_importKeys = array(
        //'estimated_delivery_enable'       => 'estimated_delivery_enable',
        'estimated_delivery_date'       => 'estimated_delivery_date_from',
        'estimated_delivery_days'       => 'estimated_delivery_days_from',
        'estimated_delivery_text'       => 'estimated_delivery_text', // convert types

        //'estimated_shipping_enable'       => 'estimated_shipping_enable',
        //'estimated_shipping_date_from'    => 'estimated_shipping_date_from',
        //'estimated_shipping_date_to'  => 'estimated_shipping_date_to',
        'estimated_shipping_days'       => 'estimated_shipping_days_from',
        'estimated_shipping_text'       => 'estimated_shipping_text', // convert types
    );

    protected $_updateEnableData = array(
        'estimated_delivery_enable'     => array(
            'estimated_delivery_days'       => Plumrocket_Estimateddelivery_Model_ProductCategory::DYNAMIC_DATE,
            'estimated_delivery_date'       => Plumrocket_Estimateddelivery_Model_ProductCategory::STATIC_DATE,
            'estimated_delivery_text'       => Plumrocket_Estimateddelivery_Model_ProductCategory::TEXT,
        ),

        'estimated_shipping_enable'     => array(
            'estimated_shipping_days'       => Plumrocket_Estimateddelivery_Model_ProductCategory::DYNAMIC_DATE,
            'estimated_shipping_date_from'  => Plumrocket_Estimateddelivery_Model_ProductCategory::STATIC_DATE,
            'estimated_shipping_date_to'    => Plumrocket_Estimateddelivery_Model_ProductCategory::STATIC_RANGE,
            'estimated_shipping_text'       => Plumrocket_Estimateddelivery_Model_ProductCategory::TEXT,
        ),
    );

    public function __construct($model, $type)
    {
        $this->_model = $model;
        $this->_type  = $type;
        $this->_setup = Mage::getModel('catalog/resource_setup', 'core_setup');
    }

    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    public function name($string, $firstToUpper = false, $past = false)
    {
        $pastArray = array('delivery' => 'delivered', 'shipping' => 'shipped');

        $result = $this->_type;
        if ($past) {
            $result = $pastArray[$this->_type];
        }
        if ($firstToUpper) {
            $result = ucfirst($result);
        }
        return sprintf($string, $result);
    }

    public function getGroup()
    {
        return Mage::helper('estimateddelivery')->getGroupName();
    }

    protected function _appendInfo($data, $sort_order, $required = 0) {
        if ($this->_type == 'shipping') {
            $sort_order += 100;
        }

        $data['global']     = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE;
        $data['visible']    = 1;
        $data['required']   = $required;
        $data['group']      = $this->getGroup();
        $data['sort_order'] = $sort_order;
        return $data;
    }

    protected function _addAttribute($name, $data, $sort_order, $required = 0)
    {
        $this->_setup->addAttribute(
            $this->_model, 
            $this->name($name), 
            $this->_appendInfo($data, $sort_order, $required)
        );
    }

    public function addAll()
    {
        $this->_prepareImportInfo();

        $this->_addAttribute('estimated_%s_enable', array(
            'type'          => 'int',
            'label'         => $this->name('%s Date(s)', true),
            'input'         => 'select',
            'source'        => 'estimateddelivery/attribute_source_enable',
            'default'       => 0,
        ), 10, 1);

        $this->_addAttribute('estimated_%s_days_from', array(
            'type'          => 'int',
            'label'         => $this->name('Business Days For %s', true),
            'input'         => 'text',
            'note'          => $this->name("Number of business days (excluding weekends and holidays) from today's date required for the product to be %s.", false, true),
        ), 20);

        $this->_addAttribute('estimated_%s_days_to', array(
            'type'          => 'int',
            'label'         => '',
            'input'         => 'text',
        ), 30);

        $this->_addAttribute('estimated_%s_date_from', array(
            'type'          => 'datetime',
            'label'         => $this->name('Estimated %s Date', true),
            'input'         => 'date',
            'backend'       => 'eav/entity_attribute_backend_datetime',
        ), 40);

        $this->_addAttribute('estimated_%s_date_to', array(
            'type'          => 'datetime',
            'label'         => '',
            'input'         => 'date',
            'backend'       => 'eav/entity_attribute_backend_datetime',
        ), 50);

        $this->_addAttribute('estimated_%s_text', array(
            'type'          => 'text',
            'label'         => $this->name('Estimated %s Text', true),
            'input'         => 'textarea',
            // 'note'          => $this->name("Text field has higher priority over the estimated %s date field. Make sure it is empty if you don't want text to be displayed."),
            'wysiwyg_enabled'           => 1,
            'visible_on_front'          => 1,
            'is_html_allowed_on_front'  => 1,
        ), 60);
        return $this;
    }

    public function orderGroup($order)
    {
        $entityTypeId = $this->_setup->getEntityTypeId($this->_model);

        $sets = Mage::getModel('eav/entity_attribute_set')
            ->getResourceCollection()
            ->addFilter('entity_type_id', $entityTypeId);

        foreach ($sets as $set) {
            $this->_setup->addAttributeGroup($entityTypeId, $set->getData('attribute_set_id'), $this->getGroup(), $order);
        }
        return $this;
    }

    protected function _prepareImportInfo()
    {
        foreach ($this->_importKeys as $from => $to) {
            $id = $this->_setup->getAttributeId($this->_model, $from);
            $table = $this->_setup->getAttributeTable($this->_model, $from);

            if (!empty($id) && !empty($table)) {
                $this->_oldAttributes[$from] = array(
                    'id'    => $id,
                    'table' => $table,
                );
            }
        }
    }

    public function importExistsData($installer)
    {
        $this->_updateEnable($installer);

        foreach ($this->_oldAttributes as $from => $fromData) {
            $to = $this->_importKeys[$from];

            $fromId = $fromData['id'];
            $toId = $this->_setup->getAttributeId($this->_model, $to);

            $fromTable = $fromData['table'];
            $toTable = $this->_setup->getAttributeTable($this->_model, $to);

            if (!empty($toId) && !empty($toTable)) {
                // can be converting types
                if ($fromId != $toId) {
                    $this->_setup->removeAttribute($this->_model, $from);
                }

                if ($fromTable == $toTable) {
                    $installer->run(sprintf("UPDATE `%s` SET `attribute_id` = '%s' WHERE `attribute_id` = '%s'",
                        $toTable,
                        $toId,
                        $fromId
                    ));
                } else {
                    $installer->run(sprintf("INSERT INTO `%s` (`entity_type_id`, `attribute_id`, `store_id`, `entity_id`, `value`)
                        (SELECT `entity_type_id`, '%s', `store_id`, `entity_id`, `value` FROM `%s`
                            WHERE `attribute_id` = '%s')",
                        $toTable,
                        $toId,
                        $fromTable,
                        $fromId
                    ));

                    $installer->run(sprintf('DELETE FROM `%s` WHERE attribute_id = %s',
                        $fromTable,
                        $fromId
                    ));
                }
            }
        }
    }

    protected function _updateEnable($installer)
    {
        foreach ($this->_updateEnableData as $name => $items) {
            $id = $this->_setup->getAttributeId($this->_model, $name);
            $table = $this->_setup->getAttributeTable($this->_model, $name);

            foreach ($items as $fname => $value) {
                $fid = $this->_setup->getAttributeId($this->_model, $fname);
                $ftable = $this->_setup->getAttributeTable($this->_model, $fname);

                if ($fid && $ftable) {
                    $installer->run(sprintf("
                        UPDATE `%s` SET `value` = '%s' 
                        WHERE `value` > 1 AND `attribute_id` = '%s' AND `entity_id` IN (
                            SELECT `entity_id` FROM %s WHERE attribute_id = '%s' AND `value` != ''
                        )",
                        $table,
                        $value,
                        $id,

                        $ftable,
                        $fid
                    ));
                }
            }
        }
    }
}

$categoryModel = new EstimatedDeliveryInstallClass('catalog_category', 'delivery');
$categoryModel->addAll()
    ->setType('shipping')
    ->addAll()
    ->orderGroup(40)
    ->importExistsData($installer);

$productModel = new EstimatedDeliveryInstallClass('catalog_product', 'delivery');
$productModel->addAll()
    ->setType('shipping')
    ->addAll()
    ->orderGroup(500)
    ->importExistsData($installer);

$installer->endSetup();