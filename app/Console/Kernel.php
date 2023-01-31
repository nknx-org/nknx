<?php

namespace App\Console;

use Carbon\Carbon;
use App\Models\Node;
use App\Models\User;

use App\Models\Wallet;
use App\Jobs\UpdateNode;
use App\Jobs\SnapshotNode;
use App\Jobs\UpdateWallet;
use App\Jobs\SnapshotWallet;
use App\Models\NodeSnapshot;
use App\Jobs\CheckAndHedgeNKN;
use App\Models\WalletSnapshot;
use Illuminate\Support\Facades\DB;
use App\Models\EasyTransferConnection;
use App\Notifications\SubscriptionSoonEnds;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        // The main command that syncs the latest blockchain items
        $schedule->command('blockchain:sync')->everyMinute()->runInBackground()->onOneServer();

        $schedule->call(function () {
            EasyTransferConnection::where('last_active', '<', Carbon::now()->subMinute(1))->delete();
        })->everyMinute()->name('EasyTransferCleanUp')->withoutOverlapping()->onOneServer();

        $schedule->call(function () {

            $users = User::all();
            foreach ($users as $user) {
                $nodes = $user->nodes()->where([
                    ['nodes.updated_at', '>', Carbon::now()->subDay(1)]
                ])->get();

                foreach ($nodes as $node) {
                    UpdateNode::dispatch($node);
                }
            }
            // better update logic in UpdateNode (depending of last update date)

        })->everyTwoMinutes()->name('UpdateAllNodes')->withoutOverlapping()->onOneServer();

        // $schedule->call(function(){
        //     CheckAndHedgeNKN::dispatch()->onQueue('nodeIdPaymentProcessor');
        // })->everyFiveMinutes()->name('CheckAndHedgeNKN')->withoutOverlapping()->onOneServer();

        //ONCE PER HOUR

        $schedule->call(function () {
            $nodesToDelete = Node::doesntHave('users')->pluck('id')->toArray();
            Node::destroy($nodesToDelete);
        })->hourly()->name('CleanUpOrphanedNodes')->withoutOverlapping()->onOneServer();

        $schedule->call(function () {
            $walletsToDelete = Wallet::doesntHave('users')->pluck('id')->toArray();
            Wallet::destroy($walletsToDelete);
        })->hourly()->name('CleanUpOrphanedWallets')->withoutOverlapping()->onOneServer();

        //DAILY
        $schedule->call(function () {
            $wallets = Wallet::all();
            foreach ($wallets as $wallet) {
                UpdateWallet::dispatch($wallet);
            }
        })->everySixHours()->name('UpdateAllWallets')->withoutOverlapping()->onOneServer();


        //DAILY

        $schedule->call(function () {
            $wallets = Wallet::all();
            foreach ($wallets as $wallet) {
                SnapshotWallet::dispatch($wallet);
            }
        })->daily()->name('SnapshotWallets')->withoutOverlapping()->onOneServer();


        $schedule->call(function () {
            $nodes = Node::all();
            foreach ($nodes as $node) {
                NodeSnapshot::firstOrCreate(
                    ['node_id' => $node->id, 'created_at' => new \DateTime()],
                    ["mined" => 0]
                );
            }
        })->daily()->name('CreateNodeSnapshots')->withoutOverlapping()->onOneServer();

        $schedule->call(function () {
            DB::table('nodes')->update(array('daily_changes' => 0));
        })->daily()->name('ResetDailyChanges')->withoutOverlapping()->onOneServer();


        $schedule->call(function () {
            $nodes = [];
            $users = User::all();

            foreach ($users as $user) {
                $nodes = $user->nodes()->where([
                    ['nodes.updated_at', '<=', Carbon::now()->subDay(1)]
                ])->get();

                foreach ($nodes as $node) {
                    UpdateNode::dispatch($node);
                }
            }

        })->hourly()->name('UpdateOfflineNodes')->withoutOverlapping()->onOneServer();


        $schedule->call(function () {
            NodeSnapshot::where('created_at', '<', Carbon::now()->subMonths(3))->delete();
            WalletSnapshot::where('created_at', '<', Carbon::now()->subMonths(3))->delete();
            User::where('updated_at', '<', Carbon::now()->subDays(5))->whereNull('email_verified_at')->delete();
            DB::table('notifications')->where('created_at', '<', Carbon::now()->subWeek())->delete();
        })->daily()->name('CleanUp')->withoutOverlapping()->onOneServer();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
