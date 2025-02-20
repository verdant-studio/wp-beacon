<?php

const ABSPATH = __DIR__ . '/../';

require_once __DIR__ . '/../vendor/autoload.php';

use Brain\Monkey;

Brain\Monkey\setUp();

register_shutdown_function(
	function () {
		Brain\Monkey\tearDown();
	}
);
