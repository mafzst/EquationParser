<?php

function debug_var($var)
{
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
}

function debug_stop($var) {
	debug_var($var);
	die();
}

function clean_spaces($string) {
	$i = 0;
	$result = "";

	while(isset($string[$i])) {
		if($string[$i] !== " ") {
			$result .= $string[$i];
		}

		$i++;
	}

	return $result;
}