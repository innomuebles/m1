<?php 
class MST_Storepickup_Block_Adminhtml_Grid_Renderer_Images extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface
{
	protected $_element;
	
	public function __construct(){
		$this->setTemplate('storepickup/images.phtml');
	}
	
	public function render(Varien_Data_Form_Element_Abstract $element){
		$this->setElement($element);
		return $this->toHtml();
	}
	
	public function getValues($id){
		return Mage::getModel('storepickup/image')->load($id);		
	}
	
	public function setElement(Varien_Data_Form_Element_Abstract $element){
		$this->_element = $element;
		return $this;
	}
	public function getElement(){
		return $this->_element;
	}

}