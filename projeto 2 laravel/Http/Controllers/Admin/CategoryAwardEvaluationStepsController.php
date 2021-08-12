<?php

namespace App\Http\Controllers\Admin;

use App\CategoryAwardEvaluationStep;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CategoryAward;
use App\Award;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

use Illuminate\Support\Collection;

class CategoryAwardEvaluationStepsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Award $award, CategoryAward $categoryAward)
    {
        // criando parametros para breadcrumb
        $breadcrumb_params = Collection::make([
            'award' => [
                'active' => false,
                'label' => __('front.award.breadcrumb.award').$award->name,
                'link' => [
                    'name' => 'admin.awards.show',
                    'params' => [
                        'award' => $award->id
                    ]
                ],
            ],
            'category_awards' => [
                'active' => false,
                'label' => __('front.award.breadcrumb.category_awards').$categoryAward->name,
                'link' => [
                    'name' => 'admin.awards.categoryAwards.show',
                    'params' => [
                        'award' => $award->id,
                        'category_award' => $categoryAward->id,
                    ]
                ],
            ],
            'evaluation_steps' => [
                'active' => true,
                'label' => __('front.award.breadcrumb.evaluation_steps'),
            ],
        ]);

        $categoryAward->award;
        $categoryAward->evaluationSteps ;
        if ( $award->id == $categoryAward->award_id ){
            return view('admin.awards.categoryAwards.evaluationSteps.index', compact('categoryAward', 'breadcrumb_params'));
        }else{
            return redirect()->route('admin.awards.premiacoes');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Award $award, CategoryAward $categoryAward)
    {
        $roles = Role::where('status',1)->get();
        return view('admin.awards.categoryAwards.evaluationSteps.create', compact('categoryAward', 'award','roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Award $award, CategoryAward $categoryAward)
    {
        $evaluationStep = new CategoryAwardEvaluationStep();

        $evaluationStep->name = $request->name;
        $evaluationStep->evaluationType = $request->evaluationType;
        $evaluationStep->enableTwoEvaluators = $request->enableTwoEvaluators;

        $evaluationStep->evaluator_role_id = $request->evaluator_role;
        $evaluationStep->awardStatusForApprovedSocialTecnology = $request->awardStatusForApprovedSocialTecnology;

        $evaluationStep->previousEvaluationStep_id = $categoryAward->evaluationSteps->last()->id ?? NULL;

        $evaluationStep->validator_role_id =  $request->validator_role;
        $evaluationStep->numberOfApprovedSocialTechnologies =  ($request->evaluationType == 2)  ?  $request->numberOfApprovedSocialTechnologies : NULL;

        $evaluationStep->auditor_role_id = ($request->awardStatusForApprovedSocialTecnology == 'w')  ? $request->auditor_role : NULL;

        $award->categoryAwards->find($categoryAward)->evaluationSteps()->save($evaluationStep);

        /*Tarefa 4478 feita por marcio.rosa*/
        return redirect()->route('admin.awards.categoryAwards.evaluationStep.index' ,[$categoryAward->award_id,$categoryAward->id,$evaluationStep->id])->with('message', 'Etapa de Avaliação Cadastrada.');
       /* return redirect()->route('admin.awards.categoryAwards.evaluationStep.show',[$categoryAward->award_id,$categoryAward->id,$evaluationStep->id])
            ->with('message', 'Etapa de Avaliação Cadastrada.');*/

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CategoryAwardEvaluationStep  $evaluationStep
     * @return \Illuminate\Http\Response
     */
    public function show(Award $award, CategoryAward $categoryAward,CategoryAwardEvaluationStep $evaluationStep)
    {
        // pode ser refatorado futuramente
        $breadcrumb_params = Collection::make([
            'award' => [
                'active' => false,
                'label' => __('front.award.breadcrumb.award').$award->name,
                'link' => [
                    'name' => 'admin.awards.show',
                    'params' => []
                ],
            ],
            'category_awards' => [
                'active' => false,
                'label' => __('front.award.breadcrumb.category_awards').$categoryAward->name,
                'link' => [
                    'name' => 'admin.awards.categoryAwards.show',
                    'params' => [
                        'award' => $award->id,
                        'category_award' => $categoryAward->id,
                    ]
                ],
            ],
            'evaluation_step' => [
                'active' => false,
                'label' => __('front.award.breadcrumb.evaluation_steps').$evaluationStep->name,
                'link' => [
                    'name' => 'admin.awards.categoryAwards.evaluationStep.index',
                    'params' => [
                        'award' => $award->id,
                        'category_award' => $categoryAward->id,
                    ]
                ],
            ],
            'evaluation_criteria' => [
                'active' => true,
                'label' => __('front.award.breadcrumb.evaluation_criteria'),
            ],
        ]);

        $evaluationStep->evaluationCriteria;
        return view('admin.awards.categoryAwards.evaluationSteps.show', compact('categoryAward', 'evaluationStep', 'breadcrumb_params'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CategoryAwardEvaluationStep  $evaluationStep
     * @return \Illuminate\Http\Response
     */
    public function edit(Award $award, CategoryAward $categoryAward,CategoryAwardEvaluationStep $evaluationStep)
    {
        if ( $award->id == $categoryAward->award_id && $categoryAward->id == $evaluationStep->category_award_id  ){
            $roles = Role::where('status',1)->get();
            return view('admin.awards.categoryAwards.evaluationSteps.edit', compact('categoryAward','evaluationStep','roles'));
        }else{
            return redirect()->route('admin.awards.premiacoes');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CategoryAwardEvaluationStep  $evaluationStep
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Award $award, CategoryAward $categoryAward, CategoryAwardEvaluationStep $evaluationStep)
    {
        if ( $award->id == $categoryAward->award_id && $categoryAward->id == $evaluationStep->category_award_id  )
        {
            $evaluationStep->name = $request->name;
            $evaluationStep->evaluationType = $request->evaluationType;
            $evaluationStep->enableTwoEvaluators = $request->enableTwoEvaluators;

            $evaluationStep->evaluator_role_id = $request->evaluator_role;
            $evaluationStep->awardStatusForApprovedSocialTecnology = $request->awardStatusForApprovedSocialTecnology;

            $evaluationStep->validator_role_id = $request->validator_role;
            $evaluationStep->numberOfApprovedSocialTechnologies =  ($request->evaluationType == 2)  ?  $request->numberOfApprovedSocialTechnologies : NULL;

            $evaluationStep->auditor_role_id = ($request->awardStatusForApprovedSocialTecnology == 'w')  ? $request->auditor_role : NULL;

            $award->categoryAwards->find($categoryAward)->evaluationSteps()->save($evaluationStep);

            return redirect()->route('admin.awards.categoryAwards.evaluationStep.index',[$categoryAward->award_id,$categoryAward->id])
                ->with('message', 'Etapa de Avaliação atualizada.');
        }else{
            return redirect()->route('admin.awards.premiacoes');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CategoryAwardEvaluationStep  $evaluationStep
     * @return \Illuminate\Http\Response
     */
    public function destroy(Award $award, CategoryAward $categoryAward,CategoryAwardEvaluationStep $evaluationStep)
    {
        if ( $award->id == $categoryAward->award_id && $categoryAward->id == $evaluationStep->category_award_id  )
        {
            $evaluationStep->delete();
            return redirect()->route('admin.awards.categoryAwards.evaluationStep.index',[$categoryAward->award_id,$categoryAward->id])
                ->with('message', 'Etapa de Avaliação excluída.');
        }else{
            return redirect()->route('admin.awards.premiacoes');
        }
    }

    public function publishAprovedSocialTecnologiesListByStep( Request $request , CategoryAwardEvaluationStep $evaluationStep)
    {
        // 4707 Comentando o código, agora a etapa de certificação precisa da publicação no painel para ir ao portal.
        /*if($evaluationStep->awardStatusForApprovedSocialTecnology == 'c'){
            return redirect()->back()->withErrors('Etapa de Certificação não necessita efetivar publicação no portal');
        }*/

        if(is_null($evaluationStep->approvedListPublished_flag) && is_null($evaluationStep->approvedList_published_at) ){
            $evaluationStep->update([
                'approvedListPublished_flag' => 1,
                'approvedList_published_at' => Carbon::now(),
            ]);
            return redirect()->back()->with('message','Lista de Tecnologias Sociais aprovadas publicada no portal');
        }
        return redirect()->back()->withErrors('Lista de Tecnologias Sociais aprovadas já haviam sido publicadas anteriormente');
    }
}
