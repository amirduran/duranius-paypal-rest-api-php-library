paypal-rest-api-php-library
===========================

If you need to integrate your website with PayPal REST API, then all you need is to download this library and you are ready to go. There are only two files you need to download and import to your project.

# How to install
In the folder called library you will find two files:
1. DPayPal.php 
2. cacert.pem
 
Copy the files in your project folder, and reference file _DPayPal.php_ using require_once php command:
require_once "DPayPal.php"

Please make sure that both files DPayPal.php and cacert.pem are located in the same folder. If you want to keep the files in the different folders, open the file DPayPal.php and edit the following line: 

CURLOPT_CAINFO => dirname(__FILE__) . '/cacert.pem', //CA cert file







