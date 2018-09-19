<?php

require 'vendor/autoload.php';

use Predis\Client;
use Carbon\Carbon;

function dispatch($job, $delay = 0)
{
	$redis = new Client();
	if($job->shouldDelay()) {
		$redis->zadd(
			'queue:delayed',
			$job->makeStartTime(),
			serialize($job)
		);
	} else {
		$redis->lpush(
			'queue',
			serialize($job)
		);
	}
}