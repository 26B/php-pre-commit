<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Process as FacadesProcess;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Process;

use function Termwind\{render};

class SetupHook extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'setup:hook';

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
        // TODO: check if in a git repo first.

        // Add command line to pre-commit hook file (create file if needed)
            // Handle husky git hooks

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
        if ( ! file_exists( $hooksPath . '/pre-commit' ) ) {
            $this->info( "Creating hooks pre-commit file..." );
            file_put_contents( $hooksPath . '/pre-commit', '' );
        }

        if ( $this->is_line_already_in_file( $hooksPath . '/pre-commit', './src/pre-commit' ) ) {
            $this->info( "php-pre-commit already in pre-commit file." );
        } else {
            $this->info( "Add php-pre-commit line to pre-commit..." );
            file_put_contents(
                $hooksPath . '/pre-commit',
                // TODO: how to deal with permissions? chmod +x
                // TODO: add comment above line
                "\n./src/pre-commit",
                FILE_APPEND
            );
        }

        // Add lines to composer json
        $this->add_lines_to_composer_json();
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }

    private function is_line_already_in_file( string $file, string $wanted_line ) : bool {
        $handle = fopen($file, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $line = trim( $line );
                if ( $line === $wanted_line ) {
                    return true;
                }
            }

            fclose($handle);
        }

        return false;
    }

    private function add_lines_to_composer_json() : void {
        $composer_file_path = \Composer\Factory::getComposerFile();
        $composer      = file_get_contents( $composer_file_path );
        $composer      = json_decode( $composer );
        if ( ! is_object( $composer ) ) {
            $this->error( "Error reading composer.json" );
            return;
        }

        $update = false;

        if ( ! isset( $composer->scripts ) ) {
            $composer->scripts = (object) [
                'post-install-cmd' => 'vendor/bin/php-pre-commit setup:hook',
                'post-update-cmd' => 'vendor/bin/php-pre-commit setup:hook',
            ];
            $update = true;
        } else {
            // TODO: handle arrays
            if ( ! isset( $composer->scripts->{'post-install-cmd'} ) ) {
                $composer->scripts->{'post-install-cmd'} = 'vendor/bin/php-pre-commit setup:hook';
                $update = true;
            }

            if ( ! isset( $composer->scripts->{'post-update-cmd'} ) ) {
                $composer->scripts->{'post-update-cmd'} = 'vendor/bin/php-pre-commit setup:hook';
                $update = true;
            }
        }

        if ( ! $update ) {
            $this->info( "Composer.json already has scripts." );
            return;
        }

        $this->info( "Updating composer.json..." );

        // FIXME: pretty print opens up the json arrays, we don't want that.
        file_put_contents( $composer_file_path, json_encode( $composer, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES ) );
    }
}
