<?php

namespace App\Http\Controllers\User;

use App\Repositories\PetRepository;
use App\Repositories\SpeciesRepository;
use Illuminate\Http\Request;
use App\Pet;
use Illuminate\Support\Facades\DB;

class PetController extends UserBaseController
{
    private $petRepository;
    private $speciesRepository;
    public function __construct(PetRepository $petRepository, SpeciesRepository $speciesRepository)
    {
        parent::__construct();
        $this->petRepository = $petRepository;
        $this->speciesRepository = $speciesRepository;
    }
    public function index(Request $r)
    {
        $this->setMeta('page_title', 'Meus Pets');
        $pets = $this->user->pets()->get();
        return view('user.pets.index', compact('pets'));
    }

    public function view(Request $r, $slug)
    {
        $tmp = explode('-', $slug); $id = end($tmp);
        try {
            $pet = $this->petRepository->first($id, $this->user);
        } catch(\Exception $e) {
            return redirect()->route('user.pets.index')->with('error', $e->getMessage());
        }
        $this->setMeta('page_title', 'Detalhes de ' . $pet->name);
        return view('user.pets.view', compact('pet'));
    }

    public function addEdit(Request $r, $slug = null) 
    {
        if(!is_null($slug)){
            $tmp = explode('-', $slug); $id = end($tmp);
            try {
                $pet = $this->petRepository->first($id, $this->user);
            } catch(\Exception $e) {
                return redirect()->route('user.pets.index')->with('error', $e->getMessage());
            }
        } else {
            $pet = new Pet;
        }
        if($r->isMethod('post')) {
            try {
                DB::beginTransaction();
                    $this->petRepository->save($r, $this->user);
                DB::commit();
                return redirect()->route('user.pets.index')->with('success', !empty($r->input('id')) ? 'Pet atualizado com sucesso!' : 'Pet cadastrado com sucesso!');
            } catch(\Exception $e) {
                DB::rollBack();
                return redirect()->back()->withInput($r->all())->with('error', $e->getMessage() . $e->getLine());
            }
        }
        $species = $this->speciesRepository->getList();
        return view('user.pets.addEdit', compact('pet','species'));
    }
}
