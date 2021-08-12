<?php


namespace App\Services\SocialTecnologyWizard;

use App\ContentManager;
use App\Institution;
use App\SocialTecnology;
use App\Repositories\Repository\SocialTecnologyWizard\SocialTecnologyWizardRepository;
use Closure;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class SocialTecnologyWizardService
{
    // STEP CONSTANTS
    const START_STEP = "0";
    const FIRST_STEP = "1";
    const SECOND_STEP = "2";
    const THIRD_STEP = "3";
    const FOURTH_STEP = "4";
    const FIFTH_STEP = "5";
    const SIXTH_STEP = "6";
    const SEVENTH_STEP = "7";
    const EIGHTH_STEP = "8";
    const NINTH_STEP = "9";
    const TENTH_STEP = "10";
    const REVIEW_STEP = "11";

    private $wizardRepository;

    public function __construct()
    {
        $this->wizardRepository = new SocialTecnologyWizardRepository();
    }

    public function validateAttachment(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'main_images' => 'sometimes|required',
            'add_images' => 'sometimes',
            'add_files' => 'sometimes',
            'videos' => 'sometimes',
            'remove_images' => 'sometimes',
            'remove_files' => 'sometimes',
            'remove_videos' => 'sometimes',
        ]);

        // Valida a imagem somente quando estiver alterando
        $validation->sometimes(['image'], [
            (empty($request->size_image) ? '' : 'max:'.($request->size_image * 1024)),
            function ($attribute, $value, $fail) {
                if ($value->extension() != 'png' && $value->extension() != 'jpeg' && $value->extension() != 'jpg') {
                    $fail('Foto da tecnologia: é permitido apenas imagens PNG, JPEG ou JPG.');
                }
            },
        ], function ($input) {
            return $input->change_image == 1;
        });

        // Valida os vídeos
        $validation->sometimes(['videos'], [
            function ($attribute, $value, $fail) {
                if(!empty($value)) {
                    foreach ($value as $v_video_item) {
                        if (!empty($v_video_item)) {
                            $video_validation = youtube_information($v_video_item);

                            if (empty($video_validation['title'])) {
                                $fail('Vídeo informado não existe.');
                            }
                        }
                    }
                }
            },
        ], function ($input) {
            return !empty($input->videos);
        });

        return $validation->validate();
    }

    /**
     * @param object $request - dados enviados pelo formulário.
     * @return mixed
     */
    public function wizardControlManager($request)
    {
        // buscamos todos os dados da request e a transformamos
        // em object
        $data = (object) $request->all();

        // SE a action for de edição, executamos as funções
        // abaixo para gerenciar e registrar os dados.
        if($this->isEditing($data->action))
        {
            // buscamos o conteudo registrado na base.
            $content = $this->wizardRepository->getContentManagerByRegister($data->registration);
            // $new_values = $this->wizardRepository->getContentManagerValues($content);

            // dd('editando registro', $data, $content, $new_values, $request->step);

            $this->wizardNavigationManager($content, $request->step, function($step, $previousStep) use ($request) {
                return $this->wizardStepControl($step, $request, $previousStep);
            });
        }

        // SENÂO criamos um novo registro
        return $this->wizardControlFirstStep($request, $previousStep = false);
    }

    /**
     * Validamos a ação atravez do nome da rota.
     * @param string $action - nome da rota usada na view
     * @return bool
     */
    public function isEditing($action)
    {
        if($action === 'user.institution.socialtecnologies.create.steps') {
            return true;
        }

        return false;
    }

    /**
     * @param object $content
     * @param int $currentStep
     * @param callback $next
     * @return mixed $next($currentStep, $previousStep)
     */
    public function wizardNavigationManager($content, $currentStep, $next)
    {
        // SE a etapa atual for maior que a ultima etapa registrada
        // ENTÂO retornamos a ultima etapa registrada.
        if($currentStep > $content->wizard_step) {
            return $next($content->wizard_step, $previousStep = false);
        }

        // SE for uma passo anterios retornamos ele e tambem retornamos
        // a flag para indicando
        if($currentStep < $content->wizard_step) {
            return $next($currentStep, $previousStep = true);
        }

        // mesmo se for uma etapa avançada nos retornamos a ultima etapa
        // registrada para preenchimento da mesma
        // SENÂO retornamos a etapa desejada atravez da callback
        return $next($currentStep, $previousStep = false);
    }

    /**
     * @param int $currentStep
     * @param object $data
     * @return mixed
     */
    public function wizardStepControl($step, $data, $previousStep)
    {
        // Retornamos a funções responsavel pela etapa atual
        // dessa forma cada etapa tera uma função propria.
        switch ($step) {
            case self::FIRST_STEP:
                $this->wizardControlFirstStep($data, $previousStep);
            break;

            case self::SECOND_STEP:
                $this->wizardControlSecondStep($data, $previousStep);
            break;

            case self::THIRD_STEP:
                $this->wizardControlThirdStep($data, $previousStep);
            break;
        }
    }


    /**
     * Primeira etapa de inscrição
     * @param Request $request
     * @param $previousStep
     * @return mixed
     */
    public function wizardControlStartStep(Request $request, $previousStep)
    {
        // sempre válidamos os dados antes de mandar para a base.
        $validated = Validator::make($request->all(), [
            'institution_id' => 'required|integer|exists:institutions,id',
        ])->validate();

        // Busca a instituição vinculada
        $institution = \Auth::guard()->user()->institutions->where('id', $request->get('institution_id'))->first();

        // redirecionamos para a etapa seguinte
        return redirect(route('user.institution.socialtecnologies.create', [
            'institution_seo_url' => $institution->seo_url,
            'award_id' => $request->get('award_id'),
            'registration' => $request->get('registration'),
        ]));
    }

    /**
     * Primeira etapa de inscrição
     *
     * @param Request $request
     * @param $previousStep
     * @return mixed
     */
    public function wizardControlFirstStep(Request $request, $previousStep)
    {
        // sempre válidamos os dados antes de mandar para a base.
        $validated = Validator::make($request->all(), [
            'institution_id' => 'required|integer|exists:institutions,id',
            'action' => 'required',
            'registration' => 'sometimes|required|exists:content_manager,registration',
            'socialtecnology_name' => 'required|string',
            'award_id' => 'required|integer|exists:awards,id',
            'category_award' => 'required'
        ])->validate();

        // sincronizamos os dados para edição ou criação
        $storedData = $this->wizardRepository->storeOrUpdate($validated, $previousStep);

        // inicializamos as relações entre as entidades
        $this->wizardRepository->setSocialTecnologyAwardRelation($storedData->tecnology, $validated['award_id']);

        $this->wizardRepository->setSocialTecnologyCategoryAwardRelation($storedData->tecnology, $validated['category_award']);

        if ($storedData->tecnology->categoryAwardsSubscriptions->first()->isCertificationType == false) {
            // redirecionamos para o formulário customizado.
            return redirect(route('user.custom.form.challenge.index', ['registration_code' => $storedData->tecnology->registration,'current_step' => $storedData->content->wizard_step]));
        } else {
            // redirecionamos para a etapa seguinte do formulário de certificação
            return redirect(route('user.institution.socialtecnologies.create.steps', [
                'registration' => $storedData->tecnology->registration,
                'institution_seo_url' => $request->institution_seo_url,
                'steps' => $storedData->content->wizard_step
            ]));
        }
    }

    public function wizardControlSecondStep(Request $request, $previousStep)
    {
        // sempre validamos os dados do formulário.
        $validated = Validator::make($request->all(), [
            'registration' => 'required',
            'non_profit' => 'required|boolean',
            'deployment_time' => 'required|boolean',
            'remains_active' => 'required|boolean',
            'subscribed_previous' => 'required|boolean',
            'already_had_investment' => 'required|boolean'
        ])->after(function ($validator) {
            $data = $validator->getData();
            $socialTecnology = SocialTecnology::where('registration', $data['registration'])->first();
            // allowForProfitSocialTechnologies==0 ? não permite inst. com fins lucrativos : Todas as instituições
            $award = $socialTecnology->award;
            $allowOnlyNonProfit = $award->allowForProfitSocialTechnologies == 0;

            if($allowOnlyNonProfit && $data['non_profit'] == 0) {
                $validator->errors()->add('non_profit', "Somente instituições sem fins lucrativos podem se inscrever para o Prêmio '{$award->name}'");
            }
        })->validate();

        // sincronizamos os dados para edição ou criação
        $storedData = $this->wizardRepository->updateSync($validated, $previousStep);

        // inicializamos os relacionamentos entre as entidades.
        // se tivermos
        // dd($storedData);
        // redirecionamos para a etapa registrada
        return redirect(route('user.institution.socialtecnologies.create.steps', [
            'registration' => $storedData->tecnology->registration,
            'institution_seo_url' => $request->institution_seo_url,
            'steps' => $storedData->content->wizard_step
        ]));
    }

    public function wizardControlThirdStep(Request $request, $previousStep)
    {
        // Sempre validamos os dados enviados do formulário
        $validated = Validator::make($request->all(), [
            'registration' => 'required',
            'representative' => 'required',
        ])->validate();

        // registramos as mudanças na base de dados
        // nessa etapa apenas temos de fazer o relacionamento
        // entre as tabelas.
        // ENTÂO buscamos os registro de tecnologia e conteudo
        $storedData = $this->wizardRepository->storeOrUpdate($validated, $previousStep);

        // redirecionamos para a etapa seguinte
        return redirect(route('user.institution.socialtecnologies.create.steps', [
            'registration' => $storedData->tecnology->registration,
            'institution_seo_url' => $request->institution_seo_url,
            'steps' => $storedData->content->wizard_step
        ]));
    }

    public function wizardControlFourthStep(Request $request, $previousStep)
    {
        // Sempre validamos os dados enviados do formulário
        $validated = Validator::make($request->all(), [
            'registration' => 'required',
            'summary' => 'string|nullable',
            'primaryTheme' => 'required|integer',
            'secondaryTheme' => 'nullable',
            'keywords' => 'required',
        ])->validate();

        // sincronizo os dados registrado `summary`
        $storedData = $this->wizardRepository->storeOrUpdate($validated, $previousStep);

        // realizo o relacionamento entre as entidades
        $this->wizardRepository->setPrimaryThemeRelationship($storedData->tecnology, $validated['primaryTheme'], true);
        $this->wizardRepository->setSecondaryThemeRelationship($storedData->tecnology, $validated['secondaryTheme'], true);
        $this->wizardRepository->setKeywordsRelationship($storedData->tecnology, $validated['keywords']);

        // retornamos o proximo passo
        return redirect(route('user.institution.socialtecnologies.create.steps', [
            'registration' => $storedData->tecnology->registration,
            'institution_seo_url' => $request->institution_seo_url,
            'steps' => $storedData->content->wizard_step
        ]));
    }

    public function wizardControlFifthStep(Request $request, $previousStep)
    {
        // Sempre validamos os dados enviados do formulário
        $validated = Validator::make($request->all(), [
            'registration' => 'required',
            'general_objective' => 'required',
            'especific_objective' => 'required',
            'problem_solution' => 'required',
        ])->validate();

        $storedData = $this->wizardRepository->storeOrUpdate($validated, $previousStep);

        // retornamos o proximo passo
        return redirect(route('user.institution.socialtecnologies.create.steps', [
            'registration' => $storedData->tecnology->registration,
            'institution_seo_url' => $request->institution_seo_url,
            'steps' => $storedData->content->wizard_step
        ]));
    }

    public function wizardControlSixthStep(Request $request, $previousStep)
    {
        // Sempre validamos os dados enviados do formulário
        $validated = Validator::make($request->all(), [
            'registration' => 'required',
            'description' => 'required',
            'result_archieved' => 'required',
        ])->validate();

        $storedData = $this->wizardRepository->storeOrUpdate($validated, $previousStep);

        // retornamos o proximo passo
        return redirect(route('user.institution.socialtecnologies.create.steps', [
            'registration' => $storedData->tecnology->registration,
            'institution_seo_url' => $request->institution_seo_url,
            'steps' => $storedData->content->wizard_step
        ]));
    }

    public function wizardControlSeventhStep(Request $request, $previousStep)
    {
        // Sempre validamos os dados enviados do formulário
        $validated = Validator::make($request->all(), [
            'registration' => 'required',
            'necessary_resources' => 'required',
            'deploymeny_cost' => 'required',
        ])->validate();

        $storedData = $this->wizardRepository->storeOrUpdate($validated, $previousStep);

        // retornamos o proximo passo
        return redirect(route('user.institution.socialtecnologies.create.steps', [
            'registration' => $storedData->tecnology->registration,
            'institution_seo_url' => $request->institution_seo_url,
            'steps' => $storedData->content->wizard_step
        ]));
    }

    public function wizardControlEighthStep(Request $request, $previousStep)
    {
        $validated = Validator::make($request->all(), [
            'registration' => 'required',
            'deployment_place' => 'required',
            'audience_served' => 'required',
            'other_audience_options' => 'sometimes',
            'partner' => 'nullable',
        ])->validate();

        $storedData = $this->wizardRepository->storeOrUpdate($validated, $previousStep);

        // retornamos o proximo passo
        return redirect(route('user.institution.socialtecnologies.create.steps', [
            'registration' => $storedData->tecnology->registration,
            'institution_seo_url' => $request->institution_seo_url,
            'steps' => $storedData->content->wizard_step
        ]));
    }

    public function wizardControlNinthStep($request, $previousStep)
    {
        // Sempre validamos os dados enviados pelo formulários
        $validated = Validator::make($request->all(), [
            'registration' => 'required',
        ])->validate();

        $validatedAttachment = $this->validateAttachment($request);

        // sincronizo os dados registrado `summary`
        $storedData = $this->wizardRepository->storeOrUpdate($validated, $previousStep);

        // verificamos se os campos estão preenchidos
        if(isset($validatedAttachment['main_images'])) $this->wizardRepository->setSocialTecnologyImagesMain($storedData->tecnology, $validatedAttachment);
        if(isset($validatedAttachment['add_images'])) $this->wizardRepository->setSocialTecnologyImages($storedData->tecnology->id, $validatedAttachment);
        if(isset($validatedAttachment['add_files'])) $this->wizardRepository->setSocialTecnologyFile($storedData->tecnology->id, $validatedAttachment);
        if(isset($validatedAttachment['videos'])) $this->wizardRepository->setSocialTecnologyVideo($storedData->tecnology->id, $validatedAttachment);

        // removemos os items solicitados
        // if(isset($validatedAttachment['remove_images'])) $this->wizardRepository->removeSocialTecnologyImage($validatedAttachment);
        if(isset($validatedAttachment['remove_files'])) $this->wizardRepository->removeSocialTecnologyFile($validatedAttachment);
        if(isset($validatedAttachment['remove_videos'])) $this->wizardRepository->removeSocialTecnologyVideo($validatedAttachment);

        // retornamos o proximo passo
        return redirect(route('user.institution.socialtecnologies.create.steps', [
            'registration' => $storedData->tecnology->registration,
            'institution_seo_url' => $request->institution_seo_url,
            'steps' => $storedData->content->wizard_step
        ]));
    }

    public function wizardControlTenthStep(Request $request, $previousStep)
    {
        // Sempre validamos os dados enviados pelo formulários
        $validated = Validator::make($request->all(), [
            'registration' => 'required',
            'testimonial' => 'nullable|string'
        ])->validate();

        // sincronizo os dados registrado `summary`
        $storedData = $this->wizardRepository->storeOrUpdate($validated, $previousStep);

        return redirect(route('user.institution.socialtecnologies.create.steps', [
            'registration' => $storedData->tecnology->registration,
            'institution_seo_url' => $request->institution_seo_url,
            'steps' => $storedData->content->wizard_step
        ]));
    }

    public function wizardControlReviewStep(Request $request, $previousStep, $isEditing = false)
    {
        // Sempre validamos os dados enviados pelo formulários
        $validated = Validator::make($request->all(), [
            'action' => 'required',
            'registration' => 'required|exists:content_manager,registration',
            'socialtecnology_name' => 'required|string',
            'award_id' => 'required|integer|exists:awards,id',
            'category_award' => 'required',
            'non_profit' => 'required|boolean',
            'deployment_time' => 'required|boolean',
            'remains_active' => 'required|boolean',
            'subscribed_previous' => 'required|boolean',
            'already_had_investment' => 'required|boolean',
            'representative' => 'required',
            'summary' => 'string|nullable',
            'primaryTheme' => 'required|integer',
            'secondaryTheme' => 'nullable',
            'keywords' => 'required',
            'general_objective' => 'required',
            'especific_objective' => 'required',
            'problem_solution' => 'required',
            'description' => 'required',
            'result_archieved' => 'required',
            'necessary_resources' => 'required',
            'deploymeny_cost' => 'required',
            'deployment_place' => 'required',
            'audience_served' => 'required',
            'partner' => 'nullable',
            'testimonial' => 'nullable|string'
        ])->validate();

        // validar items anexados
        $validatedAttachment = $this->validateAttachment($request);

        // sincronizo os dados registrado
        $storedData = $this->wizardRepository->storeOrUpdate($validated, $previousStep);

        // RELACIONAMENTO DE PREMIAÇÃO E CATEGORIA DE PREMIAÇÃO
        if(isset($validated['award_id'])) $this->wizardRepository->setSocialTecnologyAwardRelation($storedData->tecnology, $validated['award_id']);
        if(isset($validated['category_award'])) $this->wizardRepository->setSocialTecnologyCategoryAwardRelation($storedData->tecnology, $validated['category_award']);

        // RELACIONAMENTO DE TEMAS PRIMÁRIO, SECONDÁRIO E PALAVRAS-CHAVES
        if(isset($validated['primaryTheme'])) $this->wizardRepository->setPrimaryThemeRelationship($storedData->tecnology, $validated['primaryTheme'], true);
        if(isset($validated['secondaryTheme'])) $this->wizardRepository->setSecondaryThemeRelationship($storedData->tecnology, $validated['secondaryTheme'], true);
        if(isset($validated['keywords'])) $this->wizardRepository->setKeywordsRelationship($storedData->tecnology, $validated['keywords']);

        // RELACIONAMENTO DE ANEXOS
        if(isset($validatedAttachment['main_images'])) $this->wizardRepository->setSocialTecnologyImagesMain($storedData->tecnology, $validatedAttachment);
        if(isset($validatedAttachment['add_images'])) $this->wizardRepository->setSocialTecnologyImages($storedData->tecnology->id, $validatedAttachment);
        if(isset($validatedAttachment['add_files'])) $this->wizardRepository->setSocialTecnologyFile($storedData->tecnology->id, $validatedAttachment);
        if(isset($validatedAttachment['videos'])) $this->wizardRepository->setSocialTecnologyVideo($storedData->tecnology->id, $validatedAttachment);

        // removemos os items solicitados
        // $this->wizardRepository->removeSocialTecnologyImage($validatedAttachment);
        $this->wizardRepository->removeSocialTecnologyFile($validatedAttachment);
        $this->wizardRepository->removeSocialTecnologyVideo($validatedAttachment);

        // FINALIZAMOS A INSCRIÇÃO DA TECNOLOGIA
        $this->wizardRepository->completedInscription($storedData->content, $storedData->tecnology);

        // Ajustando mensagem de feedback
        $message = $isEditing ? 'Tecnologia social editada com sucesso!' : 'Inscrição enviada com sucesso!';

        return redirect(route('user.institution.show', [
            'institution_seo_url' => $request->institution_seo_url
        ]))->with('message', $message);
    }

    //================| Render Functions |================//

    /**
     * verificamos se estamos em uma etapa atual
     * ou uma etapa anterior.
     * @param string $registration
     * @return mixed
     */
    public function contentWizardStepUpdate($content, $currentStep)
    {
        if($currentStep === $content) {
            return $content->update([
                'wizard_step' => ($content->wizard_step + 1)
            ]);
        }
    }

    public function contentValuesUpdate($model, $old_values, $new_values)
    {
        return $model->update([
            'old_values' => json_encode($old_values),
            'new_values' => json_encode($new_values)
        ]);
    }

    public function isPreviousStep($content, $currentStep)
    {
        if($currentStep < $content->wizard_step) {
            return true;
        }

        return false;
    }

    /**
     * @param string $currentStep
     * @param App\ContentManager $content
     * @param Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function renderNavigationManager($currentStep, $content, $request, $next)
    {
        if($currentStep <= $content->wizard_step) {
            // SE for uma etapa anterior, retornamos a view desejada.
            return $next();
        } else {
            // SE a etapa for mais avançada do que a registrada.
            // ENTÂO redirecionamos para a etapa registrada.
            return redirect(route('user.institution.socialtecnologies.create.steps', [
                'institution_seo_url' => $request->institution_seo_url,
                'registration' => $content->registration,
                'steps' => $content->wizard_step
            ]))->withErrors('Etapa ainda não finalizada, por favor preencha todos os dados obrigatórios.');
        }
    }
}
