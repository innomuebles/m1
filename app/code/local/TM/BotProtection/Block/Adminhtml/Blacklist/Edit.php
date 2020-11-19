<?php

class TM_BotProtection_Block_Adminhtml_Blacklist_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize tm BotProtection Blacklist edit block
     *
     * @return void
     */
    public function __construct()
    {
        $this->_objectId   = 'item_id';
        $this->_blockGroup = 'tm_botprotection';
        $this->_controller = 'adminhtml_blacklist';

        parent::__construct();

        if ($this->_isAllowedAction('save')) {
            $this->_updateButton(
                'save',
                'label',
                Mage::helper('tm_botprotection')->__('Save')
            );
            $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndContinueUrl().'\')',
                'class'     => 'save',
            ), -100);
            $this->_formScripts[] = " function saveAndContinueEdit()"
                ."{ editForm.submit($('edit_form').action + 'back/edit/') } ";
        } else {
            $this->_removeButton('save');
        }

        if ($this->_isAllowedAction('delete')) {
            $this->_updateButton(
                'delete',
                'label',
                Mage::helper('tm_botprotection')->__('Delete')
            );
        } else {
            $this->_removeButton('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded deal
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('tm_blacklist')->getItemId()) {
            return Mage::helper('tm_botprotection')->__("Edit Blacklisted Visitor");
        }
        else {
            return Mage::helper('tm_botprotection')->__('New Blacklisted Visitor');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')
                ->isAllowed('templates_master/tm_botprotection/blacklist/' . $action);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit'
        ));
    }
}
