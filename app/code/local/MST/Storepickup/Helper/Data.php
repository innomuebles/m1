<?php 

/**
 * PACKT Storepickup Data Helper
 *
 * @category   PACKT
 * @package    MST_Storepickup
 */
class MST_Storepickup_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected static $egridImgDir = null;
    protected static $egridImgURL = null;
    protected static $egridImgThumb = null;
    protected static $egridImgThumbWidth = null;
    protected $_allowedExtensions = Array();

    public function __construct() {
        self::$egridImgDir = Mage::getBaseDir('media') . DS;
        self::$egridImgURL = Mage::getBaseUrl('media');
        self::$egridImgThumb = "thumb/";
        self::$egridImgThumbWidth = 40;
    }
	

	public function updateDirSepereator($path){
        return str_replace('\\', DS, $path);
    }
	
	public function getImageUrl($image_file) {
        $url = false;
        if (file_exists(self::$egridImgDir . self::$egridImgThumb . $this->updateDirSepereator($image_file)))
            $url = self::$egridImgURL . self::$egridImgThumb . $image_file;
        else
            $url = self::$egridImgURL . $image_file;
        return $url;
    }

    public function getFileExists($image_file) {
        $file_exists = false;
        $file_exists = file_exists(self::$egridImgDir . $this->updateDirSepereator($image_file));
        return $file_exists;
    }

    public function getImageThumbSize($image_file) {
        $img_file = $this->updateDirSepereator(self::$egridImgDir . $image_file);
        if ($image_file == '' || !file_exists($img_file))
            return false;
        list($width, $height, $type, $attr) = getimagesize($img_file);
        $a_height = (int) ((self::$egridImgThumbWidth / $width) * $height);
        return Array('width' => self::$egridImgThumbWidth, 'height' => $a_height);
    }

    function deleteFiles($image_file) {
        $pass = true;
        if (!unlink(self::$egridImgDir . $image_file))
            $pass = false;
        if (!unlink(self::$egridImgDir . self::$egridImgThumb . $image_file))
            $pass = false;
        return $pass;
    }
	
	public function getCategorySpace($categoryid)
	{
		$path = Mage::getModel('catalog/category')->load($categoryid)->getPath();
		$space="";
		$num=explode("/", $path);
		for($i=1; $i<count($num);$i++)
		{
			$space=$space."---";
		}
		return $space;
	}
	
	
	public function getOptionCountry() {
        $optionCountry = array();
        $collection = Mage::getResourceModel('directory/country_collection')
                ->loadByStore();
        if (count($collection)) {
            foreach ($collection as $item) {
                $optionCountry[] = array('value' => $item->getId(), 'label' => $item->getName());
            }
        }

        return $optionCountry;
    }
	
	public function getListCountry() {
        $listCountry = array();

        $collection = Mage::getResourceModel('directory/country_collection')
                ->loadByStore();

        if (count($collection)) {
            foreach ($collection as $item) {
                $listCountry[$item->getId()] = $item->getName();
            }
        }
        return $listCountry;
    }
	function getCountryByCode($code)
	{
		$text = '';
		$listCountry = $this->getListCountry();
		if(count($listCountry))
		{
			foreach($listCountry as $k => $country)
			{
				if($k == $code)
				{
					$text = $country;
				}
			}
		}
		return $text;
	}
	
	// storepickup
	public function getLocation()
    {
		$id = $this->getRequest()->getParam('id');
		if(!$id) return null;
		$store = Mage::getModel('storepickup/storepickup')->load($id);
		
       if($store){
            $location['latitude'] = $store->getLatitude();
            $location['longtitude'] = $store->getLongtitude();
            $location['zoom_level'] = $store->getZoom_level(); 
            
            return $location;
       }else{
           return null;
       }
    }
	
	public function getDataImage($id) {
        $collection = Mage::getModel('storepickup/image')->getCollection()->addFilter('storepickup_id', $id);
        return $collection;
    }
	

	
	public function getImageUrlJS() {
        $url = Mage::getBaseUrl('media') . 'storepickup/images/';
        return $url;
    }
	
	public function saveImageStore($images, $id, $file, $imageGroup, $imageCheckbox ) 
	{
        foreach ($images as $item) {
			$option = $item['options'];
            $mod = Mage::getModel('storepickup/image');
            $file_name = $file["images_id" . $item['options']]['name'];
			
            $name_image = $this->renameImage($file_name, $id, $item['options']);
            if ($item['delete'] == 0) {
                $last = $mod->getCollection()->getLastItem()->getData('options') + 1;
                $mod->setData('storepickup_id', $id);
                if (($name_image != "") && isset($name_image) != NULL) {
                    $mod->setData('name', $name_image);
                    $this->createImage($name_image, $id, $last, $item['options']);
                }
				
                if ( isset($imageCheckbox) and $imageCheckbox == $option ) {
                    $mod->setData('status', 1);
                } else {
                    $mod->setData('status', 0);
                }
				$mod->setData('image_type', $imageGroup[$option]);
                $mod->setData('image_delete', 2);
                //$mod->setData('options', $last);
                $mod->setData('options', $option);
				
                $mod->save();
            } else if ($item['delete'] == 2) {
                if (($name_image != "") && isset($name_image) != NULL) {
                    $mod->setData('name', $name_image)->setId($item['id']);
                    $this->createImage($name_image, $id, $item['options'], $item['options']);
                }
				
                if ( isset($imageCheckbox) and $imageCheckbox == $option ) {
                    $mod->setData('status', 1);
                } else {
                    $mod->setData('status', 0);
                }
				$mod->setData('image_type', $imageGroup[$option]);
                $mod->setData('image_delete', $item['delete'])->setId($item['id']);
                $mod->save();
            } else {
                if ($item['id'] != 0) {
                    if (($name_image != "") && isset($name_image) != NULL) {
                        $mod->setData('name', $name_image)->setId($item['id']);
                        $this->createImage($name_image, $id, $item['options'], $item['options']);
                    }
					
					if ( isset($imageCheckbox) and $imageCheckbox == $option ) {
						$mod->setData('status', 1);
					} else {
						$mod->setData('status', 0);
					}
					$mod->setData('image_type', $imageGroup[$option]);
                    $mod->setData('image_delete', $item['delete'])->setId($item['id']);
                    $mod->save();
                }
            }
        }
		
        $this->deleteImageStore();
    }
	public function deleteImageStore() {
        $image_info = Mage::getModel('storepickup/image')->getCollection()->addFilter('image_delete', 1);
        foreach ($image_info as $item) {
            $id = $item->getData('storepickup_id');
            $option = $item->getData('options');
            $image = $item->getData('name');

            $image_path = $this->getImagePath($id, $option) . DS . $image;
            // $image_path_cache = $this->getImagePathCache($id, $option) . DS . $image;
            try {
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
                if (file_exists($image_path_cache)) {
                    unlink($image_path_cache);
                }
            } catch (Exception $e) {
                
            }
			$item->delete();
        }
    }
	public function createImage($image, $id, $last, $options) {
        try {
            
            $uploader = new Varien_File_Uploader("images_id" . $options);
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $path = $this->getImagePath($id, $last);
			$image_path = $path . DS . $image;
			 unlink($image_path);
			
            $uploader->save($path, $image);
        } catch (Exception $e) {
            
        }
    }
	private function renameImage($image_name, $store_id, $id_img) {

        $name = "";
        if (isset($image_name) && ($image_name != null)) {
            $array_name = explode('.', $image_name);
            $array_name[0] = $store_id . '_' . $id_img;
            $name = $array_name[0] . '.' . end($array_name);
        }
        return $name;
    }
	public function getImagePath($store_id, $options) {
        //$path = Mage::getBaseDir('media') . DS . 'storepickup' . DS . 'images' . DS . $store_id . DS . $options;
        $path = Mage::getBaseDir('media') . DS . 'storepickup' . DS . 'images' . DS . $store_id;
        return $path;
    }
	/* public function getImagePathCache($id, $options) {
        $path = Mage::getBaseDir('media') . DS . 'storepickup' . DS . 'images' . DS . 'cache' . DS . $id . DS . $options;
        return $path;
    } */
	
	public function getMainImage($store_id) {
        $collection = Mage::getModel('storepickup/image')->getCollection()->addFieldToFilter('storepickup_id', $store_id)->addFieldToFilter('status', 1);
        $urlimage = '';
        foreach ($collection as $item) {
            if ( $item->getName() ) {
                 $urlimage = Mage::getBaseUrl('media') . 'storepickup/images/'. $store_id . '/' . $item->getName();
            }
        }
        return $urlimage;
    }
	function getStorePickUpByOrderId()
	{
		$controllerAction = Mage::app()->getRequest()->getControllerName();
		$orderId = 0;
		if($controllerAction == 'sales_order_creditmemo')
		{
			$action = Mage::app()->getRequest()->getActionName();
			if($action == 'new')
			{
				$orderId = Mage::app()->getRequest()->getParam('order_id'); 
			}
			else
			{
				$creditmemoId = Mage::app()->getRequest()->getParam('creditmemo_id');
				$creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemoId);
				$orderId  = $creditmemo->getOrderId();
			}
		}
		elseif($controllerAction == 'sales_order_invoice')
		{
			$action = Mage::app()->getRequest()->getActionName();
			if($action == 'new')
			{
				$orderId = Mage::app()->getRequest()->getParam('order_id'); 
			}
			else
			{
				$invoiceId = Mage::app()->getRequest()->getParam('invoice_id');
				$invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
				$orderId  = $invoice->getOrderId();
			}
		}
		else
		{
			$orderId = Mage::app()->getRequest()->getParam('order_id');
		}
		$storepickup = array();
		$pickUpOrder = Mage::getModel('storepickup/pickuporder')->getCollection()
				->addFieldToFilter('pickup_order_id',$orderId)
				->getFirstItem();
		if($pickUpOrder->getId())
		{
			$pickupId = $pickUpOrder->getData('pickup_id');
			$objstorepickup = Mage::getModel('storepickup/storepickup')->load($pickupId);
			$storepickup = $objstorepickup->getData();
		} 
		return $storepickup;
	}
	/* edit by David */
	function get_content_id($file,$id){
		$h1tags = preg_match_all("/(<div id=\"{$id}\">)(.*?)(<\/div>)/ismU",$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}
	
	function get_div($file,$id){
    $h1tags = preg_match_all("/(<div.*>)(\w.*)(<\/div>)/ismU",$file,$patterns);
    $res = array();
    array_push($res,$patterns[2]);
    array_push($res,count($patterns[2]));
    return $res;
	}
	
	function get_domain($url)   {   
		$dev = 'dev';
		if ( !preg_match("/^http/", $url) )
			$url = 'http://' . $url;
		if ( $url[strlen($url)-1] != '/' )
			$url .= '/';
		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : ''; 
		if ( preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs) ) { 
			$res = preg_replace('/^www\./', '', $regs['domain'] );
			return $res;
		}   
		return $dev;
	}
	/* end */
	
	
}