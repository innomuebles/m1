 jQuery(document).ready(function(){
    
  //  vnwomanDay = new Date(2014, 2, 10); 

    hideAllShipping();
jQuery('input[type="radio"][name="shipping_method"]').click(function(){
  //  jQuery('#maiuoc').show();
  hideAllShipping();
   var code = jQuery(this).val();
   if(jQuery(this).is(':checked'))
   {
     showShipping(code);
   }
   
});
 jQuery('input[type="radio"][name="shipping_method"]').each(function(){
    var code = jQuery(this).val();
    if(jQuery(this).is(':checked'))
   {
     showShipping(code);
   }
   
 });
    jQuery('input[name="pickup_shipping_data"]').live("change", function(){
     jQuery("#shipping_time_pickup_id").html('<option selected="" value="">Shipping Time</option>');
    var mst_pickup_id = jQuery('input[type="text"][name="mst_store_id"]').val();
     if(mst_pickup_id.trim() == '' || parseInt(mst_pickup_id) == 0)
        {
            return false;
        }
    var day_check =  jQuery(this).val();
    var tg_day = day_check.split('/');
    var thang = parseInt(tg_day[0]) - 1;
     //check day if < day now
    var now = new Date();
    var ngaynhap = new Date(tg_day[2], thang, tg_day[1]); 
    var offset = ngaynhap.getTime() - now.getTime();
    var totalday = Math.round(offset/ 1000 / 60 / 60 / 24);
    if(totalday < -1)
    {
        return false;
    }
    var thu_day = new Date(tg_day[2], thang, tg_day[1]);
    var str_thu_day = ''+thu_day;
    str_thu_day = str_thu_day.trim();
    var thu = str_thu_day.substr(0,3);
  //  alert(thu+'_open_'+mst_pickup_id); 
    var time_open = jQuery('input[id="'+thu+'_open_'+mst_pickup_id+'"]').val();
    var time_close = jQuery('input[id="'+thu+'_close_'+mst_pickup_id+'"]').val();
    //alert(mst_pickup_id);
    var tg_time_open = time_open.split(':');
    var h_time_open = tg_time_open[0];
    var m_time_open = tg_time_open[1];
    var tg_time_close = time_close.split(':');
     var h_time_close = tg_time_close[0];
    var m_time_close = tg_time_close[1];
    var new_h;
   //  || (parseInt(h_time_close) - parseInt(h_time_open) == 0 && parseInt(m_time_close) - parseInt(m_time_open))
    if(parseInt(h_time_close) - parseInt(h_time_open) > 0)
    {
    if(parseInt(m_time_open) < 30)
    {
       jQuery("select[id='shipping_time_pickup_id']").append('<option value="'+h_time_open+':'+m_time_open+'">'+h_time_open+':'+m_time_open+'</option>');
       jQuery("select[id='shipping_time_pickup_id']").append('<option value="'+h_time_open+':30">'+h_time_open+':30</option>'); 
    }
    else
    {
         jQuery("select[id='shipping_time_pickup_id']").append('<option value="'+h_time_open+':'+m_time_open+'">'+h_time_open+':'+m_time_open+'</option>');
    }
    for(var i = (parseInt(h_time_open) + 1);i < h_time_close;i++)
    {
        if(i < 10)
        {
           new_h = '0'+i; 
        }
         else
         {
            new_h = i;
         }
        jQuery("select[id='shipping_time_pickup_id']").append('<option value="'+new_h+':30">'+new_h+':00</option>');
         jQuery("select[id='shipping_time_pickup_id']").append('<option value="'+new_h+':30">'+new_h+':30</option>');
     }
    if(parseInt(m_time_close) < 30)
    {
       jQuery("select[id='shipping_time_pickup_id']").append('<option value="'+h_time_close+':00">'+h_time_close+':00</option>');
       if(parseInt(m_time_close) > 0)
       jQuery("select[id='shipping_time_pickup_id']").append('<option value="'+h_time_close+':'+m_time_close+'">'+h_time_close+':'+m_time_close+'</option>');
    }
    else
    {
         jQuery("select[id='shipping_time_pickup_id']").append('<option value="'+h_time_close+':00">'+h_time_close+':00</option>');
         jQuery("select[id='shipping_time_pickup_id']").append('<option value="'+h_time_close+':30">'+h_time_open+':30</option>');
         jQuery("select[id='shipping_time_pickup_id']").append('<option value="'+h_time_close+':'+m_time_close+'">'+h_time_close+':'+m_time_close+'</option>');
    }
    }
    if(parseInt(h_time_close) - parseInt(h_time_open) == 0 && parseInt(m_time_close) - parseInt(m_time_open) > 0)
    {
         jQuery("select[id='shipping_time_pickup']").append('<option value="'+h_time_open+':'+m_time_open+'">'+h_time_open+':'+m_time_open+'</option>');
         if(parseInt(m_time_close) - parseInt(m_time_open) > 30)
         jQuery("select[id='shipping_time_pickup_id']").append('<option value="'+h_time_open+':30">'+h_time_open+':30</option>');
          jQuery("select[id='shipping_time_pickup_id']").append('<option value="'+h_time_close+':'+m_time_close+'">'+h_time_close+':'+m_time_close+'</option>');
    }
  });  
});
function showShipping(code)
{
  if(jQuery("#shipping_form_"+code).length != 0)
   {
    jQuery("#shipping_form_"+code).show();
    jQuery(this).find('.required-entry').attr('disabled','false');
   } 
}
function hideAllShipping()
{
    jQuery('input[type="radio"][name="shipping_method"]').each(function(){
       code = jQuery(this).val();
        jQuery("#shipping_form_"+code).hide();
         jQuery(this).find('.required-entry').attr('disabled','false');
    });
}      