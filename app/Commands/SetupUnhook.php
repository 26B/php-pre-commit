<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Process as FacadesProcess;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Process;

use function Termwind\{render};

class SetupUnhook extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'setup:unhook';

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
        // Remove command line from pre-commit hook file
            // Handle rusty git hooks

        $process = new Process(['git','config','core.hooksPath']);
        $process->run();
        if ( ! $process->isSuccessful() && $process->getExitCode() !== 1 ) {
            $this->error( "Error getting hooks path." );
            return;
        }
        $hooksPath = trim( $process->getOutput() );
        if ( empty( $hooksPath ) ) {
            $hooksPath = '.git/hooks';
        }

        // Check if it's a husky directory.
        if ( str_ends_with( $hooksPath, '.husky/_' ) ) {
            $hooksPath = preg_replace( '/\.husky\/_$/', '.husky', $hooksPath );
        }

        $this->info( "Hooks path: $hooksPath" );

        // Check if file exists.
        if ( file_exists( $hooksPath . '/pre-commit' ) ) {
            $this->info( "Cleaning pre-commit file..." );
            $pre_commit = file_get_contents( $hooksPath . '/pre-commit', '' );
            // TODO: this needs to be done in a better way.
            $pre_commit = str_replace( "\n./src/pre-commit", '', $pre_commit );
            if ( trim( $pre_commit ) !== '' ) {
                file_put_contents( $hooksPath . '/pre-commit', $pre_commit );

            } else {
                $answer = $this->ask( "Pre-commit file is empty. Remove it? (y/n)" );
                if ( strtolower( $answer ) === 'y' ) {
                    unlink( $hooksPath . '/pre-commit' );
                }
            }

        } else {
            $this->info( "No pre-commit file found." );
        }

        // Remove lines from composer json
        $this->remove_lines_to_composer_json();
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }

    private function remove_lines_to_composer_json() : void {
        $composer_file_path = \Composer\Factory::getComposerFile();
        $composer           = file_get_contents( $composer_file_path );
        $composer           = json_decode( $composer );
        if ( ! is_object( $composer ) ) {
            $this->error( "Error reading composer.json" );
            return;
        }

        $update = false;

        if ( isset( $composer->scripts ) ) {
            if ( isset( $composer->scripts->{'post-install-cmd'} ) ) {
                // TODO: handle when there's other entries
                unset( $composer->scripts->{'post-install-cmd'} );
                $update = true;
            }

            if ( isset( $composer->scripts->{'post-update-cmd'} ) ) {
                // TODO: handle when there's other entries
                unset( $composer->scripts->{'post-update-cmd'} );
                $update = true;
            }
        }

        if ( ! $update ) {
            $this->info( "No scripts found in composer.json." );
            return;
        }

        $this->info( "Removing scripts from composer.json..." );

        // FIXME: pretty print opens up the json arrays, we don't want that.
        file_put_contents( $composer_file_path, json_encode( $composer, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES ) );
    }
}
