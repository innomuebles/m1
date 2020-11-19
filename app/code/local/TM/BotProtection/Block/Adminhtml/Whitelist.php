<?php
/**
 *  Adminhtml TM Bot Protection content block
 */
class TM_BotProtection_Block_Adminhtml_Whitelist
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'tm_botprotection';
        $this->_controller = 'adminhtml_whitelist';
        $this->_headerText = Mage::helper('tm_botprotection')
            ->__('Manage Whitelist');

        parent::__construct();

        if ($this->_isAllowedAction('save')) {
            $this->_updateButton(
                'add',
                'label',
                Mage::helper('tm_botprotection')->__('Add To Whitelist')
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
            ->isAllowed('templates_master/tm_botprotection/whitelist/' . $action);
    }

}
