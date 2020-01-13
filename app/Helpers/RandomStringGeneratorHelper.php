<?php  

namespace App\Helpers;

use Validator;

class RandomStringGeneratorHelper
{
	public static function generateRandomString($length){
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $tempPassword = '';
        for ($counter = 0; $counter <= $length; $counter++) {
            $tempPassword .= $characters[rand(0, $charactersLength - 1)];
        }

        return $tempPassword;
	} 
}
?>