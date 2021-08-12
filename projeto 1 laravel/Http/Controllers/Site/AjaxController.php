<?php
namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Breed;
use App\State;
use App\Branch;
use App\City;
use App\Contact;
use App\District;
use App\Address;
use App\Repositories\BranchRepository;
use App\Repositories\AlertRepository;
//const API_TOKEN = '1f4f1d35afae2a';
const API_TOKEN = 'AIzaSyC_UN3I_F9htbi-nvFQOayxgH0zuzwxarg';
//const API_URL   = 'https://us1.locationiq.com/v1/search.php?key=#TOKEN#&q=#ADDRESS#&format=json';
const API_URL   = 'https://maps.googleapis.com/maps/api/geocode/json?address=#ADDRESS#&key=#TOKEN#';

class AjaxController extends SiteBaseController
{

    private $branchRepository;
    private $alertRepository;

    public function __construct(BranchRepository $branchRepository, AlertRepository $alertRepository)
    {
        $this->branchRepository = $branchRepository;
        $this->alertRepository = $alertRepository;
    }

    public function resource(Request $r, $resource)
    {
        $id     = $r->input('id');
        $return[] = ['id' => '', 'value' => 'Selecione...'];
        switch($resource) {
            case 'breed':
                foreach(Breed::where('species_id', $id)->get() as $r)
                $return[] = ['id' => $r->id, 'value' => $r->name];
                break;
            case 'state': 
                foreach(State::where('id', $id)->get() as $r)
                $return[] = [
                                'id' => $r->id,
                                'value' => $r->name,
                                'lat' => empty($r->lat) ? '' : $r->lat,
                                'lng' => empty($r->lng) ? '' : $r->lng
                            ];
                break;
            case 'city': 
                foreach(City::where('state_id',
                             $id)->get() as $r)
                $return[] = [
                                'id' => $r->id,
                                'value' => $r->name,
                                'lat' => empty($r->lat) ? '' : $r->lat,
                                'lng' => empty($r->lng) ? '' : $r->lng
                            ];
                break;
            case 'district': 
                foreach(District::where('city_id',
                             $id)->get() as $r)
                $return[] = [
                                'id' => $r->id,
                                'value' => $r->name,
                                'lat' => empty($r->lat) ? '' : $r->lat,
                                'lng' => empty($r->lng) ? '' : $r->lng
                            ];
                break;
            case 'address': 
                foreach(Address::where('district_id',
                             $id)->get() as $r)
                $return[] = [
                                'id' => $r->id,
                                'value' => $r->address_name,
                                'lat' => empty($r->lat) ? '' : $r->lat,
                                'lng' => empty($r->lng) ? '' : $r->lng
                            ];
                break;
        }
        return $return;
    }

    public function view(Request $r, $id)
    {
        switch($id):
            case 'contacts':
                $contact = new Contact;
                return view('serviceProvider.partials.contact', ['index' => !empty($r->input('index')) ? $r->input('index') : 0])->with([ 'typeContacts' => Contact::typeContacts(), 'contact' => $contact->fill(['type' => Contact::EMAIL]) ]);
            default:
            return response()->json(['message' => 'View não informada'])->setStatusCode(400);
        endswitch;
    }


    public function findServiceProviders(Request $r)
    {
        $data = $r->all();
        $serviceProviderBranches = $this->branchRepository->findByLocationQuerystring($data)->get();
        return response()->json(['places' => $serviceProviderBranches, 'count' => count($serviceProviderBranches)])->setStatusCode(200);
    }

    public function retrieveGps(Request $r)
    {
        $curl           = curl_init();
        $address        = $r->input('address');
        try {
            curl_setopt_array($curl, array(
                CURLOPT_URL => str_replace('#ADDRESS#', urlencode($address), str_replace('#TOKEN#', API_TOKEN, API_URL)),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array('Content-Type: application/json')
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            $response   = curl_exec($curl);
            $err        = curl_error($curl);
            curl_close($curl);
            if ($err) {
                if(empty($gps)) throw new Exception('Erro ao recuperar localização do endereço [1]');
            } else {
                $data = json_decode($response, true);
            }
            if(json_last_error() !== JSON_ERROR_NONE) {
                if(empty($gps)) throw new Exception('Erro ao recuperar localização do endereço [2]');
            } else {
                if(!isset($data['results'][0]['geometry']['location'])) throw new \Exception('Endereço não localizado');
                $gps = [
                    'latitude' => $data['results'][0]['geometry']['location']['lat'],
                    'longitude' => $data['results'][0]['geometry']['location']['lng']
                ];
            }
            return response()->json($gps)->setStatusCode(200);
        } catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage()])->setStatusCode(500, $e->getMessage());
        }
    }

    public function gps(Request $r)
    {
        $curl           = curl_init();
        $address_id     = $r->input('address_id');
        $address        = $r->input('full_address');
        try {
            $address_model  = Address::where('id', $address_id)->first();
            $gps = ($address_model) ? $address_model->getGps(true) : [];
            curl_setopt_array($curl, array(
                CURLOPT_URL => str_replace('#ADDRESS#', urlencode($address), str_replace('#TOKEN#', API_TOKEN, API_URL)),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array('Content-Type: application/json')
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            $response   = curl_exec($curl);
            $err        = curl_error($curl);
            curl_close($curl);
            if ($err) {
                if(empty($gps)) throw new Exception('Erro ao recuperar localização GPS [1]');
            } else {
                $data = json_decode($response, true);
            }
            if(json_last_error() !== JSON_ERROR_NONE) {
                if(empty($gps)) throw new Exception('Erro ao recuperar localização GPS [2]');
            } else {
                if(!isset($data['results'][0]['geometry']['location'])) throw new \Exception('Endereço não localizado');
                $gps = [
                    'latitude' => $data['results'][0]['geometry']['location']['lat'],
                    'longitude' => $data['results'][0]['geometry']['location']['lng']
                ];
                if(empty($address_model->gps)){
                    $address_model->update(['lat' => $gps['latitude'], 'lng' => $gps['longitude']]);
                }
            }
            return response()->json($gps)->setStatusCode(200);
        } catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage()])->setStatusCode(500, $e->getMessage());
        }
    }


    public function getAlerts(Request $r)
    {
        $positions = $r->all();
        $alerts = $this->alertRepository->getAlerts($positions['lat'], $positions['lng'], ['limit' => 5])->get();
        return response()->json(['alerts' => $alerts], 200);
    }

    public function getNearestPlaces(Request $r)
    {
        $positions = $r->all();
        $places = $this->branchRepository->getNearestPlaces($positions['lat'], $positions['lng'], ['limit' => 5])->get();
        return response()->json(['nearest_places' => $places], 200);
    }
}