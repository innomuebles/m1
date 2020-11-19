<?php 

class MST_Storepickup_Block_Adminhtml_Message_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('message_form', array('legend' => Mage::helper('storepickup')->__('Message Information')));
        return parent::_prepareForm();
    }
}