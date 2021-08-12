<?php


namespace App\Services\Admin\SocialTecnologies;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\File;

class SocialTecnologyService
{
    /**
     * @param string $path
     * @param string $ext
     * @return mixed
     */
    public function base64Parser($path, $ext) : string
    {
        $base = base64_encode($path);

        return 'data:image/'. $ext .';base64,'. $base;
    }

    /**
     * @param SocialTecnology $model
     * @param string $image_name
     * @return mixed
     */
    public function getSocialTecnologyImageFile($model, $image_name) : string
    {
        try {
            return Storage::get('socialtecnologies/'.$model->id.'/images/'.$image_name);
        } catch (\Throwable $throwable) {
            // https://everyuseful.com/how-to-read-file-content-in-public-folder-with-laravel/
            return '';
        }
    }

    /**
     * @param SocialTecnology $model
     * @param string $image_name
     * @return string
     */
    public function getSocialTecnologyRelativeImagePaht($model, $image_name) : string
    {
        try {
            return asset('storage/socialtecnologies/'.$model->id.'/images/'.$image_name);
        } catch(\Throwable $throwable) {
            return asset('img/no_image.png');
        }
    }

    /**
     * @param string $file
     * @return mixed
     */
    public function getExtenssion($file) : string
    {
        $ext = explode('.', $file);

        return end($ext);
    }

    /**
     * @param SocialTecnology $model
     * @return bool
     */
    public function tecnologyHasImages($model) : bool
    {
        if($model->images) {
            return true;
        }

        return false;
    }

    /**
     * @param SocialTecnology $model
     * @param Closure $callback
     * @return Collection
     */
    public function getSocialTecnologyBase64Images($model) : Collection
    {
        $result = Collection::make();

        if($this->tecnologyHasImages($model)) {
            foreach($model->images as $image) {
                $base = $this->base64Parser(
                    $this->getSocialTecnologyImageFile($model, $image->image),
                    $this->getExtenssion($image->image)
                );

                $arr_aux = [
                    'id' => $image->id,
                    'image' => $image->image,
                    'extension' => $this->getExtenssion($image->image),
                    'base64' => $base,
                    'relativePath' => $this->getSocialTecnologyRelativeImagePaht($model, $image->image),
                ];

                $result->push($arr_aux);

                unset($arr_aux);
            }
        }

        return $result;
    }
}
