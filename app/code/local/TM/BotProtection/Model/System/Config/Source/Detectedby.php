<?php
/**
 * Used in creating options for default action with visitor identified as bot
 *
 */
class TM_BotProtection_Model_System_Config_Source_Detectedby
{

    const MANUALLY = 10;
    const FORM_HONEYPOT = 20;
    const USER_AGENT = 30;
    const CLICK_MAX_REACHED = 40;
    const VIA_SUSPICIOUS_LIST = 50;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::MANUALLY,
                'label'=> Mage::helper('tm_botprotection')->__('Manually')
            ),
            array(
                'value' => self::FORM_HONEYPOT,
                'label'=> Mage::helper('tm_botprotection')->__('Form honeypot')
            ),
            array(
                'value' => self::USER_AGENT,
                'label'=> Mage::helper('tm_botprotection')->__('User Agent')
            ),
            array(
                'value' => self::CLICK_MAX_REACHED,
                'label'=> Mage::helper('tm_botprotection')->__('Clicks max reached')
            ),
            array(
                'value' => self::VIA_SUSPICIOUS_LIST,
                'label'=> Mage::helper('tm_botprotection')->__('Via suspicious list')
            )
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            self::MANUALLY
                => Mage::helper('tm_botprotection')->__('Manually'),
            self::FORM_HONEYPOT
                => Mage::helper('tm_botprotection')->__('Form honeypot'),
            self::USER_AGENT
                => Mage::helper('tm_botprotection')->__('User Agent'),
            self::CLICK_MAX_REACHED
                => Mage::helper('tm_botprotection')->__('Clicks max reached'),
            self::VIA_SUSPICIOUS_LIST
                => Mage::helper('tm_botprotection')->__('Via suspicious list')
        );
    }

}
