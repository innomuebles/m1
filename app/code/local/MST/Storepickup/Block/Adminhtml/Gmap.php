<?php 
class MST_Storepickup_Block_Adminhtml_Gmap extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface {
	
        
    protected $_element;
	
    /**
     * constructor
    */
    public function __construct(){

            $this->setTemplate('storepickup/gmap.phtml');
    }

    /*
     * renderer
    */
    public function render(Varien_Data_Form_Element_Abstract $element){
            $this->setElement($element);
            return $this->toHtml();
    }

    /**
     * get and set element
    */
    public function setElement(Varien_Data_Form_Element_Abstract $element){
            $this->_element = $element;
            return $this;
    }
    public function getElement(){
            return $this->_element;
    }
        	
}