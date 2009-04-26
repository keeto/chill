<?php

class SpecBase extends PHPSpec_Context
{
	function getDump($var) {
		ob_start();
		var_dump($var);
		$dump = ob_get_contents();
		ob_end_clean();
		return $dump;
	}
}