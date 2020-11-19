<?php

class TM_BotProtection_Block_Adminhtml_Pending_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize tm BotProtection Pending edit block
     *
     * @return void
     */
    public function __construct()
    {
        $this->_objectId   = 'item_id';
        $this->_blockGroup = 'tm_botprotection';
        $this->_controller = 'adminhtml_pending';

        parent::__construct();

        $this->_removeButton('reset');
        $this->_removeButton('save');

        if (!$this->_isAllowedAction('delete')) {
            $this->_removeButton('delete');
        }

        if ($this->_isAllowedAction('save')) {
            $this->_addButton('toblacklist', array(
                'label'     => Mage::helper('tm_botprotection')->__("Move to blacklist"),
                'title'     => Mage::helper('tm_botprotection')->__("Move current item to blacklist"),
                'onclick'   => 'moveToBotlist(\'blacklist\')'
            ), 0, 4);
            $this->_addButton('towhitelist', array(
                'label'     => Mage::helper('tm_botprotection')->__("Move to whitelist"),
                'title'     => Mage::helper('tm_botprotection')->__("Move current item to whitelist"),
                'onclick'   => 'moveToBotlist(\'whitelist\')'
            ), 0, 8);
            $this->_formScripts[] = " function moveToBotlist(listName)"
                ."{ editForm.submit($('edit_form').action + 'moveto/' + listName + '/') } ";
            $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save',
            ), 1);
            $this->_formScripts[] = " function saveAndContinueEdit()"
                ."{ editForm.submit($('edit_form').action + 'back/edit/') } ";
        }

    }

    /**
     * Retrieve text for header element depending on loaded deal
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('tm_pending')->getItemId()) {
            return Mage::helper('tm_botprotection')->__("Edit pending list item");
        }
        else {
            return Mage::helper('tm_botprotection')->__('New pending list item');
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
                ->isAllowed('templates_master/tm_botprotection/pending/' . $action);
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

    private function unichr($i)
    {
        return iconv('UCS-4LE', 'UTF-8', pack('V', $i));
    }

    protected function _getMoveUrl($urlKey)
    {
        return $this->getUrl(
            '*/*/'.$urlKey,
            array(
                $this->_objectId => $this->getRequest()->getParam($this->_objectId)
            )
        );
    }

}

