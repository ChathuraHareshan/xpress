<?php

class PayHereConfig {

private static $merchant_id = "1234258"; 
    private static $merchant_secret = "NDE4Nzc1NDQyNjIwODA4NTA0NDU2MzU5MDU1MTgyNTk3MzIzODI4";
    
   
    public static function getMerchantId() {
        return self::$merchant_id;
    }
    
    public static function getMerchantSecret() {
        return self::$merchant_secret;
    }
    

    public static function generateHash($order_id, $amount, $currency = "LKR") {
        $hash = strtoupper(
            md5(
                self::$merchant_id . 
                $order_id . 
                number_format($amount, 2, '.', '') . 
                $currency .  
                strtoupper(md5(self::$merchant_secret)) 
            )
        );
        return $hash;
    }
}
?>