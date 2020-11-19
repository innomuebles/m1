<?php

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.

@package    Plumrocket_Estimated_Delivery_Date-v1.1.x
@copyright  Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
@license    http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/

class Plumrocket_Estimateddelivery_Block_System_Config_Renderer_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $html = '';
        $form = new Varien_Data_Form();

        $params = array(
            'name'      => $this->getColumn()->getId(),
            'html_id'   => $this->getColumn()->getId(),
            'value'     => $row->getData($this->getColumn()->getIndex()),
        );

        $dateParams = array_merge($params, array(
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => Mage::helper('estimateddelivery')->getDateTimeFormat(),
            'time'      => false,
        ));

        switch ($this->getColumn()->getData('id')) {
            case 'recurring_date':
                $months = Zend_Locale_Data::getList(Mage::app()->getLocale()->getLocaleCode(), 'months');

                $monthsSelect = new Varien_Data_Form_Element_Select(array_merge($params, array(
                    'name'      => $params['name'] .'[month]',
                    'html_id'   => $params['html_id'] .'[month]',
                    'class'     => 'dateperiod-month',
                    'values'    => $months['format']['wide'],
                    'value'     => !empty($params['value']['month'])? $params['value']['month'] : 1,
                )));

                $html .= $monthsSelect
                    ->setForm($form)
                    ->getElementHtml();
                

                $html .= ' the ';

                $daysSelect = new Varien_Data_Form_Element_Select(array_merge($params, array(
                    'name'      => $params['name'] .'[day]',
                    'html_id'   => $params['html_id'] .'[day]',
                    'class'     => 'dateperiod-day',
                    'values'    => $this->_getDayOptions(),
                    'value'     => !empty($params['value']['day'])? $params['value']['day'] : 1,
                )));

                $html .= $daysSelect
                    ->setForm($form)
                    ->getElementHtml();
                
                break;

            case 'period':
                $date = new Varien_Data_Form_Element_Date(array_merge($dateParams, array(
                    'name'      => $dateParams['name'] .'[start]',
                    'html_id'   => $dateParams['html_id'] .'[start]',
                    'value'     => !empty($dateParams['value']['start'])? $dateParams['value']['start'] : '',
                )));

                $html .= $date
                    ->setForm($form)
                    ->getElementHtml();

                
                $html .= ' - ';

                $dateParams = array_merge($dateParams, array(
                    'name'      => $dateParams['name'] .'[end]',
                    'html_id'   => $dateParams['html_id'] .'[end]',
                    'value'     => !empty($dateParams['value']['end'])? $dateParams['value']['end'] : '',
                ));

                // no break

            case 'single_day':
            default:
                $date = new Varien_Data_Form_Element_Date($dateParams);

                $html .= $date
                    ->setForm($form)
                    ->getElementHtml();
                
                break;
        }

        return $html;
    }

    protected function _getDayOptions()
    {
        $dayOptions = array();
        foreach(range(1, 31) as $num) {
            switch($num) {
                case 1: case 21: case 31: $sfx = 'st'; break;
                case 2: case 22: $sfx = 'nd'; break;
                case 3: case 23: $sfx = 'rd'; break;
                default: $sfx = 'th';
            }

            $dayOptions[$num] = $num . $sfx;
        }

        /*$sfx = ' (or last day)';
        $dayOptions[29] .= $sfx;
        $dayOptions[30] .= $sfx;
        $dayOptions[31] .= $sfx;*/

        return $dayOptions;
    }

}