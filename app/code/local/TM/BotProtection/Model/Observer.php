<?php

class TM_BotProtection_Model_Observer
{

    protected $_helper = null;

    public function __construct()
    {
        $this->_helper = Mage::helper('tm_botprotection');
    }

    public function checkVisitor($observer, $recursionCall = true)
    {
        $request = $observer->getControllerAction()->getRequest();
        if ($this->_skipPredispatch($request)) {
            return;
        }

        $visitorIpPacked = Mage::helper('core/http')->getRemoteAddr(true);
        $crawlerName = $this->_helper->getCrawlerName();

        //  CHECK BLACKLIST
        $listItem = Mage::getModel('tm_botprotection/blacklist')
            ->findItem($visitorIpPacked, $crawlerName);
        if (!$listItem->getId()) {
            //  CHECK PENDING LIST
            $listItem = Mage::getModel('tm_botprotection/pending')
                ->findItem($visitorIpPacked, $crawlerName);
        }

        if ($listItem->getId()) {
            // visitor is in blacklist or in pending list
            if ($responsePage = $listItem->getResponsePage()) {
                if ($request->getParam('page_id')) {
                    $currentPage = Mage::getModel('cms/page')
                        ->load($request->getParam('page_id'));
                    if ($currentPage->getIdentifier() == $responsePage->getIdentifier()){
                        // it is a response page, do not redirect
                        // set HTTP code for response page
                        $observer->getControllerAction()->getResponse()
                            ->setHeader('HTTP/1.0', 403, true);
                        return;
                    }
                }
                if ($responsePage->getId()) {
                    $this->_forwardToPage(
                        $observer,
                        $responsePage,
                        $listItem->isRedirectEnabled()
                    );
                    return; // leave method
                } else {
                    // failed to identify response page
                }
            }
        } elseif ($this->_helper->getConfigDetectEnabled()) {
            // detect bot/crawler if config enabled
            $detectedBy = '';
            if ($crawlerName) {
                // 1. check User Agent is form any crawler
                $detectedBy = 'USER_AGENT';
            } elseif ($request->getParam($this->_helper->getHoneypotElementName())) {
                // 2. check honeypot input field has any value
                $detectedBy = 'FORM_HONEYPOT';
            } elseif ($this->_isClickLimitReached($visitorIpPacked)) {
                // 3. check is too many clicks
                $detectedBy = 'CLICK_MAX_REACHED';
            }

            if ($detectedBy) {
                // visitor is crawler
                $this->_punishBotVisitor($visitorIpPacked, $crawlerName, null, $detectedBy);
                if ($recursionCall) {
                    $this->checkVisitor($observer, false); // FALSE - prevent infinit loop
                }
            }
        }
    }

    public function updateHtmlBeforeOutput($observer)
    {
        $hideFormActions = $this->_helper->hideFormActionsEnabled();
        $placeHoneypot = $this->_helper->honeypotFormEnabled();
        if (!($hideFormActions || $placeHoneypot)) {
            return;
        }
        $response = $observer->getResponse();
        if (!$response->getBody()) {
            return;
        }

        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        // silence warnings and erros during parsing.
        // More at http://php.net/manual/en/function.libxml-use-internal-errors.php
        $oldUseErrors = libxml_use_internal_errors(true);
        $dom->loadHTML($response->getBody());
        // restore old value
        libxml_use_internal_errors($oldUseErrors);

        $nodes = $dom->getElementsByTagName('form');
        foreach ($nodes as $node) {
            if ($node->hasAttribute('action') && $hideFormActions) {
                $node->setAttribute(
                    'data-perform',
                    bin2hex($node->getAttribute('action'))
                );
                $node->removeAttribute('action');
            }
            if ($placeHoneypot) {
                $honeypotElement = $dom->createElement('input');
                $honeypotElement->setAttribute('name', $this->_helper->getHoneypotElementName());
                $honeypotElement->setAttribute('class', 'field-to-fill');
                $node->appendChild($honeypotElement);
            }
        }
        $response->setBody($dom->saveHTML());
    }

