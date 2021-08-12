<?php

namespace App\Http\Controllers;

use App\ContentManager;
use App\SocialTecnology;
use App\SocialTecnologyCustomStepFieldValues;
use App\CustomStepFields;
use App\CustomStepForm;
use App\SocialTecnologyFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Helpers;
use App\Filesystem\Storage;
use Exception;

class SocialTecnologyWizardCustomStepController extends Controller
{
    /**
     * Show and manage custom form
     *
     * @route('user.custom.form.challenge.index')
     * @param Illuminate\Http\Request $request
     * @return View
     */
    public function wizard (Request $request)
    {
        if (empty($request->registration_code)) {
            return redirect()->back()->with('parece que não temos numero de registro nessa inscrição');
        }

        $social_tecnology = SocialTecnology::where('registration', $request->registration_code)->first();
        $content_manager = ContentManager::where('registration', $request->registration_code)->first();

        $current_step = $request->current_step;

        $categoryAward = $social_tecnology->categoryAwardsSubscriptions->first();

        $stepForms = $categoryAward->stepForms()->where('status', 1)->get();

        // case ele tenha parado no passo de review
        if(($current_step - 1) > $stepForms->count()){
            // redirecionamos a ele
            return redirect(route('user.custom.form.challenge.review', ['registration_code' => $social_tecnology->registration]));
        }

        $index_current_step = $current_step - 2;

        $currentForm = $stepForms[$index_current_step] ?? null;
        $currentFormFields = $currentForm->activeStepFields() ?? null;

        // dd($social_tecnology->toArray(), $stepForms->toArray(), $currentForm->toArray(), $currentFormFields->toArray());

        if($current_step < 2) {
            return redirect()->back()->withErrors('ops, essa etapa não existe.');
        }

        // SE a etapa selecionada for menor ou igual a etapa registrada no content_manager
        if ($current_step <= $content_manager->wizard_step) {
            return view('customForms.wizard.index', compact('social_tecnology', 'categoryAward', 'stepForms', 'current_step', 'currentForm', 'currentFormFields'));
        } else {
            return redirect(route('user.custom.form.challenge.index', ['registration_code' => $social_tecnology->registration, 'current_step' => $content_manager->wizard_step]))
                ->withErrors('ops, voce precisa preencher essas informações antes de ir para essa etapa. 🙁');
        }
    }

    /**
     * Store custom form values.
     *
     * @route('user.custom.form.challenge.register')
     * @param Illuminate\Http\Request $request
     * @return mixed
     */
    public function wizard_register (Request $request)
    {
        // valida todos os possiveis campos
        $validated = Validator::make($request->all(), [
            'stepForm_id' => 'required|integer',
        ])->validate();

        //TODO: fazer validação avançada sobre os campos
        //* https://laravel.com/docs/5.6/validation#creating-form-requests
        $fields_validated = Validator::make($request->all(), [
            'text_field.*.*' => 'sometimes',
            'textarea_field.*.*' => 'sometimes',
            'data_fields.*.*' => 'sometimes',
            'state_fields.*.*' => 'sometimes',
            'combo_state_city.*.*' => 'sometimes',
            'attachment_fields.*.*' => 'sometimes',
        ])->validate();

        // dd($request->all(), $fields_validated);

        // busca as models relacionadas.
        $social_tecnology = SocialTecnology::where('registration', $request->registration_code)->first();
        $content_manager = ContentManager::where('registration', $request->registration_code)->first();

        $categoryAward = $social_tecnology->categoryAwardsSubscriptions->first();

        $stepForm = $categoryAward->stepForms()->find($validated['stepForm_id']);

        // verifica o passo para saber se estamos editando ou preenchendo um formulário.
        return $this->wizardStepNavigationController($request->current_step, $content_manager,
            function ($current_step, $isEditing) use ($content_manager, $fields_validated, $stepForm, $social_tecnology, $categoryAward) {

                //* escrever codigo para salvar as informações dos campos aqui...
                // percorremos o array dos campos validados
                foreach ($fields_validated as $fieldType => $type) {
                    // percorremos todos os tipos de campo do formulário
                    foreach ($type as $form_id) { //TODO: retirar o array de form_id dos campos pois já temos uma variavel com ele.
                        // percorremos os id's dos formulários e pegamos os dados do campo
                        foreach ($form_id as $field_id => $value) {
                            // verificamos se o campo existe no formulário
                            $customField = $stepForm->stepFields()->find($field_id);

                            // dump($stepForm->toArray(),$customField->toArray());
                            if(!empty($customField)) {
                                // SE for um combo de estado cidade passamos os valores para JSON
                                if ($fieldType == 'combo_state_city') {
                                    $this->wizardStepFieldValueStore($stepForm, $customField, $social_tecnology, json_encode($value));
                                } elseif( $fieldType == 'attachment_fields'){
                                    $data = [
                                        // todo: a definir melhor
                                        'add_files' => $value['add_files'] ?? [],
                                        'remove_files' => $value['remove_files'],
                                    ];

                                    // dd( $customField , $value , $data);

                                    // dd( $request->all() , $stepForm,$customField,$social_tecnology, $data  );

                                    $this->attachFilesOnChallengeSocialTechnology($stepForm,$customField,$social_tecnology,$data);

                                }else {
                                    $this->wizardStepFieldValueStore($stepForm, $customField, $social_tecnology, $value);
                                }
                            } else {
                                return redirect()->back()->withErrors('Campo Customizado de ID:'.$field_id.' não encontrado');
                            }
                        }
                    }
                }
                // dd( $fields_validated);

                // SENAO estivermos editando um passo incrementamos o `wizard_step`
                if(!$isEditing) {
                    $content_manager->increment('wizard_step');
                }

                // dd(($current_step - 1 ), $categoryAward->stepForms->count(), $isEditing, $content_manager, $stepForm, $fields_validated);

                // verificamos se essa é a ultima etapa.
                if ($categoryAward->stepForms->count() == ($current_step - 1)) {
                    // redirecionar para rota de revisão
                    return redirect(route('user.custom.form.challenge.review', ['registration_code' => $content_manager->registration]));
                } else {
                    $next_step = ($current_step + 1);
                    // redireciona para a proxima etapa do formulário.
                    return redirect(route('user.custom.form.challenge.index', ['registration_code' => $content_manager->registration, 'current_step' => $next_step]));
                }
            }
        );
    }

