<?php

abstract class TM_BotProtection_Block_Adminhtml_Abstract_List_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form
{

    // set prefix for fields in form
    protected $_prefix = '';

    protected $_helper = null;


    protected function _prepareFieldsetGeneral($form)
    {
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

        $fieldset->addField('is_ip_range', 'select', array(
            'name'      => 'is_ip_range',
            'label'     => $this->_helper->__('Range'),
            'title'     => $this->_helper->__('Range'),
            'values'    => $yesnoSource,
            'disabled'  => $isElementDisabled
        ));

        $fieldset->addField('ip_unpacked', 'text', array(
            'name'      => 'ip_unpacked',
            'label'     => $this->_helper->__('IP address'),
            'title'     => $this->_helper->__('IP address'),
            'class'     => 'validate-ip',
            'required'  => true,
            'disabled'  => $isElementDisabled
        ));

        $fieldset->addField('ip_from_unpacked', 'text', array(
            'name'      => 'ip_from_unpacked',
            'label'     => $this->_helper->__('IP address from'),
            'title'     => $this->_helper->__('IP address from'),
            'class'     => 'validate-ip',
            'required'  => true,
            'disabled'  => $isElementDisabled
        ));

        $fieldset->addField('ip_to_unpacked', 'text', array(
            'name'      => 'ip_to_unpacked',
            'label'     => $this->_helper->__('IP address to'),
            'title'     => $this->_helper->__('IP address to'),
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

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('cms')->__('Status'),
            'title'     => Mage::helper('cms')->__('Status'),
            'name'      => 'status',
            'options'   => Mage::getSingleton('cms/page')->getAvailableStatuses(),
            'disabled'  => $isElementDisabled
        ));

        return $fieldset;
    }

    protected function _prepareFieldsetAdditional($form)
    {
        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')
            ->toOptionArray();

        // Checking if user have permissions to save information 
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $fieldset = $form->addFieldset(
            'add_fieldset',
            array(
                'legend' => $this->_helper->__('Additional'),
                'class' => 'fieldset-wide'
            )
        );

        $fieldset->addField('use_custom_page', 'select', array(
            'name'      => 'use_custom_page',
            'label'     => $this->_helper->__('Show custom response page'),
            'title'     => $this->_helper->__('Show custom response page'),
            'values'    => $yesnoSource,
            'disabled'  => $isElementDisabled
        ));

        $fieldset->addField('cms_page_identifier', 'text', array(
            'name'      => 'cms_page_identifier',
            'label'     => $this->_helper->__('Response page URL Key'),
            'title'     => $this->_helper->__('Response page URL Key'),
            'class'     => 'validate-identifier',
            'note'      => Mage::helper('cms')->__('Relative to Website Base URL'),
            'disabled'  => $isElementDisabled
        ));

        $fieldset->addField('redirect', 'select', array(
            'name'      => 'redirect',
            'label'     => $this->_helper->__('Redirect to response page'),
            'title'     => $this->_helper->__('Redirect to response page'),
            'values'    => $yesnoSource,
            'note'      => $this->_helper->__('`No` - forwards the request to a'
                                .' different page but keeps the URL the same so'
                                .' the browser does not know any difference. '
                                .'`Yes` - creates a new HTTP Request and goes '
                                .'to specified URL.'),
            'disabled'  => $isElementDisabled
        ));

        return $fieldset;
    }

    protected function _prepareFieldsetInformation($form, $visitorInfo)
    {
        // Checking if user have permissions to save information 
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $fieldset = $form->addFieldset(
            'info_fieldset',
            array(
                'legend' => $this->_helper->__('Information'),
                'class' => 'fieldset-wide'
            )
        );

        if ($visitorInfo) {
            $fieldset->addField('note1', 'note', array(
                'label'     => $this->_helper->__('Last time seen at'),
                'text'      => Mage::app()->getLocale()->date(
                                $visitorInfo->getLastVisitAt(),
                                Varien_Date::DATETIME_INTERNAL_FORMAT
                            )
            ));
            $fieldset->addField('note2', 'note', array(
                'label'     => $this->_helper->__('From IP'),
                'text'      => $this->_helper->unpackIp($visitorInfo->getRemoteAddr())
            ));
        } else {
            $fieldset->addField('note1', 'note', array(
                'text'      => $this->_helper->__('No information found...'),
            ));
        }
    }

    protected function _appendDependencyJavascript()
    {
        /* Append dependency javascript */
        $this->setChild('form_after', $this->getLayout()
            ->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap($this->_prefix . 'is_ip_range', 'is_ip_range')
                ->addFieldMap($this->_prefix . 'ip_from_unpacked', 'ip_from')
                ->addFieldMap($this->_prefix . 'ip_to_unpacked', 'ip_to')
                ->addFieldMap($this->_prefix . 'ip_unpacked', 'ip')
                ->addFieldMap($this->_prefix . 'use_custom_page', 'use_custom_page')
                ->addFieldMap($this->_prefix . 'cms_page_identifier', 'cms_id')
                ->addFieldMap($this->_prefix . 'redirect', 'redirect')
                // show IP range if 'Set IP range' - Yes
                ->addFieldDependence('ip_from', 'is_ip_range', 1)
                ->addFieldDependence('ip_to', 'is_ip_range', 1)
                // show IP range if 'Set IP range' - No
                ->addFieldDependence('ip', 'is_ip_range', 0)
                // show Page URL if 'Show custom response' - Yes
                ->addFieldDependence('cms_id', 'use_custom_page', 1)
                ->addFieldDependence('redirect', 'use_custom_page', 1)
            );
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        // method has to be implemented in child class
    }

}
