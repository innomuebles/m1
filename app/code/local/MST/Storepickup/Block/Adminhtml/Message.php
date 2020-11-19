<?php

class MST_Storepickup_Block_Adminhtml_Message extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_message';
        $this->_blockGroup = 'storepickup';
        $this->_headerText = Mage::helper('storepickup')->__('Manage Store');
        $this->_addButtonLabel = Mage::helper('storepickup')->__('Add Item');
        parent::__construct();
    }
}