<?php

if(!function_exists('generateRandomString')){
	function generateRandomString($length = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}

if (!function_exists('encrypted_string')) {
	function encrypted_string($content)
	{
		$prefix_no = rand(1,9);
		$suffix_no = rand(1,9);
		$prefix = generateRandomString($prefix_no);
		$suffix = generateRandomString($suffix_no);
		$letters = str_split($content);
		$convertedString = $prefix_no . $prefix;
		foreach ($letters as $key => $letter){
			$decVal = ord($letter);
			if($key%2 == 0){
				$decVal += 1;
			}else{
				$decVal -= 1;
			}
			$convertedString .= chr($decVal);
		}
		$convertedString .= $suffix . $suffix_no;
		return $convertedString;
	}

}

if (!function_exists('decrypted_string')) {
	function decrypted_string($encrypted_content)
	{
    	$p_content = $encrypted_content;
		$prefix_no = (int) substr($p_content, 0, 1);
		$suffix_no = (int) substr($p_content, -1);
		$content = substr($p_content, 1, -1);
		$suffixed = substr($content, $prefix_no);
		$encrypted = substr($suffixed, 0, -$suffix_no);
		$letters = str_split($encrypted);
		$realString = '';
		foreach ($letters as $key => $letter){
			$decVal = ord($letter);
			if($key%2 == 0){
				$decVal -= 1;
			}else{
				$decVal += 1;
			}
			$realString .= chr($decVal);
		}
		return $realString;
	}
}
