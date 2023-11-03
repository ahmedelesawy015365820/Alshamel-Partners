<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function ahmed(){
        return 'dsd';
        $client = new \Adyen\Client();
        $client->setXApiKey('sk_test_XKokBfNWv6FIYuTMg5sLPjhJ');
        $service = new \Adyen\Service\Checkout($client);

        $params = array(
            "amount" => array(
                "currency" => "KWD",
                "value" => 1000
            ),
            "reference" => "YOUR_ORDER_NUMBER",
            "paymentMethod" => array(
                "type" => "knet"
            ),
            "shopperName" => [
                "firstName" => "Happy",
                "lastName" => "Testing",
                "gender" => "UNKNOWN"
            ],
              "shopperEmail" => "shopper@email.com",
              "telephoneNumber" => "000000000000",
              "returnUrl" => "https://your-company.com/checkout?shopperOrder=12xy....",
              "merchantAccount" => "YOUR_MERCHANT_ACCOUNT"
            );
            return $service->payments($params);
    }
}
