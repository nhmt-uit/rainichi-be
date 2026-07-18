<?php

namespace App\Jobs;

use App\Service\PushNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FCMJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $device_tokens;
    protected $title;
    protected $body;
    protected $all;


    /**
     * FCMJob constructor.
     * @param $device_tokens
     * @param $title
     * @param $body
     * @param $all
     */
    public function __construct($device_tokens, $title, $body, $all = false)
    {
        $this->device_tokens = $device_tokens;
        $this->title = $title;
        $this->body = $body;
        $this->all = $all;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $push = new PushNotification(
            $this->device_tokens,
            $this->title,
            $this->body,
            $this->all
        );
        $push->trigger();
    }
}
