<?php

require 'vendor/autoload.php';

use Predis\Client;
use Carbon\Carbon;

// Retrieve jobs which is time to execute
function queue_delayed_job()
{
	$redis = new Client();
	array_map(
		function($serializedJob) use($redis) {
			$redis->lpush(
				'queue',
				$serializedJob
			);
		},
		$redis->zrangebyscore(
			'queue:delayed',
			'-inf',
			Carbon::now()->timestamp
		)
	);
}

function queue_job()
{
	$redis = new Client();
	// Deal with new job
	$serializedJob = $redis->rpop('queue');
	if($serializedJob) {
		execute_job($serializedJob);
	}
}

function execute_job($serializedJob)
{
	$redis = new Client();

	// Add into resvered queue
	$redis->zadd(
		'queue:resvered',
		Carbon::now()->timestamp,
		$serializedJob
	);

	$job = unserialize($serializedJob);

	try {
		// Execute job
		$job->handle();

	} catch (Throwable $e) {
		// Job failed
		// For continue to attempt
		// Push back to resvered queue
		$redis->zadd(
			'queue:delayed',
			$job->makeStartTime(),
			$serializedJob
		);

		echo "Excute Time : "
		. ((string) Carbon::now())
		. " \ Execute job failed.\n";
	}

	// Job of thie time is done
	// Remove from resvered queue
	$redis->zrem('queue:resvered', $serializedJob);
}

while(true) {
	queue_delayed_job();
	queue_job();
	sleep(1);
}