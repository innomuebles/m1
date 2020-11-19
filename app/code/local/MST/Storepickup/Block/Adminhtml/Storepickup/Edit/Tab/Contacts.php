<?php
class MST_Storepickup_Block_Adminhtml_Storepickup_Edit_Tab_Contacts extends Mage_Adminhtml_Block_Widget_Form {
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('storepickup_contacts', array('legend' => Mage::helper('storepickup')->__('Contact Information')));
        $fieldset->addField('pickup_manager', 'text', array(
            'label' => Mage::helper('storepickup')->__('Store Manager'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'pickup_manager',
        ));
        $fieldset->addField('pickup_phone', 'text', array(
            'label' => Mage::helper('storepickup')->__('Phone Number'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'pickup_phone',
        ));
         $fieldset->addField('pickup_email', 'text', array(
            'label' => Mage::helper('storepickup')->__('Email'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'pickup_email',
        ));
         $fieldset->addField('pickup_fax', 'text', array(
            'label' => Mage::helper('storepickup')->__('Fax'),
            'class' => '',
            'required' => false,
            'name' => 'pickup_fax',
        ));
        if (Mage::getSingleton('adminhtml/session')->getStorepickup()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getStorepickup());
            Mage::getSingleton('adminhtml/session')->getStorepickup(null);
        } elseif (Mage::registry('storepickup_data')) {
            $form->setValues(Mage::registry('storepickup_data')->getData());
        }
        return parent::_prepareForm();
    }

}
?>