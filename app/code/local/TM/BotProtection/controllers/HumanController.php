<?php

class TM_BotProtection_HumanController extends Mage_Core_Controller_Front_Action
{
    /**
     * Validate if visitor is human
     *
     */
    public function verifyAction()
    {
        $botHelper = Mage::helper('tm_botprotection');

        $visitorIpPacked = Mage::helper('core/http')->getRemoteAddr(true);
        $crawlerName = $botHelper->getCrawlerName();
        $pendingItem = Mage::getModel('tm_botprotection/pending')
            ->findItem($visitorIpPacked, $crawlerName);
        if (!$pendingItem->getId()) {
            $this->_redirect();
            return;
        }

        $captchaId = $botHelper->getHumanConfirmCaptchaId();
        $captchaModel = Mage::helper('captcha')->getCaptcha($captchaId);
        $word = $this->_getCaptchaString($this->getRequest(), $captchaId);
        if ($captchaModel->isCorrect($word)) {
            $refererUrl = Mage::app()->getRequest()->getServer('HTTP_REFERER');
            $responsePageUrl = $pendingItem->getResponsePage()->getUrl();
            $pendingItem->setConfirmedHuman(1);
            Mage::getSingleton('core/session')
                ->addSuccess($botHelper->__('Please, continue shopping.'));
            if (strpos($responsePageUrl, $refererUrl) === false) {
                $this->_redirectReferer();
            } else {
                $this->_redirect('');
            }
        } else {
            $pendingItem->setFailedAttempts($pendingItem->getFailedAttempts()+1);
            Mage::getSingleton('core/session')
                ->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
            $this->_redirectReferer();
        }
        $pendingItem->save();
    }

    /**
     * Get Captcha String
     *
     * @param Varien_Object $request
     * @param string $formId
     * @return string
     */
    protected function _getCaptchaString($request, $formId)
    {
        $captchaParams = $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
        return $captchaParams[$formId];
    }
}