<?php
/**
 * Used in creating options for default action with visitor identified as bot
 *
 */
class TM_BotProtection_Model_System_Config_Source_Defaultaction
{

    const ACTION_ADD_TO_BLACKLIST = 1;
    const ACTION_ADD_TO_PENDING = 3;
    const ACTION_SHOW_CAPTCHA = 2;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::ACTION_ADD_TO_BLACKLIST,
                'label'=> Mage::helper('tm_botprotection')->__('Add to Blacklist')
            ),
            array(
                'value' => self::ACTION_SHOW_CAPTCHA,
                'label'=> Mage::helper('tm_botprotection')->__('Confirm Human (CAPTCHA)')
            ),
            array(
                'value' => self::ACTION_ADD_TO_PENDING,
                'label'=> Mage::helper('tm_botprotection')->__('Add to Pending List')
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
            self::ACTION_ADD_TO_BLACKLIST
                => Mage::helper('tm_botprotection')->__('Add to Blacklist'),
            self::ACTION_SHOW_CAPTCHA
                => Mage::helper('tm_botprotection')->__('Confirm human (CAPTCHA)'),
            self::ACTION_ADD_TO_PENDING
                => Mage::helper('tm_botprotection')->__('Add to Pending List')
        );
    }

    public function getConstActionBlacklist()
    {
        return self::ACTION_ADD_TO_BLACKLIST;
    }

    public function getConstActionCaptcha()
    {
        return self::ACTION_SHOW_CAPTCHA;
    }

    public function getConstActionPending()
    {
        return self::ACTION_ADD_TO_PENDING;
    }

}
