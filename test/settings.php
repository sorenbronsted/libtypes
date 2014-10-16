<?php

spl_autoload_register(function($class) {
	$paths = array(
		"types",
	);

	foreach($paths as $path) {
		$fullname = $path.'/'.$class.'.php';
		if (is_file($fullname)) {
			include($fullname);
			return true;
		}
	}
	return false;
});
