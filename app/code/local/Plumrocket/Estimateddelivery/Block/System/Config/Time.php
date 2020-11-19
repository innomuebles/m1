<?php

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.

@package	Plumrocket_Estimated_Delivery_Date-v1.1.x
@copyright	Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
@license	http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/

class Plumrocket_Estimateddelivery_Block_System_Config_Time extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $element->addClass('select');

        $value_hrs = 0;
        $value_min = 0;

        if( $value = $element->getValue() ) {
            $values = explode(',', $value);
            if( is_array($values) && count($values) == 2 ) {
                $value_hrs = $values[0];
                $value_min = $values[1];
            }
        }

        if(!$storeId = $this->getRequest()->getParam('store')) {
            if($websiteId = $this->getRequest()->getParam('website')) {
                $storeId = Mage::app()
                    ->getWebsite($websiteId)
                    ->getDefaultGroup()
                    ->getDefaultStoreId();
            }
        }
        $timestamp = Mage::app()->getLocale()->storeTimeStamp($storeId);
        $is24h = Mage::getSingleton('catalog/product_option_type_date')->is24hTimeFormat();

        $hourLabels = array(
            '12 a.m.', '01 a.m.', '02 a.m.', '03 a.m.', '04 a.m.', '05 a.m.', '06 a.m.', '07 a.m.', '08 a.m.', '09 a.m.', '10 a.m.', '11 a.m.',
            '12 p.m.', '01 p.m.', '02 p.m.', '03 p.m.', '04 p.m.', '05 p.m.', '06 p.m.', '07 p.m.', '08 p.m.', '09 p.m.', '10 p.m.', '11 p.m.'
        );

        $html = '<input type="hidden" id="' . $element->getHtmlId() . '" />';
        $html .= '<select name="'. $element->getName() . '" '.$element->serialize($element->getHtmlAttributes()).' style="width:75px">'."\n";

        for( $i=0;$i<24;$i++ ) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $html.= '<option value="'.$hour.'" '. ( ($value_hrs == $i) ? 'selected="selected"' : '' ) .'>' . ($is24h? $hour : $hourLabels[$i]) . '</option>';
        }
        $html.= '</select>'."\n";

        $html.= '&nbsp;:&nbsp;&nbsp;<select name="'. $element->getName() . '" '.$element->serialize($element->getHtmlAttributes()).' style="width:75px">'."\n";
        for( $i=0;$i<60;$i+=5 ) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $html.= '<option value="'.$hour.'" '. ( ($value_min == $i) ? 'selected="selected"' : '' ) .'>' . $hour . '</option>';
        }
        $html.= '</select>'."\n";

        $html.= $element->getAfterElementHtml();
        return $html;
    }

}