<?php 

class MST_Storepickup_Adminhtml_StorepickupController extends Mage_Adminhtml_Controller_action
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
        $this->_addContent($this->getLayout()->createBlock('storepickup/adminhtml_storepickup'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('storepickup/storepickup')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('storepickup_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('storepickup/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Storepickup'), Mage::helper('adminhtml')->__('Item Storepickup'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('storepickup/adminhtml_storepickup_edit'))
                ->_addLeft($this->getLayout()->createBlock('storepickup/adminhtml_storepickup_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storepickup')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
		$imagedata = array();
        if (!empty($_FILES['mapicon']['name'])) {
            try {
                $ext = substr($_FILES['mapicon']['name'], strrpos($_FILES['mapicon']['name'], '.') + 1);
                $fname = 'Imgae-' . time() . '.' . $ext;
                $uploader = new Varien_File_Uploader('mapicon');
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png')); // or pdf or anything

                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);

                $path = Mage::getBaseDir('media').DS.'storepickup';

                $uploader->save($path, $fname);

                $imagedata['mapicon'] = 'storepickup/'.$fname;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        //upload image contact
	       $imagecontact = array();
        if (!empty($_FILES['pickup_image']['name'])) {
            try {
                $ext = substr($_FILES['pickup_image']['name'], strrpos($_FILES['pickup_image']['name'], '.') + 1);
                $fname = 'Imgae-' . time() . '.' . $ext;
                $uploader = new Varien_File_Uploader('pickup_image');
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png')); // or pdf or anything

                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);

                $path = Mage::getBaseDir('media').DS.'storepickup';

                $uploader->save($path, $fname);

                $imagecontact['pickup_image'] = 'storepickup/'.$fname;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
	
        if ($data = $this->getRequest()->getPost()) {

            if (!empty($imagedata['mapicon'])) {
                $data['mapicon'] = $imagedata['mapicon'];
            } else {
                if (isset($data['mapicon']['delete']) && $data['mapicon']['delete'] == 1) {
                    if ($data['mapicon']['value'] != '') {
                        $_helper = Mage::helper('storepickup');
                        $this->removeFile(Mage::getBaseDir('media').DS.$_helper->updateDirSepereator($data['mapicon']['value']));
                    }
                    $data['mapicon'] = '';
                } else {
                    unset($data['mapicon']);
                }
            }
            //check for image contact upload
            
            if (!empty($imagecontact['pickup_image'])) {
                $data['pickup_image'] = $imagecontact['pickup_image'];
            } else {
                if (isset($data['pickup_image']['delete']) && $data['pickup_image']['delete'] == 1) {
                    if ($data['pickup_image']['value'] != '') {
                        $_helper = Mage::helper('storepickup');
                        $this->removeFile(Mage::getBaseDir('media').DS.$_helper->updateDirSepereator($data['mapicon']['value']));
                    }
                    $data['pickup_image'] = '';
                } else {
                    unset($data['pickup_image']);
                }
            }


            $model = Mage::getModel('storepickup/storepickup');
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();
				
				if ( $this->getRequest()->getParam('id') == null ) { 
					if (isset($data['images_id'])) {
						Mage::helper('storepickup')->saveImageStore($data['images_id'], $model->getCollection()->getLastItem()->getId(), $_FILES, $data['image_group'], $data['img_base']);
					}
				} else {  
					if (isset($data['images_id'])) {
						//Zend_Debug::dump($data['images_id']); 
						Mage::helper('storepickup')->saveImageStore($data['images_id'], $this->getRequest()->getParam('id'), $_FILES, $data['image_group'], $data['img_base']);
					}
				}
				
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('storepickup')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storepickup')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }
     function loadmessagesAction()
    {
          $this->_initAction();
          $this->getResponse()->setBody(
           $this->getLayout()->createBlock('storepickup/adminhtml_storepickup_edit_tab_messages')->toHtml()
        );
    }
     function loadorderAction()
    {
        $this->_initAction();
          $this->getResponse()->setBody(
           $this->getLayout()->createBlock('storepickup/adminhtml_storepickup_edit_tab_gridorders')->toHtml()
        );
    }
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('storepickup/storepickup');

                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $storepickupIds = $this->getRequest()->getParam('storepickup');
        if (!is_array($storepickupIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($storepickupIds as $storepickupId) {
                    $storepickup = Mage::getModel('storepickup/storepickup')->load($storepickupId);
                    $storepickup->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($storepickupIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $storepickupIds = $this->getRequest()->getParam('storepickup');
        if (!is_array($storepickupIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($storepickupIds as $storepickupId) {
                    $storepickup = Mage::getSingleton('storepickup/storepickup')
                            ->load($storepickupId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($storepickupIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName = 'storepickup.csv';
        $content = $this->getLayout()->createBlock('storepickup/adminhtml_storepickup_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'storepickup.xml';
        $content = $this->getLayout()->createBlock('storepickup/adminhtml_storepickup_grid')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
	//Ham de xoa file image trong media/menupro
    protected function removeFile($file) {
        try {
            $io = new Varien_Io_File();
            $result = $io->rmdir($file, true);
        } catch (Exception $e) {

        }
    }

}