    /**
     * Forward/redirect action to other controller/URL
     * NOTICE: force REDIRECT not forward if:
     * - target URL is real file
     * - request is for magento backend
     */
    protected function _forwardToPage($observer, $page, $redirect = false)
    {
        $isAdmin = $this->_helper->isAdmin();
        if ($redirect || $page->getRealFile() || $isAdmin) {
            $controller = $observer->getControllerAction();
            $controller->setFlag(
                '',
                Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH,
                true
            );
            $controller->getResponse()->setRedirect($page->getUrl())->sendResponse();
        } else {
            $request = $observer->getControllerAction()->getRequest();
            $request->initForward();
            $request->setModuleName('cms')
                ->setControllerName('page')
                ->setActionName('view')
                ->setParam('page_id', $page->getId());
            $request->setDispatched(false);
        }
    }

    protected function _isClickLimitReached($visitorIp)
    {
        $seconds = $this->_helper->getConfigDetectTimeframe();
        $maxClicks = $this->_helper->getConfigDetectClickmax();
        if ($seconds && $maxClicks) {
            $startTime = Mage::app()->getLocale()->date(null, null, null, false);
            $startTime->sub($seconds, Zend_Date::SECOND);
            $endTime = Mage::app()->getLocale()->date(null, null, null, false);
            $urlCollection = Mage::getModel('tm_botprotection/url')
                ->getCollection()
                ->joinUrlInfo(null)
                ->joinVisitor(null)
                ->joinVisitorInfo(null);
            $urlCollection->addVisitTimeFilter($startTime, $endTime);
            $urlCollection->addVisitorIpFilter($visitorIp);
            $urlCollection->addStoreFilter(Mage::app()->getStore()->getStoreId());
            if ($urlCollection->getSize() >= $maxClicks) {
                // visitor is bot
                return true;
            }
        }
        return false;
    }

    /*
     * Visitor detected as bot; add him to blacklist/pending list
     */
    protected function _punishBotVisitor($ipPacked, $crawler, $userAgent = null, $detectedBy)
    {
        $data = array();
        $data['ip_unpacked'] = $this->_helper->unpackIp($ipPacked);
        $data['crawler_name'] = $crawler;
        $data['status'] = 1;
        $data['user_agent'] = $userAgent;
        if (is_null($data['user_agent'])) {
            $data['user_agent'] = Mage::helper('core/http')->getHttpUserAgent();
        }
        $data['detected_by'] = $this->_helper->getDetectedByValue($detectedBy);

        $actionModel = Mage::getModel('tm_botprotection/system_config_source_defaultaction');
        switch ($this->_helper->getConfigDetectAction()) {
            case $actionModel->getConstActionBlacklist():
                $model = Mage::getModel('tm_botprotection/blacklist');
                break;
            case $actionModel->getConstActionCaptcha():
                $model = Mage::getModel('tm_botprotection/pending');
                $data['ask_confirm_human'] = 1;
                break;
            case $actionModel->getConstActionPending():
                $model = Mage::getModel('tm_botprotection/pending');
                break;
        }

        $model->setData($data);
        try {
            $model->save();
        }
        catch (Exception $e) {
            // An error occurred while saving
        }
    }

    /*
     *  Check if this request can be skipped by Bot Protection module
     *  @return boolean (true - skip predispatch)
     */
    protected function _skipPredispatch($request)
    {
        // check if extension enabled
        if (!$this->_helper->getConfigModuleEnabled()) {
            return true;
        }

        //get the admin session
        Mage::getSingleton('core/session', array('name'=>'adminhtml'));
        //verify if the user is logged in to the backend
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            return true;
        }

        // check URL from skip list
        $urlKey = implode('/', array(
                $request->getModuleName(),
                $request->getControllerName(),
                $request->getActionName()
            ));
        if (in_array($urlKey, $this->_helper->getUrlsToSkip())) {
            return true;
        }

        // check visitor is whitelisted
        $visitorIpPacked = Mage::helper('core/http')->getRemoteAddr(true);
        $crawlerName = $this->_helper->getCrawlerName();
        $whiteItem = Mage::getModel('tm_botprotection/whitelist')
            ->findItem($visitorIpPacked, $crawlerName);
        if ($whiteItem->getId()) {
            return true;
        }

        // request can not be skipped
        return false;
    }
}
