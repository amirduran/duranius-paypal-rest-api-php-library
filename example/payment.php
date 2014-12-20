<?php

require_once './DPayPal.php'; //Import library

$paypal = new DPayPal(); //Create an object
//Now we will call SetExpressCheckout API operation. All available parameters for this method are available here https://developer.paypal.com/docs/classic/api/merchant/SetExpressCheckout_API_Operation_NVP/
$requestParams = array(
    'RETURNURL' => "http://photo-epicenter.com", //Enter your webiste URL here
    'CANCELURL' => "http://photo-epicenter.com"//Enter your website URL here
);

$orderParams = array(
    'LOGOIMG' => "http://photo-epicenter.com/wp-content/uploads/2014/10/logo2.png", //You can paste here your logo image URL
    "MAXAMT" => "100", //Set max transaction amount
    "NOSHIPPING" => "1", //I do not want shipping
    "ALLOWNOTE" => "0", //I do not want to allow notes
    "BRANDNAME" => "Photo Epicenter Subscription",
    "GIFTRECEIPTENABLE" => "0",
    "GIFTMESSAGEENABLE" => "0"
);
$item = array(
    'PAYMENTREQUEST_0_AMT' => "20",
    'PAYMENTREQUEST_0_CURRENCYCODE' => 'GBP',
    'PAYMENTREQUEST_0_ITEMAMT' => "20",
    'L_PAYMENTREQUEST_0_NAME0' => 'Buy me a beer :)',
    'L_PAYMENTREQUEST_0_DESC0' => 'Buy me a beer :)',
    'L_PAYMENTREQUEST_0_AMT0' => "20",
    'L_PAYMENTREQUEST_0_QTY0' => '1',
        //"PAYMENTREQUEST_0_INVNUM" => $transaction->id - This field is useful if you want to send your internal transaction ID
);

//Now you will be redirected to the PayPal to enter your customer data
//After that, you will be returned to the RETURN URL 
$response = $paypal->SetExpressCheckout($requestParams + $orderParams + $item);

//Response is aslo accessible by calling  $paypal->getLastServerResponse()

if (is_array($response) && $response['ACK'] == 'Success') { //Request successful
    //Now we have to redirect user to the PayPal
    $token = $response['TOKEN'];

    header('Location: https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=' . urlencode($token));
} else if (is_array($response) && $response['ACK'] == 'Failure') {
    var_dump($response);
    exit;
}
exit;

