<?php

require 'vendor/autoload.php';

$redis = new Predis\Client();

while(true) {
	$serialize = $redis->rpop('queue');

	if($serialize) {
		$object = unserialize($serialize);
		$object->handle();
	}

	sleep(1);
}