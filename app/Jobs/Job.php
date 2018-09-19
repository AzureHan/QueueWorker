<?php

namespace App\Jobs;

require 'vendor/autoload.php';

use Carbon\Carbon;

class Job
{
	protected $createTime;

	protected $delay;

	public function __construct($delay = 0)
	{
		$this->createTime = (string) Carbon::now();

		$this->delay = $delay;
	}

	public function shouldDelay()
	{
		return intval($this->delay) > 0;
	}

	public function makeStartTime()
	{
		return Carbon::now()->addSeconds($this->delay)->timestamp;
	}

	public function handle()
	{
		echo "Excute Time : "
		. ((string) Carbon::now())
		. " \ Dispatch Time : {$this->createTime} \n";
	}
}