    /**
     * @param $currentStep
     * @param $content
     * @param $callback
     */
    public function wizardStepNavigationController ($currentStep, $content, $callback)
    {
        $isEditing = false;

        // CASO: a etapa seja incorreta ou maior que a ultima registrada.
        if ($currentStep > $content->wizard_step) {
            return $callback($content->wizard_step, $isEditing);
        }

        // CASO: a etapa seja uma edição de etapas anteriores.
        if ($currentStep < $content->wizard_step) {
            return $callback($currentStep, $isEditing = true);
        }

        return $callback($currentStep, $isEditing);
    }

    /**
     * registra os dados do campo customizado.
     * @return mixed
     */
    public function wizardStepFieldValueStore ($stepForm, $stepField, $tecnology, $value)
    {
        $basic = [
            'customStepForm_id' => $stepForm->id,
            'customStepField_id' => $stepField->id,
            'socialTecnology_id' => $tecnology->id,
        ];

        $data = [
            'value' => $value,
            'fieldLabel' => $stepField->title,
        ];

        DB::table(
            (new SocialTecnologyCustomStepFieldValues)->getTable()
        )->updateOrInsert($basic, $data);
    }

    /**
     * formulário de revisão da categoria de desafio.
     *
     * @route('user.custom.form.challenge.review')
     * @param Illuminate\Http\Request $request
     * @return View
     */
    public function wizardReview (Request $request)
    {
        $social_tecnology = SocialTecnology::where('registration', $request->registration_code)->first();

        $categoryAward = $social_tecnology->categoryAwardsSubscriptions->first();

        $stepForms = $categoryAward->stepForms()->where('status', 1)->get();

        return view('customForms.wizard.review', compact('social_tecnology', 'categoryAward', 'stepForms'));
    }

