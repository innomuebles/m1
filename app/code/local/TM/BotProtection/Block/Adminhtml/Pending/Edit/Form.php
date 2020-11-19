<?php

class TM_BotProtection_Block_Adminhtml_Pending_Edit_Form
    extends TM_BotProtection_Block_Adminhtml_Abstract_List_Edit_Form
{

    protected function _prepareForm()
    {
        $this->_prefix = 'pending_';
        $this->_helper = Mage::helper('tm_botprotection');
        
        // create object form 
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl(
                    '*/*/save',
                    array('id' => $this->getRequest()->getParam('id'))
                ),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );
        $form->setHtmlIdPrefix($this->_prefix);

        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')
            ->toOptionArray();

        // Checking if user have permissions to save information 
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $fieldset = $form->addFieldset(
            'base_fieldset',
            array(
                'legend' => $this->_helper->__('General'),
                'class' => 'fieldset-wide'
            )
        );


        $fieldset->addField('ip_unpacked', 'text', array(
            'name'      => 'ip_unpacked',
            'label'     => $this->_helper->__('IP address'),
            'title'     => $this->_helper->__('IP address'),
            'class'     => 'validate-ip',
            'required'  => true,
            'disabled'  => $isElementDisabled
        ));

        $fieldset->addField('crawler_name', 'text', array(
            'name'      => 'crawler_name',
            'label'     => $this->_helper->__('Crawler/Bot Name'),
            'title'     => $this->_helper->__('Crawler/Bot Name'),
            'after_element_html' => '<div>'.$this->_helper->__('Read more at <a id="ask-about-1">useragentstring.com</a> or <a id="ask-about-2">google.com</a> about this crawler.').'</div>',
            'disabled'  => $isElementDisabled
        ));

        $fieldset->addField('user_agent', 'text', array(
            'name'      => 'user_agent',
            'label'     => $this->_helper->__('User Agent'),
            'title'     => $this->_helper->__('User Agent'),
            'disabled'  => true
        ));

        $fieldset = $form->addFieldset(
            'confirm_fieldset',
            array(
                'legend' => $this->_helper->__('Confirm human'),
                'class' => 'fieldset-wide'
            )
        );

        
        $model = Mage::registry('tm_pending');

        if ($model->getId())
        {
            // edit exisiting item
            // add hidden field with item ID
            $fieldset->addField('item_id', 'hidden', array(
                'name' => 'item_id',
            ));
            // get visitor info for item
            $visitorInfo = $this->_helper->getLastVisit(
                $model->getIp(), $model->getIp(), $model->getCrawlerName()
            );
            // add fieldset for information block
            $this->_prepareFieldsetInformation($form, $visitorInfo);
        }

        $fieldset->addField('ask_confirm_human', 'select', array(
            'name'      => 'ask_confirm_human',
            'label'     => $this->_helper->__('Ask visitor to confirm he is a human'),
            'title'     => $this->_helper->__('Ask visitor to confirm he is a human'),
            'note'      => $this->_helper->__('`Yes` - visitor get page with '
                            .'CAPTCHA and has to solve it.'),
            'values'    => $yesnoSource,
            'disabled'  => $isElementDisabled
        ));

        $fieldset->addField('note101', 'note', array(
                'label'     => $this->_helper->__('Human confirmed?'),
                'text'      => $model->getConfirmedHuman() ? $this->__('Yes') : $this->__('No')
            ));

        $fieldset->addField('note102', 'note', array(
                'label'     => $this->_helper->__('Failed confirmation attempts'),
                'text'      => $model->getFailedAttempts()
            ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        /* Append dependency javascript */
        $this->setChild('form_after', $this->getLayout()
            ->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap($this->_prefix.'ask_confirm_human', 'ask_confirm_human')
                ->addFieldMap($this->_prefix.'note101', 'note101')
                ->addFieldMap($this->_prefix.'note102', 'note102')
                // show notes if 'Ask to confirm' - Yes
                ->addFieldDependence('note101', 'ask_confirm_human', 1)
                ->addFieldDependence('note102', 'ask_confirm_human', 1)
            );

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
            ->isAllowed('templates_master/tm_botprotection/pending/' . $action);
    }

}
