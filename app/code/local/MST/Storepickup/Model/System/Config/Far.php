<?php

class MST_Storepickup_Model_System_Config_Far
{
    public function toOptionArray()
    {
        return array(
            array('value' => '100', 'label'=>Mage::helper('adminhtml')->__('100 Km')),
            array('value' => '200', 'label'=>Mage::helper('adminhtml')->__('200 Km')),
            array('value' => '300', 'label'=>Mage::helper('adminhtml')->__('300 Km')),
            array('value' => '400', 'label'=>Mage::helper('adminhtml')->__('400 Km')),
            array('value' => '500', 'label'=>Mage::helper('adminhtml')->__('500 Km')),
            array('value' => '600', 'label'=>Mage::helper('adminhtml')->__('600 Km')),            
        );
    }
}