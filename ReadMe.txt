How to Display Google Recaptcha in Magento Onepage Checkout?

--Navigate to your "billing.phtml" located at app\design\frontend\base\default\template\persistent\checkout\onepage\billing.phtml
(Magento core path)
--If you are using any custom store theme then you will find that "billing.phtml" at this path.
app\design\frontend\{package}\{theme}\template\persistent\checkout\onepage\billing.phtml
e.g :In RWD default theme : app\design\frontend\rwd\default\template\persistent\checkout\onepage\billing.phtml
	
--Open that file using any editor and find <li id='billing-new-address-form'> located around line no. 41 and paste the following shortcode after 'billing-new-address-form' id tag closed to display Google Recaptcha.

<!-- MageComp-Recaptcha Start Code -->
	<script src='https://www.google.com/recaptcha/api.js'></script>
        <?php if ($this->helper('recaptcha')->showOnOnepage() == 1) { ?>
             <li id="rcode">
                 <div class="captcha">
                      <div class="g-recaptcha" data-sitekey="<?php echo $this->helper('recaptcha')->getKey(); ?>"
                           data-theme="<?php echo($this->helper('recaptcha')->getTheme()); ?>"></div>
                      </div>
                 <span id="captcha-required" style='display:none; color:#ff0000'><?php echo $this->__('Please Fill Recaptcha To Continue'); ?></span>
             </li>
        <?php } ?>
<!-- MageComp-Recaptcha End Code -->	

--After closing of form tag, update below script code.

<!-- MageComp-Recaptcha Start Work -->		
	<script type="text/javascript">
		//<![CDATA[
			var billing = new Billing('co-billing-form', '<?php echo $this->getUrl('checkout/onepage/getAddress') ?>address/', '<?php echo $this->getUrl('checkout/onepage/saveBilling') ?>');
			var billingForm = new VarienForm('co-billing-form');

			//billingForm.setElementsRelation('billing:country_id', 'billing:region', '<?php echo $this->getUrl('directory/json/childRegion') ?>', '<?php echo $this->__('Select State/Province...') ?>');
			$('billing-address-select') && billing.newAddress(!$('billing-address-select').value);

			var billingRegionUpdater = new RegionUpdater('billing:country_id', 'billing:region', 'billing:region_id', <?php echo $this->helper('directory')->getRegionJson() ?>, undefined, 'billing:postcode');
		//]]>
	</script>
	<script type="text/javascript">
        jQuery('.btn').click(function () {
            var billing = new Billing('co-billing-form', '<?php echo $this->getUrl('checkout/onepage/getAddress') ?>address/', '<?php echo $this->getUrl('checkout/onepage/saveBilling') ?>');
        <?php
			if($this->helper('recaptcha')->showOnOnepage() == 0){ ?>
			billing.save();
			<?php } else { ?>
			if (grecaptcha.getResponse() != "") {
				billing.save();
			}
			else {
				document.getElementById("captcha-required").style.display = "block";
			}
        <?php } ?>
		});
	</script>
<!-- MageComp-Recaptcha End Work -->

--That’s it! Feel free to ask if you are facing any issue while implementing above code.