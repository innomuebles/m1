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

class Plumrocket_Estimateddelivery_Block_System_Config_DatePeriod extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
        $renderDate = Mage::getBlockSingleton('estimateddelivery/system_config_renderer_date');

        $html = '<input type="hidden" name="'. $element->getName() .'" value="" />';

		$html .= $this->getLayout()->createBlock('estimateddelivery/system_config_datePeriod_inputTable')
			->setContainerFieldId($element->getName())
			->setRowKey('name')
			->addColumn('date_type', array(
				'header'    => Mage::helper('estimateddelivery')->__('Period / Date Type'),
				'index'     => 'date_type',
				'type'      => 'select',
                'options'   => array(
                    'recurring_date'    => 'Recurring Date',
                    'single_day'        => 'Single Day',
                    'period'            => 'Period (from-to)',
                ),
                'value'     => 2,
                'column_css_class' => 'dateperiod-type',
			))
            ->addColumn('recurring_date', array(
                'header'    => $this->__('Period / Date'),
                'index'     => 'recurring_date',
                'type'      => 'date',
                'renderer'  => clone $renderDate,
                'column_css_class' => 'dateperiod-recurring-date',
            ))
            ->addColumn('single_day', array(
                'index'     => 'single_day',
                'type'      => 'date',
                'renderer'  => clone $renderDate,
                'width'		=> '0',
                'header_css_class' => 'dateperiod-hide',
                'column_css_class' => 'dateperiod-single-day',
            ))
            ->addColumn('period', array(
                'index'     => 'period',
                'type'      => 'date',
                'renderer'  => clone $renderDate,
                'width'		=> '0',
                'header_css_class' => 'dateperiod-hide',
                'column_css_class' => 'dateperiod-period',
            ))
			->addColumn('remove', array(
                'header'    => Mage::helper('estimateddelivery')->__('Action'),
                'index'     => 'remove',
                'type'      => 'text',
                'renderer'  => 'estimateddelivery/system_config_renderer_button',
                'value'     => 1,
                'column_css_class' => 'remove',
            ))
			->setArray($this->_getValue($element->getValue()))
			->toHtml();

        $html .= $this->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->addData(array(
                'label'   => $this->__('Add Row'),
                'type'    => 'button',
                'class'   => 'add dateperiod-add',
            ))
            ->toHtml();

        return $html;
	}

	/*public function render(Varien_Data_Form_Element_Abstract $element)
    {
    	$html = parent::render($element);
    	$html = str_replace('<td class="value', '<td class="value', $html);
    	// delete last td
    	// $html = str_replace('<td class=""></td>', '', $html);
        
    	return $html;
    }*/

    protected function _getValue($data = array())
    {
        $rows = array(
            '_TMPNAME_' => array(),
        );

        if($data && is_array($data)) {
            /*if(isset($data['value'])) {
                if(is_array($data['value'])) {
                	$rows = array_merge($rows, $data['value']);
            	}
            }else{
                $rows = array_merge($rows, $data);
            }*/
            $rows = array_merge($rows, $data);
        }
        
        foreach ($rows as $name => &$row) {
            $row = array_merge($row, array(
                'name'      => $name,
            ));
        }

        return $rows;
    }

}