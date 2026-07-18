<?php

namespace App\Console\Commands;

use App\Models\OauthAccessToken;
use App\Models\OrderPayment;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\ConsoleOutput;

class PruneOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rainichi:order:prune';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove order with IN_PROGRESS status after 24h';

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
        $order = OrderPayment::query()
            ->where('payment_status', OrderPayment::INPROGRESS)
            ->whereNotIn('payment_method', [OrderPayment::OFFLINE, OrderPayment::TRANSFER])
            ->whereNull('payment_method')
            ->whereRaw('TIMESTAMPDIFF(MINUTE,created_at ,CURDATE())>=' . env('TIME_PRUNE'))->forceDelete();
        $tokenRevoked = OauthAccessToken::query()->where('revoked', true)->delete();
        $output = new ConsoleOutput();
        $output->writeln("<info>Done, deleted: {$order} items</info>");
        $output->writeln("<info>Done, deleted: {$tokenRevoked} tokens</info>");
    }
}
