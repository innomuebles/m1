<?php
/**
 *  Manage TM Bot Protection Blacklist controller
 */
class TM_BotProtection_Adminhtml_Botprotection_BlacklistController
    extends TM_BotProtection_Controller_Adminhtml_Abstract
{

    protected function _construct()
    {
        $this->_listName = 'blacklist';
        return parent::_construct();
    }

    /**
     * Edit rule
     */
    public function editAction()
    {
        $this->_title($this->_getHelper()->__('TM'))
             ->_title($this->_getHelper()->__('Bot Protection'))
             ->_title($this->_getHelper()->__('Blacklist'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('item_id');
        $model = Mage::getModel('tm_botprotection/blacklist');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getItemId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->_getHelper()
                        ->__('This blacklist item no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        if ($model->getItemId()) {
            $this->_title($this->_getHelper()->__('Edit Blacklisted Visitor'));
        } else {
            $this->_title($this->_getHelper()->__('New Blacklisted Visitor'));
        }

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('tm_blacklist', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? $this->_getHelper()->__('Edit Blacklisted Visitor')
                    : $this->_getHelper()->__('New Blacklisted Visitor'),
                $id ? $this->_getHelper()->__('Edit Blacklisted Visitor')
                    : $this->_getHelper()->__('New Blacklisted Visitor'));

        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {

            //init model and set data
            $model = Mage::getModel('tm_botprotection/blacklist');

            if ($id = $this->getRequest()->getParam('item_id')) {
                $model->load($id);

            } else {
                $data['detected_by'] = $this->_getHelper()
                    ->getDetectedByValue('MANUALLY');
            }

            $model->setData($data);

            // try to save blacklist rule
            try {
                // save the data
                $model->save();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->_getHelper()->__(
                        'Item %s has been saved.',
                        $model->getItemId()
                    )
                );
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect(
                        '*/*/edit',
                        array(
                            'item_id' => $model->getItemId(),
                            '_current'=>true
                        )
                    );
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    $this->_getHelper()->__('An error occurred while saving.')
                );
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect(
                '*/*/edit',
                array('item_id' => $this->getRequest()->getParam('item_id'))
            );
            return;
        }
        $this->_redirect('*/*/');
    }

}
