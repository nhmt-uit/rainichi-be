<?php

namespace App\Console\Commands;

use App\Models\CourseClass;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\ConsoleOutput;

class ExpiredCourseClass extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rainichi:course_class:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update expired status is TRUE each 1 hours/times';

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
        $courseClass = CourseClass::query()->whereNotNull('expired_date')
            ->whereRaw('expired_date < NOW()')->update(['is_expired' => true]);
        $output = new ConsoleOutput();
        $output->writeln("<info>Done, Updated expired: {$courseClass} items</info>");
    }
}
