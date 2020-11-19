<?php

class TM_BotProtection_Helper_Data extends Mage_Core_Helper_Abstract
{

    const BLACKLIST_RESPONSE_PAGE = 'tm_botprotection/response/blacklisted';
    const CONFIRMHUMAN_RESPONSE_PAGE = 'tm_botprotection/response/confirm_human';
    const REDIRECT_TO_RESPONSE = 'tm_botprotection/response/redirect';

    const VISITOR_INFO_TABLE = 'log/visitor_info';

    /**
     * Available values for 'Detected by' field
     * @var array
     */
    protected $_detectedBy = array();

    protected function _getDbTable($table)
    {
        return Mage::getSingleton('core/resource')->getTableName($table);
    }

    public function getCrawlerName($userAgent = null)
    {
        $crawlerName = '';
        $crawlerDetect = new Jaybizzle_CrawlerDetect();
        if ($crawlerDetect->isCrawler($userAgent)) {
            $crawlerName = $crawlerDetect->getMatches();
        }
        if ($crawlerName) {
            if ($crawlerName == 'Slurp') {
                $crawlerName = 'Yahoo! ' . $crawlerName;
            }
            return $crawlerName;
        }
        return false;
    }

    /**
     *  Get information about last visit from current visitor
     */
    public function getLastVisit($ipFrom, $ipTo, $crawlerName = null)
    {
        $visitorCollection = Mage::getModel('log/visitor')->getCollection();
        $visitorCollection->getSelect()
            ->join(
                array('vinfo' => $this->_getDbTable(self::VISITOR_INFO_TABLE)),
                'vinfo.visitor_id = main_table.visitor_id',
                array('remote_addr', 'http_user_agent')
            )
            ->order('main_table.last_visit_at DESC');
        // $visitorCollection->addFilterToMap('http_user_agent', 'vinfo.http_user_agent');
        // $visitorCollection->addFieldToSelect('vinfo.http_user_agent', 'http_user_agent');
        $visitorCollection->getSelect()
            ->where(
                'vinfo.remote_addr BETWEEN 0x'
                .(empty($ipFrom) ? '00000000' : bin2hex($ipFrom))
                .' AND 0x'
                .(empty($ipTo) ? '00000000' : bin2hex($ipTo))
            );
        if (!empty($crawlerName)) {
            $visitorCollection->getSelect()
                ->where('vinfo.http_user_agent LIKE "%'.$crawlerName.'%"');
        }
        $visitor = $visitorCollection->getFirstItem();
        return $visitor->getId() ? $visitor : false;
    }

    public function unpackIp($ip_packed)
    {
        if (empty($ip_packed)) {
            return '';
        }
        return inet_ntop($ip_packed);
    }

    public function packIp($ip_unpacked)
    {
        return inet_pton($ip_unpacked);
    }

    public function getConfigDetectTimeframe()
    {
        return (int)Mage::getStoreConfig('tm_botprotection/detect/seconds');
    }

    public function getConfigDetectClickmax()
    {
        return (int)Mage::getStoreConfig('tm_botprotection/detect/clicks_max');
    }

    public function getConfigDetectAction()
    {
        return Mage::getStoreConfig('tm_botprotection/detect/action');
    }

    public function getHumanConfirmCaptchaId()
    {
        return 'human_confirm';
    }

    public function getHumanConfirmActionUrlKey()
    {
        return 'botprotection/human/verify';
    }

    public function getUrlsToSkip()
    {
        return array(
                $this->getHumanConfirmActionUrlKey(),
                'captcha/refresh/index'
            );
    }

    public function getConfigModuleEnabled()
    {
        return Mage::getStoreConfig('tm_botprotection/general/enabled');
    }

    public function getConfigDetectEnabled()
    {
        return Mage::getStoreConfig('tm_botprotection/detect/enabled');
    }

    public function getConfigProtectFormsEnabled()
    {
        return Mage::getStoreConfig('tm_botprotection/general/protect_forms');
    }

    public function getConfigHoneypotFormsEnabled()
    {
        return Mage::getStoreConfig('tm_botprotection/detect/honeypot_forms');
    }

    public function hideFormActionsEnabled()
    {
        // no if extension disabled
        if (!$this->getConfigModuleEnabled()) {
            return false;
        }
        // no if protect forms disabled in config
        if (!$this->getConfigProtectFormsEnabled()) {
            return false;
        }
        // no if it is ajax request
        if (Mage::app()->getRequest()->isXmlHttpRequest()) {
            return false;
        }
        // no if backend
        if ($this->isAdmin()) {
            return false;
        }
        return true;
    }

    public function honeypotFormEnabled()
    {
        // no if extension disabled
        if (!$this->getConfigModuleEnabled()) {
            return false;
        }
        // no if honey pot for forms disabled
        if (!$this->getConfigHoneypotFormsEnabled()) {
            return false;
        }
        // no if it is ajax request
        if (Mage::app()->getRequest()->isXmlHttpRequest()) {
            return false;
        }
        // no if backend
        if ($this->isAdmin()) {
            return false;
        }
        return true;
    }

    public function isAdmin()
    {
        if(Mage::app()->getStore()->isAdmin())
        {
            return true;
        }
        if(Mage::getDesign()->getArea() == 'adminhtml')
        {
            return true;
        }
        return false;
    }

    public function getHoneypotElementName()
    {
        return 'field_to_fill';
    }

    public function getDetectedByValue($constantName)
    {
        if (!count($this->_detectedBy)) {
            $className = 'TM_BotProtection_Model_System_Config_Source_Detectedby';
            $obj = new ReflectionClass($className);
            $this->_detectedBy = $obj->getConstants();
        }
        return $this->_detectedBy[$constantName];
    }

}
