<?php

class TM_BotProtection_Block_Adminhtml_Whitelist_Edit_Form
    extends TM_BotProtection_Block_Adminhtml_Abstract_List_Edit_Form
{

    protected function _prepareForm()
    {
        $this->_prefix = 'whitelist_';
        $this->_helper = Mage::helper('tm_botprotection');
        
        // create object form 
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save',
                        array('id' => $this->getRequest()->getParam('id'))
                    ),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );
        $form->setHtmlIdPrefix($this->_prefix);
        
        // fieldset for general section
        $fieldset = $this->_prepareFieldsetGeneral($form);

        $model = Mage::registry('tm_whitelist');

        if (!$model->getItemId()) {
            $model->setData('status', '1');
        } else {
            // edit exisiting item
            // add hidden field with item ID
            $fieldset->addField('item_id', 'hidden', array(
                'name' => 'item_id',
            ));
            // get visitor info for item
            $visitorInfo = $this->_helper->getLastVisit(
                $model->getIpFrom(), $model->getIpTo(), $model->getCrawlerName()
            );
            // add fieldset for information block
            $this->_prepareFieldsetInformation($form, $visitorInfo);
        }

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        $this->_appendDependencyJavascript();

        return parent::_prepareForm();
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
            ->isAllowed('templates_master/tm_botprotection/' . $action);
    }

}
