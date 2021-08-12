<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Award;
use App\CategoryAward;
use App\CustomStepForm;
use App\CategoryAwardCustomStepForms;
use Illuminate\Support\Facades\DB;

class ChallengeCategoryAwardCustomStepFormsController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Award $award, CategoryAward $categoryAward)
    {
        if(!$categoryAward->isCertificationType){

            $customStepForm = CustomStepForm::whereDoesntHave('categoryAwards', function($query) use ($categoryAward){
                $query->where('id',$categoryAward->id);
            })->where('status', 1)->get();

            $categoryAwardCustomStepForms = CategoryAwardCustomStepForms::where('categoryAward_id',$categoryAward->id)->get();

            $stepForms = $categoryAward->stepForms->where('status', 1);

            return view('admin.awards.categoryAwards.challenge.index', compact('customStepForm','categoryAwardCustomStepForms','categoryAward', 'stepForms'));
        }
        return redirect()->back()->with('error','Categoria de premiação do tipo certificação não permite essa ação');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Award $award, CategoryAward $categoryAward)
    {
        if(!$categoryAward->isCertificationType){

            $customStepFormListSelected = explode(',',$request->stepList);
            $customStepFormRemoved = explode(',',$request->customStepFormRemoved);

            foreach($customStepFormRemoved as $keyToRemove => $customStepFormToDelete ){
                DB::table( (new CategoryAwardCustomStepForms)->getTable() )->where([
                    'categoryAward_id' => $categoryAward->id,
                    'customStepForm_id' => $customStepFormToDelete,
                ])->delete();
            }

            foreach($customStepFormListSelected as $keyToSave => $customStepFormToSave ){
                DB::table( (new CategoryAwardCustomStepForms)->getTable() )
                    ->updateOrInsert([
                        'categoryAward_id' => $categoryAward->id,
                        'customStepForm_id' => $customStepFormToSave
                    ],
                    ['wizard_step' => ($keyToSave+1)]
                );
            }
            return redirect(route('admin.awards.categoryAwards.show', ['award' => $award->id, 'categoryAward' => $categoryAward->id]))->with('message','Seleção de formulários salva');
        }
        return redirect()->back()->withErrors('Categoria de premiação do tipo certificação não permite essa ação');
    }

}
