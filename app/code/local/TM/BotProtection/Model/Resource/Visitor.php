<?php

/**
 * BotProtection extension overrides Mage class to save proper URL and referer
 */
class TM_BotProtection_Model_Resource_Visitor
    extends Mage_Log_Model_Resource_Visitor
{
    /**
     * Saving information about url
     *
     * @param   Mage_Log_Model_Visitor $visitor
     * @return  TM_BotProtection_Model_Resource_Visitor
     */
    protected function _saveUrlInfo($visitor)
    {
        $adapter    = $this->_getWriteAdapter();
        $data       = new Varien_Object(array(
            'url'    => $this->_prepareCurrentUrl(),
            'referer'=> $this->_prepareReferer()
        ));

        $bind = $this->_prepareDataForTable($data, $this->getTable('log/url_info_table'));

        $adapter->insert($this->getTable('log/url_info_table'), $bind);

        $visitor->setLastUrlId($adapter->lastInsertId($this->getTable('log/url_info_table')));

        return $this;
    }

    protected function _prepareCurrentUrl()
    {
        $helperStr = Mage::helper('core/string');
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        if (strlen($currentUrl) > 250) {
            return $helperStr->substr($currentUrl, 0, 247).'...';
        }
        return $helperStr->substr($currentUrl, 0, 250);
    }

    protected function _prepareReferer()
    {
        $helperStr = Mage::helper('core/string');
        $referer = Mage::app()->getRequest()->getServer('HTTP_REFERER');
        if (strlen($referer) > 250) {
            return $helperStr->substr($referer, 0, 247).'...';
        }
        return $helperStr->substr($referer, 0, 250);
    }
}
