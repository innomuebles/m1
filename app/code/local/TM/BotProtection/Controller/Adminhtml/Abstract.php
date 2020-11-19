<?php

abstract class TM_BotProtection_Controller_Adminhtml_Abstract
    extends Mage_Adminhtml_Controller_Action
{

    protected $_listName = '';

    protected $_breadcrumb = array();

    protected function _construct()
    {
        $this->_breadcrumb['blacklist'] = $this->_getHelper()->__('Blacklist');
        $this->_breadcrumb['pending'] = $this->_getHelper()->__('Suspicious');
        $this->_breadcrumb['whitelist'] = $this->_getHelper()->__('Whitelist');
        return parent::_construct();
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title($this->_getHelper()->__('TM'))
             ->_title($this->_getHelper()->__('Bot Protection'))
             ->_title($this->_breadcrumb[$this->_listName]);

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Create new  whitelist rule
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('item_id')) {
            try {
                // init model and delete
                $model = Mage::getModel('tm_botprotection/' . $this->_listName);
                $model->load($id);
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->_getHelper()->__(
                        'Item %s has been deleted.',
                        $model->getId()
                    )
                );
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')
                    ->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('item_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(
            $this->_getHelper()->__('Unable to find an item to delete.')
        );
        // go to grid
        $this->_redirect('*/*/');
    }

    public function massDisableAction()
    {
        $this->_massChangeStatus(0);
        $this->_redirect('*/*/index');
    }
    public function massEnableAction()
    {
        $this->_massChangeStatus(1);
        $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {
        $items = $this->getRequest()->getParam('items');
        if (is_array($items)) {
            if (!empty($items)) {
                try {
                    foreach ($items as $i) {
                        $item = Mage::getModel('tm_botprotection/'.$this->_listName)
                            ->load($i);
                        $item->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__(
                            'Total of %d items(s) have been deleted.',
                            count($items)
                        )
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Change status of list item via mass action
     * @param  integer $newStatus 1 - enabled / 0 - disabled
     */
    protected function _massChangeStatus($newStatus = 0)
    {
        $items = $this->getRequest()->getParam('items');
        if (is_array($items)) {
            if (!empty($items)) {
                try {
                    foreach ($items as $i) {
                        $item = Mage::getModel('tm_botprotection/'.$this->_listName)
                            ->load($i);
                        $item->setStatus($newStatus);
                        $item->save();
                    }
                    $msg = $newStatus ? 'Total of %d item(s) have been enabled.' :
                        'Total of %d item(s) have been disabled.';
                    $this->_getSession()->addSuccess(
                        $this->__($msg, count($item))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
    }

    /**
     * Init actions
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('templates_master/tm_botprotection')
            ->_addBreadcrumb(
                $this->_getHelper()->__('Bot Protection'),
                $this->_getHelper()->__('Bot Protection'))
            ->_addBreadcrumb(
                $this->_breadcrumb[$this->_listName],
                $this->_breadcrumb[$this->_listName]
            );
        return $this;
    }

    protected function _getHelper()
    {
        return Mage::helper('tm_botprotection');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('templates_master/tm_botprotection/'.$this->_listName);
    }

}
