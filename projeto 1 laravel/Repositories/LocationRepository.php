<?php
namespace App\Repositories;
use App\TypeProvider;
use App\State;
use App\City;
use App\District;
use App\Address;
use Illuminate\Support\Facades\DB;

class LocationRepository {

    private $state;
    private $city;
    private $district;
    private $address;

    public function __construct()
    {
        $this->state    = new State;
        $this->city     = new City;
        $this->district = new District;
        $this->address  = new Address;
    }

    public function getStates()
    {
        return $this->state->orderBy('name','ASC')->pluck('name','id');
    }

    public function getCities($state_id = null)
    {
        if(is_null($state_id)){
            return $this->city->orderBy('name','ASC')->pluck('name','id');
        } else {
            return $this->city->where('state_id', $state_id)->orderBy('name','ASC')->pluck('name','id');
        }
    }

    public function getDistricts($city_id = null)
    {
        if(is_null($city_id)){
            return $this->district->orderBy('name','ASC')->pluck('name','id');
        } else {
            return $this->district->where('city_id', $city_id)->orderBy('name','ASC')->pluck('name','id');
        }
    }

    public function getAddresses($district_id = null)
    {
        if(is_null($district_id)){
            return $this->address->orderBy('name','ASC')->pluck('name','id');
        } else {
            return $this->address
            ->select(DB::raw("CONCAT(type, ' ', name) as full_name", "id"))
            ->where('district_id', $district_id)->orderBy('full_name','ASC')->pluck('full_name','id');
        }
    }

    public function addLocation($user, $location)
    {
        return $user->locations()->create($location);
    }

    public function removeLocation($user, $id){
        $location = $user->locations()->where('id', $id)->first();
        
        if(!$location) throw new \Exception('Local não encontrado');
        
        return $location->delete();
    }
}