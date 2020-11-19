<?php
class MST_Storepickup_Block_Adminhtml_Message_Grid_Viewmessage extends Mage_Adminhtml_Block_Widget_Container 
{
 function __construct()
 {
    parent::__construct();
    $this->setTemplate('storepickup/grid/view_messages.phtml');
 }
// function getTabsHtml()
// {
//    return $this->getChildHtml('tabsmessage');
// }    
// protected function _prepareLayout()
// {
//    $this->setChild('tabsmessage',$this->getLayout()->createBlock('storepickup/adminhtml_storepickup_edit_tab_messages','storepickup.grid.messages'));
//    return parent::_prepareLayout();
// }
}