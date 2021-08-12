<?php

namespace App\Http\Controllers\User;

use App\Repositories\PetRepository;
use App\Repositories\MedicineRepository;
use App\Repositories\SpeciesRepository;
use Illuminate\Http\Request;
use App\Pet;
use App\Medicine;
use Illuminate\Support\Facades\DB;

class MedicineController extends UserBaseController
{
    private $petRepository;
    private $medicineRepository;

    public function __construct(PetRepository $petRepository, MedicineRepository $medicineRepository)
    {
        parent::__construct();
        $this->petRepository = $petRepository;
        $this->medicineRepository = $medicineRepository;
    }

    public function list(Request $r, $slug)
    {
        $tmp = explode('-', $slug); $id = end($tmp);
        try {
            $pet = $this->petRepository->first($id, $this->user);
            $medicines = $pet->medicines;
        } catch(\Exception $e) {
            return redirect()->route('user.pets.index')->with('error', $e->getMessage());
        }
        $this->setMeta('page_title', 'Medicamentos');

        return view('user.pets.medicines.list', compact('medicines','pet'));
    }

    public function add(Request $r, $slug)
    {
        $tmp = explode('-', $slug); $id = end($tmp);
        try {
            $pet = $this->petRepository->first($id, $this->user);
            $medicine = $this->medicineRepository->save($pet, array_only($r->get('medicine'), ['name', 'first_day', 'last_day',  'comment']));
            return redirect()->back()->with('success', 'Medicamento adicionado com Sucesso!');
        } catch(\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function remove(Request $r, $slug, $medicine_id)
    {
        $tmp = explode('-', $slug); $id = end($tmp);
        try {
            $pet = $this->petRepository->first($id, $this->user);
            $this->medicineRepository->remove($pet, $medicine_id);
            return redirect()->back()->with('success', 'Medicamento apagado com Sucesso!');
        } catch(\Exception $e){
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

}
