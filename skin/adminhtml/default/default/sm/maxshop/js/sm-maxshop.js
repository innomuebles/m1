jQuery(document).ready(function($){

	jQuery('#row_maxshop_cfg_general_body_background_image td .inline img').click(function(){
		$this = jQuery(this);
		jQuery('#row_maxshop_cfg_general_body_background_image td .inline').removeClass('active');
		jQuery('#row_maxshop_cfg_general_body_background_image td .inline img').removeClass('active');
		$this.addClass('active');
	});
	
	jQuery('#row_maxshop_cfg_general_body_background_image td input').each(function(){
		$this = jQuery(this);
		attr_id = $this.attr('id');
		attr_active = $this.attr('checked');
		if( attr_active == 'checked' ){		
			jQuery('#row_maxshop_cfg_general_body_background_image td .inline').each(function(){
				$this = jQuery(this);
				attr_for = $this.attr('for');				
				if( attr_for == attr_id ){
					$this.addClass('active');
				}
			});	
		}
	});
	
	if(jQuery('#maxshop_cfg_general_use_background_image').val() == 1){
		jQuery('#row_maxshop_cfg_general_body_background_image').css('display','table-row');	
	}
	else{
		jQuery('#row_maxshop_cfg_general_body_background_image').css('display','none');
	}
	jQuery('#maxshop_cfg_general_use_background_image').change(function() {
		v = jQuery(this).val();
		//alert(v);
		if(v==0){
			jQuery('#row_maxshop_cfg_general_body_background_image').css('display','none');
		}else{
			//jQuery('#row_maxshop_cfg_general_body_background_image').removeAttr("maxshop");
			jQuery('#row_maxshop_cfg_general_body_background_image').css('display','table-row');
		}
	});
});

