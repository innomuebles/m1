<?php
/**
/**
 * One page checkout status
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magebay Team 
 */
 
class MST_Storepickup_Block_Checkout_Onepage_Progress extends Mage_Checkout_Block_Onepage_Progress
{
  
    public function getShippingDescription()
    {
		$pickups = Mage::getSingleton('checkout/session')->getPickupData();
		$shipping = $this->getQuote()->getShippingAddress()->getShippingDescription();
		if(count($pickups))
		{
			$pickupId = $pickups['pickup_id'];
			$pickup = Mage::getModel('storepickup/storepickup')->load($pickupId);
			$text = '';
			if($pickup->getId())
			{
				if(version_compare(Mage::getVersion(), '1.8.0', '>='))
				{
					$text .= '<br/>'.$this->__('Store Name: ');
					$text .= $pickup->getTitle();
					if(isset($pickups['time_pickup']))
					{
						$text .= '<br/>'.$this->__('Pick Up Time: ');
						$text .= $pickups['time_pickup'];
					}
				
				}
				else
				{
					$text .= ' , '.$this->__('Store Name: ');
					$text .= '  '.$pickup->getTitle();
					if(isset($pickups['time_pickup']))
					{
						$text .= ' , '.$this->__('Pick Up Time: ');
						$text .= '  '.$pickups['time_pickup'];
					}
				}
				$shipping .= $text;
			}
			
		}
        return $shipping;
    }
}
