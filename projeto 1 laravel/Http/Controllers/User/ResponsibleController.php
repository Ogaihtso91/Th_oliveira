<?php

namespace App\Http\Controllers\User;

use App\Repositories\PetRepository;
use Illuminate\Http\Request;
use App\Pet;
use Illuminate\Support\Facades\DB;

class ResponsibleController extends UserBaseController
{
    private $petRepository;

    public function __construct(PetRepository $petRepository)
    {
        parent::__construct();
        $this->petRepository = $petRepository;
    }

    public function list(Request $r, $slug)
    {
        $tmp = explode('-', $slug); $id = end($tmp);
        try {
            $pet = $this->petRepository->first($id, $this->user);
            $responsibles = $pet->responsibles;
        } catch(\Exception $e) {
            return redirect()->route('user.pets.index')->with('error', $e->getMessage());
        }
        $this->setMeta('page_title', 'Responsáveis');

        return view('user.pets.responsibles.list', compact('responsibles','pet'));
    }

    public function add(Request $r, $slug)
    {
        $tmp = explode('-', $slug); $id = end($tmp);
        try {
            $pet = $this->petRepository->first($id, $this->user);
            $this->petRepository->addResponsible($pet, $r->get('responsible'));
            return redirect()->back()->with('success', 'Responsavel adicionado com Sucesso!');
        } catch(\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }


}
