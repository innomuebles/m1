<?php
class Credix_Cpayment_Model_PaymentLogic extends Mage_Payment_Model_Method_Cc
{
    /**
     * unique internal payment method identifier
     */
    protected $_code = 'cpayment';
    
    protected $_formBlockType = 'cpayment/form_pos';
    
    protected $_isInitializeNeeded      = false;
 
    /**
     * this should probably be true if you're using this
     * method to take payments
     */
    protected $_isGateway               = true;
 
    /**
     * can this method authorise?
     */
    protected $_canAuthorize            = true;
 
    /**
     * can this method capture funds?
     */
    protected $_canCapture              = true;
 
    /**
     * can we capture only partial amounts?
     */
    protected $_canCapturePartial       = false;
 
    /**
     * can this method refund?
     */
    protected $_canRefund               = false;
 
    /**
     * can this method void transactions?
     */
    protected $_canVoid                 = true;
 
    /**
     * can admins use this payment method?
     */
    protected $_canUseInternal          = true;
 
    /**
     * show this method on the checkout page
     */
    protected $_canUseCheckout          = true;
 
    /**
     * available for multi shipping checkouts?
     */
    protected $_canUseForMultishipping  = true;
 
    /**
     * can this method save cc info for later use?
     */
    protected $_canSaveCc = false;
    
    public function assignData($data)
    {
        // Call parent assignData
        parent::assignData($data);

        // Get Mage_Payment_Model_Info instance from quote 
        $info = $this->getInfoInstance();

        // Add some arbitrary post data to the Mage_Payment_Model_Info instance 
        // so it is saved in the DB in the 'additional_information' field        
        $info->setAdditionalInformation('cc_cuotas',$data['cc_cuotas']);
        return $this;
    }
 
    /**
     * this method is called if we are just authorising
     * a transaction
     */
    public function authorize (Varien_Object $payment, $amount)
    {       
    
    }    

    /**
     * this method is called if we are authorising AND
     * capturing a transaction
     */
    public function capture (Varien_Object $payment, $amount)
    {
        $info = $this->getInfoInstance();
        $cc_cuotas = $info->getAdditionalInformation('cc_cuotas');
        //$url="https://qaconectividad.credix.com/webservices/ws/transactions?wsdl";
        $url="https://conectividad.credix.com/webservices/ws/transactions?wsdl";
        $user=Mage::getStoreConfig("payment/cpayment/api_key");
        $pass=Mage::getStoreConfig("payment/cpayment/api_secret");
        $expdate=$payment->getData("cc_exp_month").substr($payment->getData("cc_exp_year"),2);
        $cnumber=$payment->getData("cc_number");
        $cvv=$payment->getData("cc_cid");
        $cuotas=$cc_cuotas;
        $amo=number_format($amount,2,"","");
        $reference=rand(1,9).rand(0,5).rand(5,9).rand(0,7);
        $params=array(
            "user"=>$user,
            "pass"=>$pass,           
        );  
        $trans=array(
            "user"=>$user,
            "pass"=>$pass,
            "tipo_trans"=>"S",
            "dia"=>date("Y-m-d"),
            "numtar"=>$cnumber,
            "moneda"=>188,
            "fecha_venc"=>$expdate,
            "cvv"=>$cvv,
            "importe"=>$amo,
            "cuotas"=>$cuotas,
            "referencia"=>$reference,
        );       
        $webServiceClient = new SoapClient($url);        
        $response = $webServiceClient->servicioDisponible($user,$pass);        
        if($response==="00")
        {            
            $res=$webServiceClient->auth_transaction($user,$pass,$cnumber,$expdate,$cvv,$amo,188,$cuotas,$reference,'S');           
            $tran=(string)substr($res,0,2);                        
            if($tran==="00")
            {               
                
            }
            else
            {
                if($tran=="01")
                {                        
                    Mage::throwException("Transaction Declined.");
                }
                elseif($tran=="89")
                {
                    Mage::throwException('Modelo de Usuario.');                    
                }
                else
                {
                    Mage::throwException('Error reportado por Sistema.');                    
                }
            }
        }
    }
    /**
     * called if refunding
     */
    public function refund (Varien_Object $payment, $amount)
    {
    
    }
    /**
     * called if voiding a payment
     */
    public function void (Varien_Object $payment)
    {
    
    }
    /*****CC Verification Starts******/
    public function getVerificationRegEx()
    {
        return array_merge(parent::getVerificationRegEx(), array(
            'CR' => '/^[0-9]{3}$/' 
       ));
    }
 
    public function OtherCcType($type)
    {
        
    } 
    public function validate()
    {
        /*parent::validate(); 
        $info = $this->getInfoInstance();
        $ccNumber = $info->getCcNumber();
        $availableTypes = explode(',',$this->getConfigData('cctypes'));        
        if(!in_array($info->getCcType(), $availableTypes)){
            Mage::throwException($this->_getHelper()->__('Credit card type is not allowed for this payment method.'));
        }        
        if(!$this->validateCcNum($info->getCcNumber())){
            Mage::throwException($this->_getHelper()->__('Invalid Credit Card Number'));
        }
        if($info->getCcType()=='CR' && !preg_match('/^5[0-5][0-9]{14}$/', $ccNumber)){
            Mage::throwException($this->_getHelper()->__('Credit card number mismatch with credit card type.'));
        }        
        $verificationRegex = $this->getVerificationRegEx();
        if(!preg_match($verificationRegex[$info->getCcType()], $info->getCcCid()))
        {
            Mage::throwException($this->_getHelper()->__('Please enter a valid credit card verification number.'));
        }*/
 
    }
 
    
    /*****CC Verification Ends*******/
}
?>