    /**
     * Concluindo a inscrição da Iniciativa no formulário de desafio.
     *
     * @route('user.custom.form.challenge.complete')
     * @param Illuminate\Http\Request $request
     * @return mixed
     */
    public function wizardCompleteInscription (Request $request)
    {
        //* https://laravel.com/docs/5.6/validation#creating-form-requests
        $fields_validated = Validator::make($request->all(), [
            'text_field.*.*' => 'sometimes',
            'textarea_field.*.*' => 'sometimes',
            'data_fields.*.*' => 'sometimes',
            'state_fields.*.*' => 'sometimes',
            'combo_state_city.*.*' => 'sometimes',
        ])->validate();

        // dd($fields_validated, $request->all());

        $social_tecnology = SocialTecnology::where('registration', $request->registration_code)->first();
        $content_manager = ContentManager::where('registration', $request->registration_code)->first();

        $categoryAward = $social_tecnology->categoryAwardsSubscriptions->first();

        foreach ($fields_validated as $fieldType => $type) {
            foreach ($type as $form_id => $form) {

                $stepForm = $categoryAward->stepForms()->find($form_id);

                if (!empty($stepForm)) {
                    foreach ($form as $field_id => $field_value) {
                        $customField = $stepForm->stepFields()->find($field_id);

                        if (!empty($customField)) {
                            // SE for um combo de estado cidade passamos os valores para JSON
                            if ($fieldType == 'combo_state_city') {
                                $this->wizardStepFieldValueStore($stepForm, $customField, $social_tecnology, json_encode($field_value));
                            } else {
                                $this->wizardStepFieldValueStore($stepForm, $customField, $social_tecnology, $field_value);
                            }
                        } else {
                            return redirect()->back()->withErrors('Campo de formulário não encontrado, ID:'.$field_id);
                        }
                    }
                } else {
                    return redirect()->back()->withErrors('Passo de formulário não encontrado, ID:'.$form_id);
                }
            }
        }

        // mudamos os status das models de social_tecnology e content_manager
        $social_tecnology->update(['status' => ContentManager::STATUS_COMPLETE]);
        $content_manager->update(['status' => ContentManager::STATUS_COMPLETE]);

        // redirecionamos para a pagina da instituição
        return redirect(route('user.institution.show', [
            'institution_seo_url' => $social_tecnology->institution->seo_url
        ]))->with('message', 'Tecnologia cadastrada com sucesso!');
    }

/**
     * Persistir dados dos anexos da Tecnologia Social
     * @param Array $data
     * @param SocialTecnology $social_tecnology
     *
     */
    public function attachFilesOnChallengeSocialTechnology (CustomStepForm $stepForm, CustomStepFields $customField,SocialTecnology $social_tecnology, array $data)
    {
        // inicializando e atribuindo algumas variaveis
        $arr_rm_files = (!is_null($data['remove_files'])) ? explode(',', $data['remove_files']) : [];
        $arr_files_id;
        $arr_new_files_id = collect([]);
        $value;
        // pesquisar se já existe dados persistidos na base nesse campo
        $st_field_value = SocialTecnologyCustomStepFieldValues::where([
            'socialTecnology_id' => $social_tecnology->id,
            'customStepForm_id' => $stepForm->id,
            'customStepField_id' => $customField->id,

        ])->get()->first();

        $arr_files_id = (!is_null($st_field_value)) ? explode(',', $st_field_value->value ) : [];

        // Verifica se há algum arquivo para excluir
        if(!empty($data['remove_files'])){
            foreach ($arr_rm_files as $file_rm_item) {
                if(!empty($file_rm_item)) {

                    // Busca o objeto para excluir o arquivo
                    $file_obj = SocialTecnologyFile::find($file_rm_item);

                    // Exclui do banco de dados
                    $file_obj->delete();
                }
            }
        }

        // retira os ids de anexos excluidos, do array que já estava persistido na base
        $arr_files_id = collect($arr_files_id)->diff($arr_rm_files);

        // Salva os arquivos adicionados no banco de dados
        if(!empty($data['add_files'])) {
            foreach ($data['add_files'] as $file_item) {

                if(!empty($file_item) && $file_item->isValid()) {

                    // Pega o nome da imagem
                    $fileName = $file_item->getClientOriginalName();

                    // Remove os acentos do nome do arquivo
                    $fileName = Helpers::create_file_name_from_existing_name($fileName);

                    // Recupera a extensão do arquivo
                    $extension = $file_item->getClientOriginalExtension();

                    // Verifica se já existe arquivo com o nome
                    $aux_name = 0;
                    $ori_fileName = str_replace('.'.$extension, '', $fileName);
                    while (Storage::exists('socialtecnologies/'.$social_tecnology->id.'/files/'.$fileName)) {
                        $aux_name++;
                        $fileName = $ori_fileName.'('.$aux_name.").".$extension;
                    }

                    // Salva a imagem no banco
                    if($file_item-> storeAs('socialtecnologies/'.$social_tecnology->id."/files", $fileName)){
                        $st_File = SocialTecnologyFile::create([
                            'socialtecnology_id' => $social_tecnology->id,
                            'file' => $fileName
                        ]);
                        // guarda o id do novo anexo persistido
                        $arr_new_files_id->push($st_File->id);
                    }

                }
            }
        }

        // iteração para adicionar os IDs de novos anexos ao já existentess
        foreach($arr_new_files_id as $new_file_id){
            $arr_files_id->push( $new_file_id);
        }

        $value = '';

        // prepara os ids para persistir
        if($arr_files_id->isNotEmpty()){
            foreach($arr_files_id as $key => $item){
                if(!empty($item)){
                    $value = $value  . $item . ',';
                }
            }
        }

        $this->wizardStepFieldValueStore($stepForm, $customField, $social_tecnology, $value);

    }


}
