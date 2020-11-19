<?php

class TM_BotProtection_Model_Blacklist extends TM_BotProtection_Model_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('tm_botprotection/blacklist');
    }

    /**
     * Get response page for blacklisted visitor
     */
    public function getResponsePage()
    {
        $cms_page = Mage::getModel('cms/page');
        if ($this->getData('cms_page_identifier')) {
            // there is custom page for response
            $url_key = $this->getData('cms_page_identifier');
        } else {
            // take response page from extension config
            $url_key = Mage::getStoreConfig(
                TM_BotProtection_Helper_Data::BLACKLIST_RESPONSE_PAGE
            );
        }
        // get cms page id by url key
        $page_id = $cms_page->checkIdentifier(
            $url_key,
            Mage::app()->getStore()->getId()
        );

        if (!$page_id) {
            // CMS page for response not found
            if (file_exists(Mage::getBaseDir('base') . DS . $url_key)) {
                // there is real file - set it as response
                $page = new Varien_Object(array(
                    'id' => -1,
                    'real_file' => true,
                    'url' => Mage::getUrl(null, array('_direct' => $url_key))
                ));
                return $page;
            } else {
                // no file and page not found - get 404 page
                $page_id = Mage::getStoreConfig(
                    Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE
                );
            }
        }

        $cms_page->load($page_id);
        if (Mage::app()->getStore()->isAdmin()) {
            $defaultStoreId = Mage::app()->getWebsite(true)->getDefaultGroup()->getDefaultStoreId();
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($defaultStoreId);
            $cms_page->setUrl(Mage::helper('cms/page')->getPageUrl($cms_page->getId()));
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
        } else {
            $cms_page->setUrl(Mage::helper('cms/page')->getPageUrl($cms_page->getId()));
        }

        return $cms_page;
    }

    public function isRedirectEnabled()
    {
        if ($this->getData('cms_page_identifier')) {
            // there is custom response settings
            return $this->getData('redirect');
        } else {
            // take value from extension config
            return Mage::getStoreConfig(
                TM_BotProtection_Helper_Data::REDIRECT_TO_RESPONSE
            );
        }
    }

}
