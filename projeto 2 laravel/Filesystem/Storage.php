<?php

namespace App\Filesystem;

use Illuminate\Support\Facades\Storage as SystemStorage;

/**
 * @method static \Illuminate\Contracts\Filesystem\Filesystem disk(string $name = null)
 *
 * @see \Illuminate\Filesystem\FilesystemManager
 */
class Storage extends SystemStorage
{
    /**
     * Create modules storage images
     *
     * @param  string|null  $disk
     *
     * @return void
     */
    public static function storeImage($request, $module, $filename = 'image')
    {
        if ($request->hasfile($filename) && $request->file($filename)->isValid()) {

            // Define um aleatório para o arquivo baseado no timestamps atual
            $unique_image_name = uniqid(date('HisYmd'));

            // Recupera a extensão do arquivo
            $extension = $request->image->extension();

            // Define finalmente o nome
            $image_name = "{$unique_image_name}.{$extension}";

            // Salva a imagem no banco
            if(!$request->image->storeAs($module, $image_name)) {

                return redirect()
                        ->back()
                        ->with('error', 'Falha ao fazer upload da imagem')
                        ->withInput();
            }

            return $image_name;
        }

        return false;
    }
}