<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Award;
use App\UserAdmin;
use App\CategoryAward;
use App\CategoryAwardEvaluationStep;
use App\CategoryAwardEvaluationStepEvaluators;
use App\CategoryAwardEvaluationStepEvaluatorUserAdmin;
use App\SocialTecnologyCategoryAwardEvaluationStep;
use Spatie\Permission\Models\Role;

class CategoryAwardEvaluationStepEvaluatorsController extends Controller
{

    public function index(Award $award, CategoryAward $categoryAward, CategoryAwardEvaluationStep $evaluationStep  )
    { 
        $list = CategoryAwardEvaluationStepEvaluators::where('evaluationStep_id',$evaluationStep->id)->get();
        $evaluators = $evaluationStep->getListEvaluatorsFree();
        return view('admin.awards.categoryAwards.evaluationSteps.evaluators.index', compact('categoryAward', 'award', 'evaluationStep','list','evaluators'));
    }

    public function store(Request $request, Award $award, CategoryAward $categoryAward,CategoryAwardEvaluationStep $evaluationStep)
    {

        if(isset($request->evaluator1) && is_numeric($request->evaluator1)){

            $evaluationStepEvaluators = new CategoryAwardEvaluationStepEvaluators();

            $evaluationStepEvaluators->status = $request->status ?? 0;
            $evaluationStepEvaluators->evaluationStep_id = $evaluationStep->id;

            $evaluationStepEvaluators->save();

            if ($evaluationStep->enableTwoEvaluators == 1){
                $arr = [
                    ['evaluator_id' => $request->evaluator1],
                    ['evaluator_id' => $request->evaluator2]
                ];
            }else{
                $arr = [ 'evaluator_id' => $request->evaluator1 ];
            }

            $evaluationStepEvaluators->evaluators()->sync($arr);

            $evaluationStepEvaluators->save();

            $msg = ($evaluationStep->enableTwoEvaluators == 1) ?  'Certificadores/Avaliadores Cadastrados com sucesso' :  'Certificador/Avaliador Cadastrado com sucesso';

            return redirect()->route('admin.awards.categoryAwards.evaluationStep.evaluators.index',[
                $award->id,
                $categoryAward->id,
                $evaluationStep->id,
            ])->with($msg);
        }else{
            return redirect()->route('admin.awards.categoryAwards.evaluationStep.evaluators.index',[
                $award->id,
                $categoryAward->id,
                $evaluationStep->id,
            ])->with('Selecione um avaliador/certificador');
        }
    }

    public function edit(Award $award, CategoryAward $categoryAward,CategoryAwardEvaluationStep $evaluationStep, CategoryAwardEvaluationStepEvaluators $evaluationStepEvaluators)
    {
        $evaluators = $evaluationStep->getListEvaluatorsFree();
        if($evaluationStepEvaluators->status == 1){
            $evaluators->push($evaluationStepEvaluators->evaluators->first());
            if($evaluationStep->enableTwoEvaluators == 1){
                $evaluators->push($evaluationStepEvaluators->evaluators->last());
            }
        }
        return view('admin.awards.categoryAwards.evaluationSteps.evaluators.edit', compact('categoryAward', 'award', 'evaluationStep','list','evaluators','evaluationStepEvaluators'));
    }

    public function update(Request $request, Award $award, CategoryAward $categoryAward,CategoryAwardEvaluationStep $evaluationStep, CategoryAwardEvaluationStepEvaluators $evaluationStepEvaluators)
    {
        $evaluationStepEvaluators->status = $request->status ?? 0;

        $evaluationStepEvaluators->save();

        // desvinculando antes de sincronizar para evitar bug desconhido
        $evaluationStepEvaluators->evaluators()->detach();

        if ($evaluationStep->enableTwoEvaluators == 1){
            $arr = [
                ['evaluator_id' => $request->evaluator1],
                ['evaluator_id' => $request->evaluator2]
            ];
        }else{
            $arr = [ 'evaluator_id' => $request->evaluator1 ];
        }

        // vinculando avaliadores/certificadores
        $evaluationStepEvaluators->evaluators()->sync($arr);

        $evaluationStepEvaluators->save();

        $msg = ($evaluationStep->enableTwoEvaluators == 1) ?  'Certificadores/Avaliadores atualizados com sucesso' :  'Certificador/Avaliador atualizado com sucesso';

        return redirect()->route('admin.awards.categoryAwards.evaluationStep.evaluators.index',[
            $award->id,
            $categoryAward->id,
            $evaluationStep->id,
        ])->with($msg);

    }

    public function destroy(Request $request, Award $award, CategoryAward $categoryAward,CategoryAwardEvaluationStep $evaluationStep, CategoryAwardEvaluationStepEvaluators $evaluationStepEvaluators)
    {
        // deletando registro de TS distruibuida para esses avaliadores
        $evaluationStepEvaluators->socialTecnologyEvaluationSteps()->delete();

        // desvinculando os avaliadores/certificadores
        $evaluationStepEvaluators->evaluators()->detach();
        // excluindo o registro de avaliadores/certificadores para etapa de avaliação
        $evaluationStepEvaluators->delete();

        $msg = ($evaluationStep->enableTwoEvaluators == 1) ?  'Certificadores/Avaliadores excluídos dessa etapa de avaliação com sucesso' :  'Certificador/Avaliador excluído dessa etapa de avaliação';

        return redirect()->route('admin.awards.categoryAwards.evaluationStep.evaluators.index',[
            $award->id,
            $categoryAward->id,
            $evaluationStep->id,
        ])->with($msg);
    }

    public function distributeSocialTechnologies(Request $request, Award $award, CategoryAward $categoryAward, CategoryAwardEvaluationStep $evaluationStep  )
    {
        $evaluationStep->distributeSocialTechnologiesToEvaluators();

        return redirect()->route('admin.awards.categoryAwards.evaluationStep.evaluators.listdistributeSocialTechnologies',[
            $award->id,
            $categoryAward->id,
            $evaluationStep->id,
        ]);
    }

    public function listdistributeSocialTechnologies(Award $award, CategoryAward $categoryAward, CategoryAwardEvaluationStep $evaluationStep  )
    {
        return view('admin.awards.categoryAwards.evaluationSteps.evaluators.list-socialTecnology-evaluators', compact('evaluationStep'));
    }

}
