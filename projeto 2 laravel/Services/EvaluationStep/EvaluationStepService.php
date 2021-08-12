<?php


namespace App\Services\EvaluationStep;

use App\CategoryAwardEvaluationStep;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class EvaluationStepService
{
    /**
     * Alterando estado da premiação
     *
     * @param SocialTecnology $model
     * @param boolean $enable_two_evaluators
     * @param string $state
     * @param array $evaluations
     * @return mixed
     */
    function changeSocialTecnologyAwardState($model, $enable_two_evaluators, $evaluations, $state)
    {
        // se a avaliação for em dupla e essa é a primeira avaliação,
        // então não retornamos nada.
        if($enable_two_evaluators && count($evaluations) <= 1) {
            return;
        }

        return $model->update([
            'award_status' => $state
        ]);
    }

    /**
     * padroniza os dados para mostrar na view.
     *
     * @param Illuminate\Database\Eloquent\Collection $data
     * @return Illuminate\Database\Eloquent\Collection $list_items
     */
    public function adjustData($data, $award, $category_award, $evaluation_step, $route_name)
    {
        // variável que recebera os dados padronizados
        $list_items = Collection::make();

        // ajustando dados para ficar de acordo com a
        // estrutura padronizada na view.
        $data->each(
            function ($item)
                use ($list_items, $award, $category_award, $evaluation_step, $route_name)
            {

                $socialtecnology = $item->socialTecnology;

                // // verificamos se o usuário atual tem alguma
                // // avaliação referente a essa tecnologia social
                // $status = $socialtecnology->evaluations
                //     ->where('evaluator_id', Auth::guard('admin')->user()->id)
                //     ->where('evaluationStep_id', $evaluation_step->id)
                //     ->first();

                // // verificamos se a tecnologia já foi validada pelo coordenador
                // // SE já foi validada mostramos o feedback 'tecnologia validada'.
                // $tecnologyValitionStatus = $socialtecnology->evaluationStep->where('evaluationStep_id', $evaluation_step->id)->first()->validated;

                // // SENÂO verificamos se o usuário já avaliou essa tecnologia.
                // // SENÂO abilitamos o butão para avaliação.

                $tecnologyStatus = $this->getTecnologyStatus($socialtecnology, $evaluation_step);

                $arr_aux = [
                    'id' => $socialtecnology->id,
                    'name' => $socialtecnology->socialtecnology_name,
                    'desc' => [
                        'label' => 'Temas',
                        'content' => $socialtecnology->fulltext_themes,
                    ],
                    'status' => $tecnologyStatus,
                    'award_status' => $socialtecnology->award_status,
                    'award_icon' => $socialtecnology->getAwardIconAttribute(),
                    'route_name' => $route_name,
                    'route_params' => [
                        'award' => $award->id,
                        'category' => $category_award->id,
                        'evaluation_step' => $evaluation_step->id,
                        'socialtecnology' => $socialtecnology->id,
                    ],
                ];

                // dd($arr_aux);

                // adicionamos o item ao array principal
                $list_items->push($arr_aux);

                // limpamos o array auxiliar
                unset($arr_aux);
            }
        );

        // dd($list_items, $data);

        return $list_items;
    }

    /**
     * @param Collection $evaluations
     * @return bool
     */
    public function getEvaluationStatus($evaluations)
    {
        if(count($evaluations) > 1) {
            return true;
        }

        return false;
    }

    /**
     * @param SocialTecnology $tecnology
     * @param CategoryAwardEvaluationStep $evaluationStep
     * @return mixed
     */
    public function getTecnologyStatus($tecnology, $evaluationStep)
    {
        // buscamos informações sobre a etapa para saber
        // se já foi validada
        $validation = $tecnology->evaluationsStep
            ->where('evaluationStep_id', $evaluationStep->id)
            ->first();

        // buscamos informações sobre as avaliações para saber
        // se o usuário já avaliou a tecnologia
        $evaluation = $tecnology->evaluations
            ->where('evaluator_id', Auth::guard('admin')->user()->id)
            ->where('evaluationStep_id', $evaluationStep->id)
            ->first();

        if(!empty($validation->validated)) {
            return 1; // validada pelo coordenador
        } elseif($validation->validated === 0) {
            return 3; // avaliada pelo usuário
        } elseif(!empty($evaluation)) {
            return 2;
        }

        return 0; // tecnologia sem avaliação
    }
}
