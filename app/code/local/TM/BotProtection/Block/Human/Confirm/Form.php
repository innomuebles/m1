<?php

class TM_BotProtection_Block_Human_Confirm_Form
    extends Mage_Core_Block_Template
        implements Mage_Widget_Block_Interface
{

    protected function _construct()
    {
        $this->setTemplate('tm/botprotection/human/confirm/form.phtml');
        $this->setFormId('human-confirm');
        return parent::_construct();
    }

    public function renderCaptcha()
    {
        $block = $this->getLayout()->createBlock('captcha/captcha')
            ->setFormId(Mage::helper('tm_botprotection')->getHumanConfirmCaptchaId());
        return $block->toHtml();
    }

    public function getActionUrl()
    {
        return $this->getUrl(
            Mage::helper('tm_botprotection')->getHumanConfirmActionUrlKey(),
            array(
                '_secure'=>(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on')
            )
        );
    }

    public function isCaptchaRequired()
    {
        $captchaModel = Mage::helper('captcha')->getCaptcha(
            Mage::helper('tm_botprotection')->getHumanConfirmCaptchaId()
        );
        return $captchaModel->isRequired();
    }
}
