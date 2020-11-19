<?php
class MST_Storepickup_Block_Adminhtml_Storepickup_Grid_Gridorders extends Mage_Adminhtml_Block_Widget_Container 
{
 function __construct()
 {
    parent::__construct();
    $this->setTemplate('storepickup/grid/gridorders.phtml');
 }
 function getTabsHtml()
 {
    return $this->getChildHtml('tabsorders');
 }    
 protected function _prepareLayout()
 {
    $this->setChild('tabsorders',$this->getLayout()->createBlock('storepickup/adminhtml_storepickup_edit_tab_gridorders','storepickup.grid.gridorders'));
    return parent::_prepareLayout();
 }
}