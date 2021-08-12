<?php

namespace App\Http\Controllers\Admin;

use App\CategoryAwardEvaluationCriterion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CategoryAwardEvaluationStep;
use App\CategoryAward;
use App\Award;

class CategoryAwardEvaluationCriteriaController extends Controller
{

    public function create(Award $award, CategoryAward $categoryAward, CategoryAwardEvaluationStep $evaluationStep )
    {
        return view('admin.awards.categoryAwards.evaluationSteps.criteria.create', compact('categoryAward', 'award', 'evaluationStep'));
    }

    public function edit(Award $award, CategoryAward $categoryAward, CategoryAwardEvaluationStep $evaluationStep, CategoryAwardEvaluationCriterion $criterion )
    {
        return view('admin.awards.categoryAwards.evaluationSteps.criteria.edit', compact('categoryAward', 'award', 'evaluationStep', 'criterion'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Award $award, CategoryAward $categoryAward,CategoryAwardEvaluationStep $evaluationStep)
    {
        $criterion = new CategoryAwardEvaluationCriterion();
        $criterion->evaluationCriteria =  $request->evaluationCriteria  ;
        $criterion->weight =  $request->weight;

        $evaluationStep->evaluationCriteria()->save($criterion);

        return redirect()-> route('admin.awards.categoryAwards.evaluationStep.show',[$categoryAward->award_id, $categoryAward->id, $evaluationStep->id] )
                    ->with('message', 'Critério de Avaliação Cadastrado.');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Award $award, CategoryAward $categoryAward,CategoryAwardEvaluationStep $evaluationStep, CategoryAwardEvaluationCriterion $criterion )
    {
        $criterion->evaluationCriteria =  $request->evaluationCriteria  ;
        $criterion->weight =  $request->weight;

        $criterion->save();

        return redirect()-> route('admin.awards.categoryAwards.evaluationStep.show',[$categoryAward->award_id, $categoryAward->id, $evaluationStep->id] )
                    ->with('message', 'Critério de Avaliação atualizado.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CategoryAwardEvaluationCriterion  $categoryAwardEvaluationCriterion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Award $award, CategoryAward $categoryAward,CategoryAwardEvaluationStep $evaluationStep, CategoryAwardEvaluationCriterion $criterion)
    {
        $criterion->delete();
        return redirect()->route('admin.awards.categoryAwards.evaluationStep.show',[$categoryAward->award_id, $categoryAward->id, $evaluationStep->id] )
                ->with('message', 'Critério de Avaliação excluído.');
    }
}
