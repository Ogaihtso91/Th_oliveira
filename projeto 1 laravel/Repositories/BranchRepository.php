<?php

namespace App\Repositories;

use App\Branch;
use App\BranchGallery;
use App\ServiceProvider;
use App\BusinessHour;

class BranchRepository extends BaseRepository
{
    private $model;
    private $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->model = new Branch;
        $this->contactRepository = $contactRepository;
    }

    public function findDetails($id)
    {
        $branch = $this->model->where('id', $id)->get()->first();
        if(!$branch) throw new \Exception('Filial não encontrada');
        return $branch;
    }

    public function first($id, $user)
    {
        $branch = $user->serviceProvider->branches()->where('branches.id', $id)->get()->first();
        if(!$branch) throw new \Exception('Filial não encontrada');
        return $branch;
    }

    public function save($data, $user)
    {
        $dataArray = is_array($data) ? $data : $data->all();
        $onlyDate = array_only($dataArray, ['name', 'address_id', 'address_number', 'cep', 'lat', 'lng']);
        $onlyDate['cep'] = preg_replace('/[^0-9]+/i', '', $onlyDate['cep']);
        //dd($onlyDate);
        if(empty($dataArray['id'])){
            $onlyDate['service_provider_id'] = $user->serviceProvider->id;
            $this->model = $this->model->create($onlyDate);
        } else {
            $this->model = $this->first($dataArray['id'], $user);
            $this->model->update($onlyDate);
        }

        if(!$this->model->serviceProvider->is_active) {
            $this->model->serviceProvider->update(['active' => \App\ServiceProvider::STATUS_ACTIVE]);
        }

        $this->model->paymentMethods()->sync(isset($data['branch_payment_method']) ? $data['branch_payment_method'] : []);

        if(isset($data['branch_business_hours'])){
            if($businessHour = $this->model->businessHour()->first()){
                $businessHour->update($data['branch_business_hours']);
            } else {
                $this->model->businessHour()->save( new BusinessHour($data['branch_business_hours']) );
            }
        }
        
        $this->model->services()->sync(isset($data['branch_services']) ? $data['branch_services'] : []);

        $this->contactRepository->save($this->model, isset($dataArray['contact']) ? $dataArray['contact'] : []);

        return $this->model;
    }

    public function findByLocationQuerystring($querystring)
    {
        $topLeft = $querystring['topLeft'];
        $bottomRight = $querystring['bottomRight'];
        $query = $querystring['query'];

        
        $branches = $this->model->with(['serviceProvider', 'businessHour', 'galleries', 'services', 'address', 'address.district', 'address.district.city', 'address.district.city.state']);

        $latmin = $querystring['topLeft']['lat'] < $querystring['bottomRight']['lat'] ? $querystring['topLeft']['lat'] : $querystring['bottomRight']['lat'];
        $latmax =  $querystring['topLeft']['lat'] > $querystring['bottomRight']['lat'] ? $querystring['topLeft']['lat'] : $querystring['bottomRight']['lat'];

        $lngmin = $querystring['topLeft']['lng'] < $querystring['bottomRight']['lng'] ? $querystring['topLeft']['lng'] : $querystring['bottomRight']['lng'];

        $lngmax =  $querystring['topLeft']['lng'] > $querystring['bottomRight']['lng'] ? $querystring['topLeft']['lng'] : $querystring['bottomRight']['lng'];

        $branches = $branches->whereBetween('lat', [ $latmin, $latmax]);
        $branches = $branches->whereBetween('lng', [ $lngmin, $lngmax]);

        if(isset($query['find_by']) && !empty($query['find_by'])) {
            $branches = $branches->whereHas('serviceProvider', function($q) use ($query) {
                $q->where('type_provider_id', $query['find_by']);
            });
        }        
        return $branches;
    }

    public function uploadGallery($file, $branch_id)
    {
        $image      = \Intervention\Image\Facades\Image::make($file->path());
        $extension  = $file->extension();
        if(!in_array($extension, ['png', 'jpeg', 'jpg']))
            throw new Exception('Formato Inválido');

        $horizontal = ($image->width() / $image->height() >= 1);
        
        if($horizontal){
            $image->resize(1920, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        } else {
            $image->resize(null, 1080, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }


        $filePath = join('.', [ 
            BranchGallery::getFolder($branch_id) . md5($file->getClientOriginalName() . date('Y-m-d H:i:s')),
            $extension
        ]);
        if(!is_dir(BranchGallery::getFolder($branch_id))) mkdir(BranchGallery::getFolder($branch_id), 0755, true);
        if($image->save($filePath, 70)){
            $fileName = explode('/', $filePath);
            $gallery = BranchGallery::create(['branch_id' => $branch_id, 'path' => end($fileName)])->toArray();

            $gallery['success'] = true;

            return $gallery;
        }
    }
    public function removeGallery($branch, $id)
    {
        $image = $branch->galleries->where('id', $id)->first();
        if(!$image) throw new \Exception('Imagem não localizada');

        if(file_exists($image->full_path)){
            unlink($image->full_path);
        }
        $image->delete();

        return true;
    }

    public function getNearestPlaces($lat, $lng, $params = [])
    {
        $places = $this->listByPosition(
            'branches.lat', $lat,
            'branches.lng', $lng,
            10,
            $this->model
        )->with(['serviceProvider','address.district'])->whereHas('serviceProvider', function($q) {
            $q->where('ACTIVE', ServiceProvider::STATUS_ACTIVE);
        })
        ->orderByRaw('TRUNCATE(distance, 2) ASC, branches.created_at DESC');
        if(isset($params['limit']))
            $places->limit($params['limit']);

        return $places;
    }
}