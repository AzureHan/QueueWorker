<?php

require 'vendor/autoload.php';

function dispatch($object)
{
	$redis = new Predis\Client();
	$redis->lpush('queue', serialize($object));
}