<?php

namespace App\Console\Commands;

use App\Jobs\FCMJob;
use App\Models\ListUserByGroupUser;
use App\Models\Notification;
use App\Models\NotificationGroup;
use App\Models\UserTokenDevice;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\ConsoleOutput;

class PushNotifyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rainichi:push {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push notification by command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id = $this->argument('id');
        $notification = Notification::query()->find($id);
        if ($notification) {
            $token = [];
            $all = false;
            // Check type of notification
            switch ($notification->send_to) {
                case Notification::NOTIFICATION_SEND_TO['ALL']:
                    $all = true;
                    break;
                case Notification::NOTIFICATION_SEND_TO['GROUP']:
                    $group_id = $notification->group_id;
                    $user_in_group = ListUserByGroupUser::query()->where('group_user_id', $group_id)->pluck('user_id');
                    $token = UserTokenDevice::query()->whereIn('user_id', $user_in_group)->pluck('token_device');
                    break;
                case Notification::NOTIFICATION_SEND_TO['LIST_USER']:
                    $user_in_group = NotificationGroup::query()->where('notification_id', $id)->pluck('user_id');
                    $token = UserTokenDevice::query()->whereIn('user_id', $user_in_group)->pluck('token_device');
                    break;
            }
            // Push notification
            dispatch(new FCMJob($token, $notification->title, $notification->description, $all));

            // Remove notification one time in the cron tab file after it done.
            if ($notification->type === Notification::NOTIFICATION_TYPE['ONETIME']) {
                shell_exec("sudo crontab -l | grep -v 'php /var/www/html/backend/artisan rainichi:push $id' | crontab -");
                $notification->delete();
            }
        }
        $output = new ConsoleOutput();
        $output->writeln("<info>Done: $id</info>");
    }
}
