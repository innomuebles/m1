<?php 

class MST_Storepickup_Adminhtml_MessageController extends Mage_Adminhtml_Controller_action
{
     protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('storepickup/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Manage Store'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }
    function editAction()
    {
       $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('storepickup/messages')->load($id);
         if ($model->getId() || $id == 0) {
           Mage::register('message_data', $model);
            $this->loadLayout();
             $this->_addContent($this->getLayout()->createBlock('storepickup/adminhtml_message_edit'))
                ->_addLeft($this->getLayout()->createBlock('storepickup/adminhtml_message_edit_tabs'));
            $this->renderLayout();
        }
        else
        {
          Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storepickup')->__('Message Not exit'));
            $this->_redirect('*/*/');   
        }
         
    }
}