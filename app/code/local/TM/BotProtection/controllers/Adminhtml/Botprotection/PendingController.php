<?php
/**
 *  Manage TM Bot Protection Pending controller
 */
class TM_BotProtection_Adminhtml_Botprotection_PendingController
    extends TM_BotProtection_Controller_Adminhtml_Abstract
{

    protected function _construct()
    {
        $this->_listName = 'pending';
        return parent::_construct();
    }

    /**
     * Edit rule
     */
    public function editAction()
    {
        $this->_title($this->_getHelper()->__('TM'))
             ->_title($this->_getHelper()->__('Bot Protection'))
             ->_title($this->_getHelper()->__('Suspicious list'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('item_id');
        $model = Mage::getModel('tm_botprotection/pending');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getItemId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->_getHelper()
                        ->__('This pending list item no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        if ($model->getItemId()) {
            $this->_title($this->_getHelper()->__('Edit pending list item'));
        } else {
            $this->_title($this->_getHelper()->__('New pending list item'));
        }

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('tm_pending', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? $this->_getHelper()->__('Edit pending list item')
                    : $this->_getHelper()->__('New pending list item'),
                $id ? $this->_getHelper()->__('Edit pending list item')
                    : $this->_getHelper()->__('New pending list item'));

        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        if ($this->getRequest()->getParam('moveto')) {
            $moveTo = $this->getRequest()->getParam('moveto');
            $this->_saveTo('tm_botprotection/'.$moveTo);

        } elseif ($data = $this->getRequest()->getPost()) {
            //init model and set data
            $model = Mage::getModel('tm_botprotection/pending');
            if ($id = $this->getRequest()->getParam('item_id')) {
                $model->load($id);
            } else {
                $data['detected_by'] = $this->_getHelper()
                    ->getDetectedByValue('MANUALLY');
            }

            $model->setData($data);
            // try to save whitelist rule
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

    protected function _saveTo($recievingModel = 'tm_botprotection/blacklist')
    {
        $prettyNames['tm_botprotection/blacklist'] =
            $this->_getHelper()->__("Blacklist");
        $prettyNames['tm_botprotection/whitelist'] =
            $this->_getHelper()->__("Whitelist");
        // check if we know what should be moved
        if ($data = $this->getRequest()->getPost()) {
            // init and load pending list item
            $modelPending = Mage::getModel('tm_botprotection/pending');
            $modelPending->load($data['item_id']);
            //init recieving model
            $modelRecieving = Mage::getModel($recievingModel);
            $modelRecieving->setData('ip_unpacked', $data['ip_unpacked']);
            $modelRecieving->setData('crawler_name', $data['crawler_name']);
            $modelRecieving->setData(
                'detected_by',
                $this->_getHelper()->getDetectedByValue('VIA_SUSPICIOUS_LIST')
            );
            try {
                $modelRecieving->save();
                $modelPending->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->_getHelper()->__(
                        "Item %s has been deleted.",
                         $modelPending->getId()
                    )
                );
                if ($data['crawler_name']) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        $this->_getHelper()->__(
                            "Crawler '%s' from IP %s has been added to %s.",
                            $data['crawler_name'],
                            $data['ip_unpacked'],
                            $prettyNames[$recievingModel]
                        )
                    );
                } else {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        $this->_getHelper()->__(
                            "IP %s has been added to %s.",
                            $data['ip_unpacked'], $prettyNames[$recievingModel]
                        )
                    );
                }
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
    }

}
