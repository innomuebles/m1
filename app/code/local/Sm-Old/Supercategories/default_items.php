<?php
/*------------------------------------------------------------------------
 # SM Super Categories - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
 
$helper = Mage::helper('supercategories/data');
$isAjax = Mage::app()->getRequest()->isAjax();
if ($isAjax) {
	$catid = $this->getRequest()->getPost('categoryid');
	$start = (int)$this->getRequest()->getPost('ajax_reslisting_start');
	$list = $this->getListCriterionFilter($catid);
	$child_items = $list[$catid]->child;
}
$post     = Mage::app()->getRequest()->getPost();

if( Mage::getSingleton('cms/page')->getIdentifier() == 'home-left'  && 
Mage::app()->getFrontController()->getRequest()->getRouteName() == 'cms' ) {  
	$class_listing = 'col-lg-4 col-md-4';
} else{  
	$class_listing = 'col-lg-3 col-md-3';
} 	
if( $post ){
	$cms_page = $post['cms_page'];
	$is_ajax  = $post['is_ajax_listing_tabs'];
	//$cat_id   = $post['tab_cat_id'];
	//$order_id = $post['order_id'];
	//$start = (int)$this->getRequest()->getPost('ajax_reslisting_start');
	if( $cms_page == 'home-left' ) {  
		$class_listing = 'col-lg-4 col-md-4';
	}
}else{
	$is_ajax  = "";
	$cat_id   = "";
	$order_id = "";
	$count    = "";	
}
$img_width = $this->_getConfig('product_image_width');
$img_height = $this->_getConfig('product_image_height');

if (!empty($child_items)) {
	$k = $this->getRequest()->getPost('ajax_reslisting_start', 0);
	foreach ($child_items as $item) {
		//var_dump($item);
					
		$now = date("Y-m-d");
		$newsFrom = substr($item->getData('news_from_date'), 0, 10);
		$newsTo = substr($item->getData('news_to_date'), 0, 10);
		$specialprice = $item->getData('special_price');
			
		$k++; ?>
		
		<div class="item item-supercat respl-item">
			<div class="item-inner">
				<?php
				if ( $this->_getConfig('product_image_display', 1) == 1 ) {
					?>
					<div class="w-image-box">
						<span class="hover-background"></span>
						<div class="item-image">
							<a href="<?php echo $item->link ?>" class="product-image rspl-image">
								
								
								<img src="<?php echo $this->helper('catalog/image')->init($item, 'small_image')->resize($img_width, $img_height); ?>" alt="<?php echo $this->stripTags($this->getImageLabel($item, 'small_image'), null, true) ?>" />
							</a>
						</div>

						<?php if ($newsFrom !="" && $now>=$newsFrom && ($now<=$newsTo || $newsTo=="")){?>
							<div class="new-item">
								<span><?php echo $this->__('New'); ?></span>
							</div>
						<?php }?>
						
						<?php if ( $specialprice ){ ?>
							<div class="sale-item">
								<span><?php echo $this->__('Sale'); ?></span>
							</div>
						<?php }?>

					</div>
				<?php
				}?>
				
				<div class="item-info">
				
					<?php if ((int)$this->_getConfig('product_reviews_count', 1)) { ?>
						<div class="item-review">
							<?php echo $this->getReviewsSummaryHtml($item, "short", true);?>
						</div>
					<?php }?>
				
					<?php if ($this->_getConfig('product_title_display', 1) == 1) { ?>
						<div class="item-title">
							<a href="<?php echo $item->link ?>" <?php echo $helper::parseTarget($this->_getConfig('product_links_target', '_self')) ?>
							   title="<?php echo $item->title ?>">
								<?php echo $helper->truncate($item->title, $this->_getConfig('product_title_maxlength', 25)); ?>
							</a>
						</div>
					<?php } ?>
				
					<?php if ((int)$this->_getConfig('product_price_display', 1)) {
						?>
						<div class="item-price">
							<div class="sale-price">
								<?php echo $this->getPriceHtml($item, true); ?>
							</div>
						</div>
					<?php }?>

				<?php if ($this->_getConfig('product_description_display', 1) == 1 && $helper::_trimEncode($item->description) != '') { ?>
					<div class="item-desc">
						<?php echo $helper->truncate($item->description, $this->_getConfig('product_description_maxlength', 200)); ?>
					</div>
				<?php } ?>
				<?php if ((int)$this->_getConfig('product_readmore_display', 1)) { ?>
					<div class="item-readmore">
						<a href="<?php echo $item->link; ?>"
						   title="<?php echo $item->title ?>" <?php echo $helper->parseTarget($this->_getConfig('product_links_target', '_self')); ?> >
							<?php echo $this->_getConfig('product_readmore_text', 'Detail'); ?>
						</a>
					</div>
				<?php } ?>		
				</div>
				
				
				<a class="product-image" style="display:none;" href="<?php echo $item->link ?>" > </a><!--LINK FOR QUICKVIEW-->
				
				<?php if ((int)$this->_getConfig('product_addcart_display', 1)) { ?>
					<?php if($item->isSaleable()){ ?>				
					<button class="btn-pd btn-cart" data-toggle="tooltip" data-placement="left" title="<?php echo $this->__('Add to Cart') ?>" onclick="setLocation('<?php echo $this->getAddToCartUrl($item) ?>')">
						<?php echo $this->__('Add to Cart') ?>
					</button>
				<?php }}?>
					
					
				<?php if ($this->helper('wishlist')->isAllow() && (int)$this->_getConfig('product_addwishlist_display', 1)) : ?>
					<a data-toggle="tooltip" data-placement="left" title="<?php echo $this->__('Add to Wishlist') ?>" href="<?php echo $this->helper('wishlist')->getAddUrl($item) ?>" class="btn-pd item-wishlist"><?php echo $this->__('Add to Wishlist') ?></a>
				<?php endif; ?>
						
				<?php 
				if( (int)$this->_getConfig('product_addcompare_display', 1) ):
				if( $_compareUrl = $this->getAddToCompareUrl($item) ): ?>
					<a data-toggle="tooltip" data-placement="left" title="<?php echo $this->__('Add to Compare') ?>" href="<?php echo $_compareUrl ?>" class="btn-pd item-compare"><?php echo $this->__('Add to Compare') ?></a>
				<?php endif; 
				endif; ?>

			</div>
		</div>

		
	<?php
	}
}?>

