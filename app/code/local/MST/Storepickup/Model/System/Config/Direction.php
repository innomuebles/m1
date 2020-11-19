<?php

class MST_Storepickup_Model_System_Config_Direction 
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'horizontal', 'label'=>Mage::helper('adminhtml')->__('Horizontal')),
            array('value' => 'vertical', 'label'=>Mage::helper('adminhtml')->__('Vertical')),            
        );
    }
}