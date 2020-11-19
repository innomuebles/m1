<?php 

class MST_Storepickup_Block_Adminhtml_Message_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('message_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('storepickup')->__('Message'));
    }
    protected function _beforeToHtml()
    {
        
		
		$this->addTab('form_section', array(
            'label' => Mage::helper('storepickup')->__('Message'),
            'title' => Mage::helper('storepickup')->__('Message'),
            'content' => $this->getLayout()->createBlock('storepickup/adminhtml_message_grid_viewmessage')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}