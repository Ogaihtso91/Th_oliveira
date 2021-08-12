<?php

namespace App\Http\Controllers\User;

use App\Repositories\LocationRepository;
use Illuminate\Http\Request;
use App\UserPosition;
use Illuminate\Support\Facades\DB;

class LocationController extends UserBaseController
{
    private $locationRepository;

    public function __construct(LocationRepository $locationRepository)
    {
        parent::__construct();
        $this->locationRepository = $locationRepository;
    }

    public function list(Request $r)
    {
        try {
            $locations = $this->user->locations;
        } catch(\Exception $e) {
            return redirect()->route('user.index')->with('error', $e->getMessage());
        }
        $this->setMeta('page_title', 'Meus Locais');

        return view('user.locations.list', compact('locations'));
    }

    public function add(Request $r)
    {
        try {
            $location = $this->locationRepository->addLocation($this->user, $r->get('location'));
            return redirect()->back()->with('success', 'Local adicionado com Sucesso!');
        } catch(\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function remove(Request $r, $id)
    {
        try {
            $location = $this->locationRepository->removeLocation($this->user, $id);
            return redirect()->back()->with('success', 'Local removido com Sucesso!');
        } catch(\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }


}
