<?php

class MST_Storepickup_Model_System_Config_Styles 
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'fade', 'label'=>Mage::helper('adminhtml')->__('Fade')),
            array('value' => 'slide', 'label'=>Mage::helper('adminhtml')->__('Slide')),            
        );
    }
}