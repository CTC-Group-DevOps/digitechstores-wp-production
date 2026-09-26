<?php

require_once 'paymentRequest.php';
// Point to where you downloaded the phar
require_once('httpful.phar');
use Httpful\Request;

class  PaymentController {

/*
Call this function from your payment page (ex. on button click) and it should redirect to Syber Payment Page.
The $paymentReq object should have all it fields set appropriatly, this is not checked here!
If an exception occurs, the user will be redirected to the merchant's custom error page (URL should be set also on the $paymentReq object)
*/    
static function redirectToPayment($paymentReq) {
    
    try {
        
        //set the uri for SyberPay WS
        $uri = "http://41.78.108.194:8089/SyberPay/syberpay/getUrl";

        //Generate the message hash
        $hash = self::generateHash($paymentReq);
        $paymentReq->setHash($hash);

        $redirectUrl = self::getPaymentUrl($uri, $paymentReq);
        header('Location:'.$redirectUrl);
        //redirect($redirectUrl);
        
    } catch (Exception $e) {
        $redirectUrl = $paymentReq->getErrorPageUrl();
        header('Location:'.$redirectUrl);
    }
}

/*
Calls SyberPay WS and returns the URL.
The $paymentReq object should have all it fields set appropriatly, this is not checked here!
If an exception occurs, the function will return the merchant's custom error page (URL should be set also on the $paymentReq object)
*/
private static function getPaymentUrl($url, $paymentReq) {
        
    try {
        //set the HTTP content-type to json
        $content_type = 'application/json';    
    
        //encode the data as json. The data should have : applicationId, serviceId, amount, customerRef , and paymentInfo.
        $data = json_encode($paymentReq );
        //if PHP < 5.4
        //$data = $paymentReq->to_json();
        echo "$data";
    
        //call the WS and get the json response.
        $response = Request::post($url, $data)->addHeader('Content-Type',$content_type)->send();
        echo "$response"; 

        //get the response code value 
        $rcode = $response->body->responseCode;
        echo 'Response Code' . $rcode;
    
        //code == 1 means requset is successful
        if ($rcode == 1) {
            //get the payment url
            $redirectUrl = $response->body->paymentUrl;
            echo 'Response URL' . $redirectUrl;
        } else {
            //return the default error page
            $redirectUrl = $paymentReq->getErrorPageUrl();
        }    
    } catch(Exception $e) {
        $redirectUrl = $paymentReq->getErrorPageUrl();
    }
    
    return $redirectUrl;
}

/*
Calculate the SHA-256 hash of the message based on the agreed sequence.
No field validation is done here!
*/
private static function generateHash($paymentReq) {

    //TODO: Should add a check for empty mandatory field.
    $message = $paymentReq->getKey() . '|' . $paymentReq->getApplicationId() . '|' . $paymentReq->getServiceId() . '|' . $paymentReq->getAmount() . 
    '|' . $paymentReq->getCurrency() .  '|' . $paymentReq->getCustomerRef() . '|' . $paymentReq->getSalt();
    
    echo $message;

    $hash = hash('sha256', $message);
    return $hash;

}
    
}


?>
