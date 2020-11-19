<?php
class MST_Storepickup_Block_Adminhtml_Grid_Renderer_Message extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());
        if(strlen($value) > 100)
        {
            $strCut = substr($value,0,100);
            $str = substr($strCut,0,strrpos($strCut,' ')).'...';
        }
        else
        {
            $str = $value;
        }
        return $str;
    }
}
?>