<?php
class MST_Storepickup_Model_Storepickup extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('storepickup/storepickup');
    }
    static public function getTypeArray()
    {
        $arr_status = array(
				array(
                    'value' => 0,
                    'label' => Mage::helper('storepickup')->__('--- Select Option ---'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('storepickup')->__('Top Sellers'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('storepickup')->__('Top Rated'),
                ),
				array(
                    'value' => 3,
                    'label' => Mage::helper('storepickup')->__('Most Viewed'),
                ),
				array(
                    'value' => 4,
                    'label' => Mage::helper('storepickup')->__('Latest Products'),
                ),
            );
        return  $arr_status;
    }
    static public function getOptionArray()
    {
        $arr_status = array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('storepickup')->__('Enabled'),
                ),
                array(                   'value' => 2,
                   'label' => Mage::helper('storepickup')->__('Disabled'),
                ),
            );
        return  $arr_status;
    }
	/*Get all parent menu fill to select box*/
	protected $category_option = array();
	protected $optionsymbol = "";
	public function getChildCategoryCollection($parentId)
    {
		$categories=$this->getCategories();
		$categories->addFieldToFilter("parent_id",$parentId);
    	return $categories;
    }
    public function getCategories()
    {
    	$categories = Mage::getModel('catalog/category')
                    ->getCollection()
                    ->addAttributeToSelect('*')
                    ->addIsActiveFilter();
    	return $categories;
    }
	public function selectRecursiveCategories($parentID)
	{
		$childCollection=$this->getChildCategoryCollection($parentID);
		foreach($childCollection as $value){
			$categoryId=$value->getEntityId();
			//Check this menu has child or not
			$this->optionsymbol=Mage::helper("storepickup")->getCategorySpace($categoryId);
			$this->category_option[$categoryId]=$this->optionsymbol.$value->getName();
			$hasChild=$this->getChildCategoryCollection($categoryId);
			if(count($hasChild)>0)
			{
				$this->selectRecursiveCategories($categoryId);
			}
		}
	}
	public function getCategoryOptions()
	{
		$this->category_option[0]= '--- All Products ---';
		$categories=$this->getCategories();
		foreach ($categories as $value) {
			if($value->getParentId()==1){
				$categoryid=$value->getEntityId();
				$this->category_option[$categoryid]=$value->getName();
				//Check has child menu or not
				$hasChild=$this->getChildCategoryCollection($categoryid);
				if(count($hasChild)>0)
				{
					$this->selectRecursiveCategories($categoryid);
				}
			}
		}
		//array_unshift($this->category_option, array('label' => '--Select category--', 'value' => ''));
		return $this->category_option;
	}
	public function getStateName() {
        if ($this->getState()) {
			return $this->getState();
		} else {  
			$region = Mage::getModel('directory/region')->load( $this->getStateId() );
			return $region->getName();
		}
		return '';
    }
	public function getCountryName() {
        if ($this->getCountry())
            if (!$this->hasData('country_name')) {
                $country = Mage::getModel('directory/country')
                        ->loadByCode($this->getCountry());
                $this->setData('country_name', $country->getName());
            }
        return $this->getData('country_name');
    }	function getStorePickUpByOrderId($orderId)	{		$storepickup = array();		$pickUpOrder = Mage::getModel('storepickup/pickuporder')->getCollection()				->addFieldToFilter('pickup_order_id',$orderId)				->getFirstItem();		if($pickUpOrder->getId())		{			$pickupId = $pickUpOrder->getData('pickup_id');			$objstorepickup = Mage::getModel('storepickup/storepickup')->load($pickupId);			$storepickup['title'] = $objstorepickup->getData('title');			if(trim($pickUpOrder->getTimePickup() != ''))			{				$storepickup['time'] = $pickUpOrder->getTimePickup();			}		}		return $storepickup;
	}
}