<?php
namespace AdminModule\Tools;

/**
 * Generates random password
 * @author Kryštof Selucký
 * @package DarkAdmin
 */
class PasswordGenerator extends \Nette\Object {
    
    /**
     * Generates random password
     * @param int $maxLong maximal long of password
     * @param int $minLong minimal long of password
     * @param boolean $sensitive is case sensitive?
     * @param boolean $numbers can password contain numbers?
     * @param boolean $specialChars can password contain special chars?
     * @return string generated password
     */
    public static function generatePassword($maxLong = 10, $minLong = 7, $sensitive = true, $numbers = true, $specialChars = false) {
        $long = mt_rand($minLong, $maxLong);
        if($sensitive == true)
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        else
            $characters = 'abcdefghijklmnopqrstuvwxyz';
        
        if($numbers == true)
            $characters .= "123456789";
        if($specialChars == true)
            $characters .= "_-?!()+-*#@";

        $password = "";
        for($i=0; $i <= $long; $i++) {
            $char = substr($characters, mt_rand(0, strlen($characters)-1), 1);
            $password .= $char;
        }
        
        return $password;
    }
    
}
