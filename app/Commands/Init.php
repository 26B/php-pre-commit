<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Support\Facades\Process;

use function Termwind\{render};

class Init extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'init';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Require/install codesniffer (ask if local or global)
        $this->info( "Installing phpcs..." );

        Process::run('composer require --dev squizlabs/php_codesniffer', function (string $type, string $output) {
            echo "\t" . $output;
        });

        // Check if already required.
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
