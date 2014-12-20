<?php

class DPayPal {

    //API Credentials
    protected $username = "payment-sandbox_api1.cogora.com"; //PayPal API username
    protected $password = "S28ND8MMVRZP5VHT"; //PayPal API password
    protected $apiSignature = "Ad94LM04dMu7tJGxGq6vqZPzqwaHAy5PV2b8Sj0ggSn3e5Eb1fqQK7KO"; //PayPal API signature
    protected $apiVersion = "74.0"; //Set PayPal API version
    //If you are using live environment use the following URL: https://api-3t.paypal.com/nvp 
    //If you are using sandbox environment then use the following URL: https://api-3t.sandbox.paypal.com/nvp
    protected $payPalAPIUrl = "https://api-3t.sandbox.paypal.com/nvp";
    protected $errorReportingEnabled = true;
    protected $errors = array(); //Here you can find errors for your last API call 
    protected $lastServerResponse; //Here you can find PayPal response for your last successfull API call

    public function SetExpressCheckout($request) {
        return $this->sendRequest($request, "SetExpressCheckout");
    }

    public function DoExpressCheckoutPayment($request) {
        return $this->sendRequest($request, "DoExpressCheckoutPayment");
    }

    public function GetExpressCheckoutDetails($request) {
        return $this->sendRequest($request, "GetExpressCheckoutDetails");
    }

    public function sendRequest($requestData, $method) {

        if (!isset($method)) {
            array_push($this->errors, "Method name can not be empty");
        }
        if (!isset($requestData)) {
            array_push($this->errors, "Request data is can not be empty");
        }

        if ($this->checkForErrors()) {//If there are errors, STOP
            if ($this->errorReportingEnabled())//If error reporting is enabled, show errors
                $this->showErrors();

            $this->lastServerResponse = null;
            return false; //Do not send a request
        }
        $requestParameters = array(
            "USER" => $this->username,
            "PWD" => $this->password,
            "SIGNATURE" => $this->apiSignature,
            "METHOD" => $method,
            "VERSION" => $this->apiVersion,
        );
        $requestParameters+=$requestData;
        $finalRequest = http_build_query($requestParameters);

        $ch = curl_init();
        
        $curlOptions=$this->getcURLOptions();
        $curlOptions[CURLOPT_POSTFIELDS]=$finalRequest;
        //var_dump($curlOptions);exit;
        
        curl_setopt_array($ch, $curlOptions);
        $serverResponse = curl_exec($ch);

        if (curl_errno($ch)) {
            $this->errors = curl_error($ch);
            curl_close($ch);
            if ($this->errorReportingEnabled) {
                $this->showErrors();
            }
            $this->lastServerResponse = null;
            return false;
        } else {
            curl_close($ch);
            $result = array();
            parse_str($serverResponse, $result);
            $this->lastServerResponse = $result;
            return $this->lastServerResponse;
        }
    }

    public function getLastServerResponse() {
        return $this->lastServerResponse;
    }

    public function showErrors() {
        var_dump($this->errors);
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setApiSignature($apiSignature) {
        $this->apiSignature = $apiSignature;
    }

    //Some private methods
    private function checkForErrors() {
        if (count($this->errors) > 0) {
            return true;
        }
        return false;
    }

    private function getcURLOptions() {
        return array(
            CURLOPT_URL => $this->payPalAPIUrl,
            CURLOPT_VERBOSE => 1,
            //Have a look at this: http://stackoverflow.com/questions/14951802/paypal-ipn-unable-to-get-local-issuer-certificate
            //You can download a fresh cURL pem file from here http://curl.haxx.se/ca/cacert.pem
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO => dirname(__FILE__) . '/cacert.pem', //CA cert file
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_POST => 1,
        );
    }

}
