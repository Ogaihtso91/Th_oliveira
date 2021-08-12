<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CategoryAwardEvaluationStep;
use App\SocialTecnology;
use App\SocialTecnologyCategoryAwardEvaluationStep;
use App\SocialTecnologyEvaluatorStepEvaluation;
use Illuminate\Support\Facades\Auth;

class AuditionController extends Controller
{
    public function index()
    {
        $steps = CategoryAwardEvaluationStep::where('awardStatusForApprovedSocialTecnology','w')
            ->whereHas('socialTecnologyEvaluationStep', function($query){
                $query->whereNotNull('stepFinalScore');
            })
            ->whereHas('categoryAward', function ($query){
                $query->where('isCertificationType',1);
                $query->whereHas('award');
            })
            ->get();

        return view('admin.awards.audition.index', compact('steps'));
    }

    public function view_socialTecnologies_list(CategoryAwardEvaluationStep $evaluationStep)
    {
        $socialTecnologiesStepEvaluation = $evaluationStep->socialTecnologyEvaluationStep()->orderBy('stepFinalScore','desc')->get();

        return view('admin.awards.audition.socialTecnologiesList', compact('evaluationStep','socialTecnologiesStepEvaluation'));
    }

    public function view_evaluations_socialTecnology_list(CategoryAwardEvaluationStep $evaluationStep, SocialTecnologyCategoryAwardEvaluationStep $socialTecnologyEvaluationStep)
    {
        // dd($socialTecnologyEvaluationStep->toArray() , $socialTecnologyEvaluationStep->social_tecnology_evaluator_step_evaluation->toArray());
        return view('admin.awards.audition.socialTecnologyEvaluations', compact('evaluationStep','socialTecnologyEvaluationStep'));
    }

    public function view_evaluation_detail(CategoryAwardEvaluationStep $evaluationStep, SocialTecnologyCategoryAwardEvaluationStep $socialTecnologyEvaluationStep, SocialTecnologyEvaluatorStepEvaluation $socialTecnologyEvaluatorStep)
    {
        // dd(  $evaluationStep->toArray(),  $socialTecnologyEvaluationStep->toArray(),  $socialTecnologyEvaluatorStep->toArray() );

        return view('admin.awards.audition.socialTecnologyEvaluatorStepEvaluation', compact('evaluationStep','socialTecnologyEvaluationStep','socialTecnologyEvaluatorStep'));
    }

    public function audit_step_socialTecnology_final_list(Request $request, CategoryAwardEvaluationStep $evaluationStep)
    {
        try {
            // verificar se a etapa
            if($evaluationStep->awardStatusForApprovedSocialTecnology != 'w'){
                throw new Exception("Ação inválida para essa etapa");
            }
            // verifica se o usuário logado que está efetivando a auditoria
            // tem a role configurada para auditoria da etapa
            // dd( Auth::guard('admin')->user()->roles->toArray() );
            if(Auth::guard('admin')->user()->hasRole($evaluationStep->getAuditorRole()->name ?? '')){
                // atualiza status da etapa para identificar que ela está finalizada
                // TO-DO: validaçãoes que utilizem esse status
                $evaluationStep->update(['status' => 1]);
                // marca como auditada todas as TS participantes da etapa
                $evaluationStep->socialTecnologyEvaluationStep()->update(['audited' => 1]);
                // atualiza status da TSs vencedoras
                $evaluationStep
                    ->approved_social_tecnolgies
                    ->each(function($item) use ($evaluationStep){
                        $item->update(['award_status' => $evaluationStep->awardStatusForApprovedSocialTecnology]);
                    }
                );
            }else{
                return redirect()
                ->back()
                ->withErrors('Ação inválida, você não tem permissão para executar esta ação');
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withErros($e->getMessage());
        }
        return redirect()
            ->back()
            ->with('message','Auditoria efetivada com sucesso');
    }
}
