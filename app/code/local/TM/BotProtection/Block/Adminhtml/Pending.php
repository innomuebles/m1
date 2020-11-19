<?php
/**
 *  Adminhtml TM Bot Protection content block
 */
class TM_BotProtection_Block_Adminhtml_Pending
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'tm_botprotection';
        $this->_controller = 'adminhtml_pending';
        $this->_headerText = Mage::helper('tm_botprotection')
            ->__('Manage Pending List');

        parent::__construct();

        if ($this->_isAllowedAction('save')) {
            $this->_updateButton(
                'add',
                'label',
                Mage::helper('tm_botprotection')->__('Add to Pending List')
            );
        } else {
            $this->_removeButton('add');
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

}
