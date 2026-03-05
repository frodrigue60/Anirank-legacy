<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateImages extends Command
{
    protected $signature = 'migrate:images';
    protected $description = 'Migrate existing polymorphic images to direct table columns';

    public function handle()
    {
        $this->info('Starting image migration to direct columns...');

        $images = \App\Models\Image::all();

        foreach ($images as $image) {
            $modelClassStr = $image->imageable_type;
            
            // Map legacy Post model to Anime
            if ($modelClassStr === 'App\Models\Post') {
                $modelClassStr = \App\Models\Anime::class;
            }

            $modelId = $image->imageable_id;
            
            // Si el modelo o el ID no existen, continuamos
            if (!$modelClassStr || !$modelId) continue;

            if (!class_exists($modelClassStr)) {
                $this->warn("Class does not exist: {$modelClassStr}");
                continue;
            }

            $model = $modelClassStr::find($modelId);
            if (!$model) continue;

            $type = $image->type;
            $path = $image->path; // We assume the default disk or just the path string as requested by the user

            // Mapeo dependiendo del modelo y tipo
            switch ($modelClassStr) {
                case \App\Models\Anime::class:
                    if ($type === 'thumbnail' || $type === 'cover') {
                        $model->cover = $path;
                    } elseif ($type === 'banner') {
                        $model->banner = $path;
                    }
                    break;

                case \App\Models\Artist::class:
                    if ($type === 'thumbnail' || $type === 'avatar') {
                        $model->avatar = $path;
                    }
                    break;

                case \App\Models\User::class:
                    if ($type === 'avatar') {
                        $model->avatar = $path;
                    } elseif ($type === 'banner') {
                        $model->banner = $path;
                    }
                    break;

                case \App\Models\Badge::class:
                    if ($type === 'icon') {
                        $model->icon = $path;
                    }
                    break;
                
                default:
                    // Log or handle unknown types
                    $this->warn("Unknown imageable_type: {$modelClassStr}");
                    continue 2;
            }

            // Desactivamos timestamps para no modificar updated_at durante la migración
            $model->timestamps = false;
            $model->save();
        }

        $this->info('Image migration completed successfully!');
    }
}
