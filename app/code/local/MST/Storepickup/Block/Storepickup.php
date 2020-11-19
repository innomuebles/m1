<?php 

class MST_Storepickup_Block_Storepickup extends Mage_Core_Block_Template
{
	public function __construct()
	{
		parent::__construct();
       // $this->setTemplate('storepickup/liststores.phtml');
		$config = Mage::getStoreConfig('storepickup');
		$storepickup = null;
		if($config['setting']['enable'] == 1)
		{   
			$storepickup = Mage::getModel('storepickup/storepickup')->getCollection();
			$storepickup->addFieldToFilter('storepickup_status', 1);
			
			$address = $this->getRequest()->getParam('address');
			$state = $this->getRequest()->getParam('state');
			$city = $this->getRequest()->getParam('city');
			$zipcode = $this->getRequest()->getParam('zipcode');
			$country_code = $this->getRequest()->getParam('country');
			if ($address != "") {
				$address = trim($address);
				$storepickup->addFieldToFilter('address', array('like' => '%' . $address . '%'));
			}
			if ($state != "") {
				$state = trim($state);
				$storepickup->addFieldToFilter('state', array('like' => '%' . $state . '%'));
			}
			if ($city != "") {
				$city = trim($city);
				$storepickup->addFieldToFilter('city', array('like' => '%' . $city . '%'));
			}
			if ($zipcode != "") {
				$zipcode = trim($zipcode);
				$storepickup->addFieldToFilter('zipcode', array('like' => '%' . $zipcode . '%'));
			}
			if ($country_code != "nothing") {
				$country_code = trim($country_code);
				$storepickup->addFieldToFilter('country', array('like' => '%' . $country_code . '%'));
			}
			//echo $this->getRequest()->getParam('country');
			//exit();
		}
		$this->setCollection($storepickup);
				
	}

    public function getStorepickup()
    {
        if (!$this->hasData('storepickup')) {
            $this->setData('storepickup', Mage::registry('storepickup'));
        }
        return $this->getData('storepickup');
    }

    public function getStorepickupList()
    {
        if (!$this->hasData('list')) {
            $this->setData('list', Mage::registry('list'));
        }
        return $this->getData('list');
    }

    function limitCharacter($string, $limit = 20, $suffix = ' . . .')
    {
        $string = strip_tags($string);

        if (strlen($string) < $limit) {
            return $string;
        }

        for ($i = $limit; $i >= 0; $i--) {
            $c = $string[$i];
            if ($c == ' ' OR $c == "\n") {
                return substr($string, 0, $i) . $suffix;
            }
        }

        return substr($string, 0, $limit) . $suffix;
    }
	
    
    function getNav() {
        $storepickup = Mage::getModel('storepickup/cats')->getCollection();
        return $storepickup;
    }
    function getStoretimes()
    {
   	    $storepickup = Mage::getModel('storepickup/storepickup')->getCollection();
	    $storepickup->addFieldToFilter('storepickup_status', 1);
        return $storepickup;
    }
    //function getTime()
//    {
//      return Zend_Json::encode($this->getVariables());
//    }
//    public function getVariables()
//    {
//        $data = array(1=>"1:00",2=>"3");
//        $variables = array($data);
//
//        return $variables;
//    }
}