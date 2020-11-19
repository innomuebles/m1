<?php

class MST_Storepickup_Block_Adminhtml_Message_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
       // var_dump(get_class_methods($this));
        $this->_objectId = 'id';
        $this->_blockGroup = 'storepickup';
        $this->_controller = 'adminhtml_message';

        $this->_removeButton('back');
          $this->_removeButton('save');
        $this->_removeButton('reset');
        $this->_removeButton('delete');
        $this->_addButton('cancel', array(
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => 'setLocation(\'' . $this->getCancelUrl() . '\')',
            'class'     => 'back',
        ), -1);
    }
	
	protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getHeaderText()
    {
        if (Mage::registry('message_data') && Mage::registry('message_data')->getId()) {
            return Mage::helper('storepickup')->__("view Message'%s'", $this->htmlEscape(Mage::registry('message_data')->getPickup_contact_name()));
        }
    }
    public function getCancelUrl()
    {
        return $this->getUrl('*/adminhtml_storepickup/edit/id/'.Mage::registry('message_data')->getPickup_id());
    }
}