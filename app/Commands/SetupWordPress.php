<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Process;
use LaravelZero\Framework\Commands\Command;

use function Termwind\{render};

class SetupWordPress extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'setup:wordpress';

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
        $composer_file     = \Composer\Factory::getComposerFile();
        $result            = Process::run("pwd", function (string $type, string $output) {});
        $working_dir       = trim( $result->output() );
        $composer_dir      = dirname( $composer_file );
        $full_composer_dir = $working_dir . '/' . $composer_dir;

        // install wordpress coding standards
        $this->info( "Allowing phpcodesniffer-composer-installer..." );
        $result = Process::run('composer config allow-plugins.dealerdirect/phpcodesniffer-composer-installer true', function (string $type, string $output) {
            //echo "\t" . $output;
        });

        if ( ! $result->successful() ) {
            $this->error( "Error allowing phpcodesniffer-composer-installer." );
            return;
        }

        // Ask version (empty means latest)
        $version = $this->ask( "Which version of WordPress standards would you like to install? (Empty for latest)" );
        if ( ! empty( $version ) ) {
            $this->info( "Installing WordPress standards version {$version}..." );
            $version = ":\"{$version}\"";

        } else {
            $this->info( "Installing latest WordPress standards..." );
        }

        $result = Process::run("composer require --dev wp-coding-standards/wpcs{$version}", function (string $type, string $output) {
            // echo "\t" . $output;
        });

        // TODO: output which version was installed

        if ( ! $result->successful() ) {
            $this->error( "Error installing WordPress standards." );
            return;
        }

        // Add path to standards to phpcs
        $this->info( "Adding path to standards..." );

        $path   = $full_composer_dir . '/vendor/wp-coding-standards/wpcs';
        $result = Process::run("{$full_composer_dir}/vendor/bin/phpcs --config-set installed_paths {$path}", function (string $type, string $output) {
            echo "\t" . $output;
        });

        if ( ! $result->successful() ) {
            $this->error( "Error adding path to standards." );
            return;
        }

        // Validate phpcs standards.
        $result = Process::run("{$full_composer_dir}/vendor/bin/phpcs -i", function (string $type, string $output) {});
        if ( str_contains( $result->output(), 'WordPress' ) ) {
            $this->info( "WordPress standards installed." );
        } else {
            $this->error( "Error installing WordPress standards." );
        }
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
