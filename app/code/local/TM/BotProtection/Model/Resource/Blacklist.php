<?php

class TM_BotProtection_Model_Resource_Blacklist
    extends TM_BotProtection_Model_Resource_Abstract
{

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('tm_botprotection/blacklist', 'item_id');
    }

    /**
     * Process blacklist rule data before saving
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        // Reset 'redirect' field if 'cms_page_identifier' is empty
        if (!$object->getData('use_custom_page')) {
            $object->setData('cms_page_identifier', null);
        }
        // Reset 'redirect' field if 'cms_page_identifier' is empty
        if (!$object->getData('cms_page_identifier')) {
            $object->setData('redirect', null);
        }
        return parent::_beforeSave($object);
    }

    /**
     * Perform operations after object load
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getItemId()) {
            // check if custom page set
            if ($object->getData('cms_page_identifier')) {
               $object->setData('use_custom_page', 1);
            }
        }
        return parent::_afterLoad($object);
    }
    
}
