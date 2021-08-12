<?php

namespace App\Http\Controllers\User;

use App\Repositories\PetRepository;
use App\Repositories\VaccineRepository;
use App\Repositories\SpeciesRepository;
use Illuminate\Http\Request;
use App\Pet;
use App\Vaccine;
use Illuminate\Support\Facades\DB;

class VaccineController extends UserBaseController
{
    private $petRepository;
    private $vaccineRepository;

    public function __construct(PetRepository $petRepository, VaccineRepository $vaccineRepository)
    {
        parent::__construct();
        $this->petRepository = $petRepository;
        $this->vaccineRepository = $vaccineRepository;
    }

    public function list(Request $r, $slug)
    {
        $tmp = explode('-', $slug); $id = end($tmp);
        try {
            $pet = $this->petRepository->first($id, $this->user);
            $vaccines = $pet->vaccines;
        } catch(\Exception $e) {
            return redirect()->route('user.pets.index')->with('error', $e->getMessage());
        }
        $this->setMeta('page_title', 'Vacinas');

        return view('user.pets.vaccines.list', compact('vaccines','pet'));
    }

    public function add(Request $r, $slug)
    {
        $tmp = explode('-', $slug); $id = end($tmp);
        try {
            $pet = $this->petRepository->first($id, $this->user);
            $vaccine = $this->vaccineRepository->save($pet, array_only($r->get('vaccine'), ['name', 'date', 'remember_date', 'comment']));
            return redirect()->back()->with('success', 'Vacina adicionada com Sucesso!');
        } catch(\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function remove(Request $r, $slug, $vaccine_id)
    {
        $tmp = explode('-', $slug); $id = end($tmp);
        try {
            $pet = $this->petRepository->first($id, $this->user);
            $this->vaccineRepository->remove($pet, $vaccine_id);
            return redirect()->back()->with('success', 'Vacina apagada com Sucesso!');
        } catch(\Exception $e){
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

}
