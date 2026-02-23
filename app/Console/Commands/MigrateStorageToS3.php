<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateStorageToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:migrate-to-s3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate files from local public disk to S3 (MinIO)';

    public function handle()
    {
        $localDisk = \Storage::disk('public');
        $s3Disk = \Storage::disk('s3');

        $this->info('Starting migration to S3...');

        $files = $localDisk->allFiles();
        $total = count($files);

        if ($total === 0) {
            $this->warn('No files found in local public storage.');

            return;
        }

        $this->info("Found {$total} files to migrate.");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($files as $file) {
            try {
                if (! $s3Disk->exists($file)) {
                    $content = $localDisk->get($file);
                    $s3Disk->put($file, $content);
                }
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Failed to migrate [{$file}]: ".$e->getMessage());
                $this->info('Ensure the bucket "anirank" exists in MinIO.');

                return;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Migration completed successfully!');
    }
}
