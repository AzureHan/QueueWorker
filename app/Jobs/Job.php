<?php

namespace App\Jobs;

require 'vendor/autoload.php';

use Carbon\Carbon;

class Job
{
	protected $createTime;

	public function __construct()
	{
		$this->createTime = (string) Carbon::now();
	}

	public function handle()
	{
		echo "{$this->createTime} : TestJob@handel()\n";
	}
}