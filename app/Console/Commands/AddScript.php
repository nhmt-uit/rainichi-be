<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Output\ConsoleOutput;

class AddScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rainichi:cron-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'push data to cron tab';

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
        $a = '30 12 * * *';
        $b = 30;
        shell_exec("{ crontab -l; echo \"$a php /var/www/html/backend/artisan rainichi:push $b\"; } | crontab -");
        $output = shell_exec('crontab -l');
        $result = new ConsoleOutput();
        $result->writeln("<info>Done: $output</info>");
    }
}
