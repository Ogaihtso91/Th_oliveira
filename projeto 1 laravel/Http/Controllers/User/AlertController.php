<?php

namespace App\Http\Controllers\User;

use App\Repositories\AlertRepository;
use App\Repositories\SpeciesRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Pet;

class AlertController extends UserBaseController
{
    private $alertRepository;
    private $speciesRepository;

    public function __construct(AlertRepository $alertRepository,
    SpeciesRepository $speciesRepository)
    {
        parent::__construct();
        $this->alertRepository = $alertRepository;
        $this->speciesRepository = $speciesRepository;
    }

    public function list(Request $r)
    {
        $alerts = $this->user->alerts()->paginate(10);
        return view('user.alert.list', compact('alerts'));
    }

    public function add(Request $r)
    {
        if($r->isMethod('post')){
            try {
                $data = $r->get('alert');
                $photo = $r->file('alert')['photo'];
                $alert = $this->alertRepository->create($data, $photo, $this->user);
                return redirect()->route('site.alerts.detail', ['id' => $alert->slug])->with('success', 'Alerta criado com sucesso');
            } catch(\Exception $e) {
                return redirect()->back()->withInput($r->all())->with('error', $e->getMessage() . $e->getFile() . $e->getLine());
            }
        }

        $species = $this->speciesRepository->getList()->toArray();
        $pets = $this->user->pets()->get()->pluck('name','id')->toArray();
        $genders = Pet::GENDERS;
        return view('user.alert.add', compact('pets', 'genders', 'species'));
    }

    
    public function action(Request $r, $id, $action)
    {
        try {
            $alert = $this->alertRepository->findById($id, $this->user);
            $alert->update(['status' => $action == 'fechar' ? \App\PetAlert::CLOSED : \App\PetAlert::OPEN]);
            return redirect()->back()->with('success', 'Alerta ' . ( $action == 'fechar' ? 'fechado' : 'reaberto') . ' com sucesso!');
        } catch(\Exception $e) {
            return redirect()->back()->withInput($r->all())->with('error', $e->getMessage() . $e->getFile() . $e->getLine());
        }
    }

   
}
