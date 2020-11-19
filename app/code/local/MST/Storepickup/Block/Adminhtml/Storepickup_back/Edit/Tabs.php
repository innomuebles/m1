<?php 

class MST_Storepickup_Block_Adminhtml_Storepickup_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('storepickup_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('storepickup')->__('Store Information'));
    }
    protected function _beforeToHtml()
    {
        
		
		$this->addTab('form_section', array(
            'label' => Mage::helper('storepickup')->__('Store Information'),
            'title' => Mage::helper('storepickup')->__('Store Information'),
            'content' => $this->getLayout()->createBlock('storepickup/adminhtml_storepickup_edit_tab_form')->toHtml(),
        ));
		  $this->addTab('form_section_contacts', array(
            'label' => Mage::helper('storepickup')->__('Contact Information'),
            'title' => Mage::helper('storepickup')->__('Contact Information'),
            'content' => $this->getLayout()->createBlock('storepickup/adminhtml_storepickup_edit_tab_contacts')->toHtml(),
        ));
         $this->addTab('form_section_timeschedule', array(
            'label' => Mage::helper('storepickup')->__('Time Schedule'),
            'title' => Mage::helper('storepickup')->__('Time Schedule'),
            'content' => $this->getLayout()->createBlock('storepickup/adminhtml_storepickup_edit_tab_schedule')->toHtml(),
        ));
         $this->addTab('grid_section_messages', array(
            'label' => Mage::helper('storepickup')->__('Customer Message'),
            'alt' => Mage::helper('storepickup')->__('Customer Message'),
            'content' => $this->getLayout()->createBlock('storepickup/adminhtml_storepickup_grid_gridmessages')->toHtml(),
        ));
        $this->addTab('grid_section_orders', array(
            'label' => Mage::helper('storepickup')->__('Rerated Orders'),
            'alt' => Mage::helper('storepickup')->__('Rerated Orders'),
            'content' => $this->getLayout()->createBlock('storepickup/adminhtml_storepickup_grid_gridorders')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}