<?php
    class  PaymentRequest implements JsonSerializable {
    
    /* Member variables */
    private $applicationId = "0000000005";
    private $serviceId = "001002000201";
    private $amount;
    private $currency = "SDG";
    private $customerRef;
    private $paymentInfo;
    private $key = "zTm9r0mK";
    private $salt = "5Ylg71PeG";
    private $hash;
    private $errorPageUrl = "http://localhost:8080/error.php";


    /* Member functions */
    function setApplicationId($par){
       $this->applicationId = $par;
    }
    function getApplicationId(){
       return $this->applicationId;
    }

    function setServiceId($par){
       $this->serviceId = $par;
    }
    function getServiceId(){
       return $this->serviceId;
    }
    
    function setAmount($par){
       $this->amount = $par;
    }
    function getAmount(){
       return $this->amount;
    }

    function setCurrency($par){
       $this->currency = $par;
    }
    function getCurrency(){
       return $this->currency;
    }

    function setCustomerRef($par){
       $this->customerRef = $par;
    }
    function getCustomerRef(){
       return $this->customerRef;
    }

    function setPaymentInfo($par){
       $this->paymentInfo = $par;
    }
    function getPaymentInfo(){
       return $this->paymentInfo;
    }

    function setKey($par){
       $this->key = $par;
    }
    function getKey(){
       return $this->key;
    }

    function setSalt($par){
       $this->salt = $par;
    }
    function getSalt(){
       return $this->salt;
    }

    function setHash($par){
       $this->hash = $par;
    }
    function getHash(){
       return $this->hash;
    }

    function setErrorPageUrl($par){
       $this->errorPageUrl = $par;
    }
    function getErrorPageUrl(){
       return $this->errorPageUrl;
    }

    
    public function jsonSerialize() {
        return [
            'applicationId' => $this->getApplicationId(),
            'serviceId' => $this->getServiceId(),
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'customerRef' => $this->getCustomerRef(),
            'paymentInfo' => $this->getPaymentInfo(),
            'hash' => $this->getHash()
        ];
    }

    //for PHP < 5.4 use this method instead of the above
    /*
    public function to_json() {
        return json_encode(array(
            'applicationId' => $this->getApplicationId(),
            'serviceId' => $this->getServiceId(),
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'customerRef' => $this->getCustomerRef(),
            'paymentInfo' => $this->getPaymentInfo(),
            'hash' => $this->getHash()
        ));
    }
    */
}

?>

