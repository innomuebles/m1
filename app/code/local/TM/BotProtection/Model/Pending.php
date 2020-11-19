<?php

class TM_BotProtection_Model_Pending extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('tm_botprotection/pending');
    }

    /**
     * Find item in pending list for IP and Crawler
     */
    public function findItem($ipPacked, $crawlerName)
    {
        $listCollection = $this->getCollection();
        $listCollection->addIpFilter($ipPacked);
        $listCollection->getSelect()
            ->where(
                ' crawler_name = ? '
                .'OR '
                .'crawler_name IS NULL '
                ,
                $crawlerName
            );
        $listCollection->getSelect()
            ->order('crawler_name DESC');
        $listCollection->getSelect()
            ->order('ip DESC');
        return $listCollection->getFirstItem();
    }

    /**
     * Get response page for visitor from pending list
     */
    public function getResponsePage()
    {
        if (!$this->getAskConfirmHuman() || $this->getConfirmedHuman()) {
            return false;
        }

        $cms_page = Mage::getModel('cms/page');
        // take response page from extension config
        $url_key = Mage::getStoreConfig(
            TM_BotProtection_Helper_Data::CONFIRMHUMAN_RESPONSE_PAGE
        );
        // get cms page id by url key
        $page_id = $cms_page->checkIdentifier(
            $url_key,
            Mage::app()->getStore()->getId()
        );

        if (!$page_id) {
            // no file and page not found - get 404 page
            $page_id = Mage::getStoreConfig(
                Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE
            );
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
        return Mage::getStoreConfig(
            TM_BotProtection_Helper_Data::REDIRECT_TO_RESPONSE
        );
    }

}
