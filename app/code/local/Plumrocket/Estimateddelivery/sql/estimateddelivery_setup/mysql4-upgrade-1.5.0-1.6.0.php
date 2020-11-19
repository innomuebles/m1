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

class EstimatedDeliveryInstallClass160
{
    protected $_model = null;
    protected $_type = null;
    protected $_setup = null;

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
        $this->_addAttribute('estimated_%s_text', array(
            'type'          => 'text',
            'label'         => $this->name('Estimated %s Text', true),
            'input'         => 'textarea',
            // 'note'          => $this->name("Text field has higher priority over the estimated %s date field. Make sure it is empty if you don't want text to be displayed."),
            'wysiwyg_enabled'           => 1,
            // 'visible_on_front'          => 1,
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

}

$categoryModel = new EstimatedDeliveryInstallClass160('catalog_category', 'delivery');
$categoryModel->addAll()
    ->setType('shipping')
    ->addAll()
    ->orderGroup(40);

$productModel = new EstimatedDeliveryInstallClass160('catalog_product', 'delivery');
$productModel->addAll()
    ->setType('shipping')
    ->addAll()
    ->orderGroup(500);

$installer->endSetup();