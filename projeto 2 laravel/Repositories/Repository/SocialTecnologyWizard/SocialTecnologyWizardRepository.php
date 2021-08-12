<?php


namespace App\Repositories\Repository\SocialTecnologyWizard;

use App\ContentManager;
use App\Filesystem\Storage;
use App\Helpers;
use App\Notifications\FollowedSocialTecnologyUpdated;
use App\Services\SocialTecnologyWizard\SocialTecnologyWizardService;
use App\SocialTecnology;
use App\SocialTecnologyDeploymentPlace;
use App\SocialTecnologyFile;
use App\SocialTecnologyImage;
use App\SocialTecnologyPartner;
use App\SocialTecnologyVideo;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;

class SocialTecnologyWizardRepository
{
    /**
     * @param string $registrationCode
     * @return mixed
     */
    function getSocialTecnologyByRegister($registrationCode)
    {
        return SocialTecnology::where('registration', $registrationCode)->first();
    }

    /**
     * @param string $registrationCode
     * @return mixed
     */
    public function getContentManagerByRegister($registrationCode)
    {
        return ContentManager::where('registration', $registrationCode)->first();
    }

    /**
     * @param ContentManager $contentManager instancia do ContentManager
     * @param boolean $old_values busca antigo valores no ContentManager
     * @return Collection uma coleção com os valores armazenados no content manager.
     */
    public function getContentManagerValues($contentManager, $old_values = false)
    {
        if($old_values) {
            return Collection::unwrap(json_decode($contentManager->old_values));
        }

        return Collection::unwrap(json_decode($contentManager->new_values));
    }

    public function getTecnologyAndContentByRegister($registrationCode)
    {
        $tecnology = $this->getSocialTecnologyByRegister($registrationCode);
        $content = $this->getContentManagerByRegister($registrationCode);

        return (object) compact('tecnology', 'content');
    }

    /**
     *  registra ou atualiza os dados das tecnologias sociais
     * e os dados do content manager
     * @param array $data dados preenchidos no formulário
     * @param boolean $previousStep flag para saber se está editando etapa anterior
     * @return mixed
     */
    function storeOrUpdate($data, $previousStep) {
        if(empty($data['registration'])) {
            return $this->storeSync($data);
        } else {
            return $this->updateSync($data, $previousStep);
        }
    }

    /**
     * Armazenando é sincronizando os dados da
     * tecnologia social e gerenciador de conteudo.
     * @param array $data
     * @return mixed
     */
    function storeSync($data)
    {
        try {
            // criamos dados iniciais esenciais para o registro
            $data['registration'] = generate_unique_registration_code();
            $data['seo_url'] = generate_unique_url($data['socialtecnology_name'], new SocialTecnology());
            $data['status'] = ContentManager::STATUS_PENDING;

            // armazenamos os dados iniciais da tecnologia social.
            $tecnology = $this->storeSocialTecnology($data);

            // passamos os dados da tecnologia para json
            // para poder armazenar no content manager.
            $data['new_values'] = $tecnology->toArray();

            // armazenamos os dados iniciais do gerenciador de conteudo.
            $content = $this->storeContentManager($data, $tecnology);

            // ao finalizar o armazenamento inicial retornamos
            // os registro como objetos
            return (object) compact('tecnology', 'content');
        }
        catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * @param array $data
     * @param string $step
     * @return mixed
     */
    function updateSync($data, $previousStep)
    {
        try {
            // buscamos as instancias dos conteudos
            $tecnology = SocialTecnology::where('registration', $data['registration'])->first();
            $content = ContentManager::where('registration', $data['registration'])->first();

            // removemos o codigo de registro e instituição
            unset($data['registration'], $data['institution_id']);

            // atualizamos os dados de registro da tecnologia social
            $tecnology->update($data);

            // atualizamos os dados registrado no content manager
            $content->update([
                'old_values' => $content->new_values ?? null,
                'new_values' => json_encode($tecnology->toArray())
            ]);

            $this->wizardStepUpdate($content, $previousStep);

            return (object) compact('tecnology', 'content');
        }
        catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * @param array $data
     * @return mixed
     */
    function storeSocialTecnology($data)
    {
        try {
            return SocialTecnology::create([
                'seo_url' => $data['seo_url'],
                'status' => $data['status'],
                'registration' => $data['registration'],
                'institution_id' => $data['institution_id'],
                'socialtecnology_name' => $data['socialtecnology_name']
                // 'image' => $data['image'] ?? null,
                // 'summary' => $data['summary'] ?? null,
                // 'objective' => $data['objective'] ?? null,
                // 'non_profit' => $data['non_profit'] ?? null,
                // 'general_objective' => $data['general_objective'] ?? null,
                // 'especific_objective' => $data['especific_objective'] ?? null,
                // 'problem_solution' => $data['problem_solution'] ?? null,
                // 'description' => $data['description'] ?? null,
                // 'necessary_resources' => $data->necessary_resources ?? null,
                // 'deployment_time' => $data->deployment_time ?? null,
                // 'result_archieved' => $data->result_archieved ?? null,
                // 'remains_active' => $data->remains_active ?? null,
                // 'subscribed_previous' => $data->subscribed_previous ?? null,
                // 'already_had_investment' => $data->already_had_investment ?? null,
                // 'deploymeny_cost' => $data->deploymeny_cost ?? null,
                // 'audience_served' => $data->audience_served ?? null,
                // 'award_status' => $data->award_status ?? null,
                // 'testimonial' => $data->testimonial ?? null
            ]);
        }
        catch(Exception $e) {
            dd($e);
        }
    }

    /**
     * @param array $data
     * @param object $model
     * @return mixed
     */
    function storeContentManager($data, $model)
    {
        // ajustando dados antes de registrar
        return ContentManager::store([
            'institution_id' => $data['institution_id'],
            'model_id' => $model->id,
            'type' => ContentManager::TYPE_SOCIALTECNOLOGY,
            'old_values' => $data['old_values'] ?? null,
            'new_values' => $data['new_values'] ?? null,
            'registration' => $data['registration'],
            'status' => ContentManager::STATUS_PENDING,
            'wizard_step' => SocialTecnologyWizardService::SECOND_STEP
        ]);
    }

    /**
     * @param object $content
     * @param boolean $previousStep
     * @return mixed
     */
    function wizardStepUpdate($content, $previousStep)
    {
        if(!$previousStep && $content->wizard_step < 11) {
            return $content->update([
                'wizard_step' => ($content->wizard_step + 1)
            ]);
        }
    }

    // TODO: remover função
    // function updateFullText($tecnology)
    // {
    //     return $tecnology->update([
    //         'fulltext_keywords' => $tecnology->keywords->implode('name', ','),
    //         'fulltext_themes' => $tecnology->themes->implode('name', ','),
    //         'fulltext_institution' => $tecnology->institution->seo_url,
    //     ]);
    // }

    /**
     * @param ContentManager $content
     * @param SocialTecnology $tecnology
     * @return mixed
     */
    function completedInscription($content, $tecnology)
    {
        // mudamos o status do conteudo e da tecnologia para
        // indicar que a inscrição está completa
        $content->update(['status' => ContentManager::STATUS_COMPLETE]);
        $tecnology->update(['status' => ContentManager::STATUS_COMPLETE]);

        // geramos os FullText
        $tecnology->fulltext_keywords = $tecnology->keywords->implode('name', ',');
        $tecnology->fulltext_themes = $tecnology->themes->implode('name', ',');
        $tecnology->fulltext_institution = $tecnology->institution->institution_name;
        $tecnology->save();

        // notificamos os usuários que seguem a tecnologia
        foreach ($tecnology->recommends as $user) {
            $user->notify(new FollowedSocialTecnologyUpdated($tecnology->id));
        }

        // enviamos o email confirmando a conclusão da inscrição
        if($tecnology->award->messages()->where('type', 1)->get()->count() > 1) {
            Mail::to($tecnology->institution->email)->send( new \App\Mail\SendConfirmationSubscriptionMail( $tecnologiasocial ) );
        }

        return (object) compact('tecnology', 'content');
    }

    function updateContentManagerValues($content, $new_values)
    {
        return $content->update([
            'old_values' => $content->new_values,
            'new_values' => json_encode($new_values)
        ]);
    }

    //=================| Relationships |=================//

    /**
     * @param Collection $model
     * @param int $award_id
     * @return mixed
     */
    function setSocialTecnologyAwardRelation($model, $award_id)
    {
        return $model->update(['award_id' => $award_id]);
    }

    /**
     * @param object $model
     * @param int $category_id
     * @param boolean $unique
     * @return mixed
     */
    function setSocialTecnologyCategoryAwardRelation($model, $category_award)
    {
        $model->categoryAwardsSubscriptions()->detach();

        foreach($category_award as $key => $category) {
            $model->categoryAwardsSubscriptions()->attach($category);
        }
    }

    //================| User's Relationship |================//
    function setSocialTecnologyUsersRelation($model, $users)
    {
        foreach($users as $user) {
            if(empty($model->users->where('id', $user)->first())) {
                $model->users()->attach($user);
            }
        }

        return $model;
    }

    function deleteSocialTecnologyUsersRelation($model, $users)
    {
        foreach($users as $user) {
            $model->users()->detach($user);
        }

        return $model;
    }

    //================| Primary Theme Relationship |================//
    function setPrimaryThemeRelationship($model, $primaryTheme, $unique = false)
    {
        if($unique) {
            $model->primaryTheme()->detach();
        }

        return $model->primaryTheme()->attach($primaryTheme, ['is_primary_theme' => 1]);
    }

    //================| Secondary Theme Relationship |================//
    function setSecondaryThemeRelationship($model, $secondaryTheme, $unique = false)
    {
        if($unique) {
            $model->secondaryTheme()->detach();
        }

        return $model->secondaryTheme()->attach($secondaryTheme, ['is_primary_theme' => 0]);
    }

    //================| Keywords Relationship |================//
    function setKeywordsRelationship($model, $keywords)
    {
        if (!empty($keywords)) {
            $words = explode(',', $keywords);
            return $model->keywords()->sync($words);
        }
    }

    //================| Deployment Places Relationship |================//

    function setDeploymentPlacesRelationship($model, $deploymentPlaces)
    {
        if(!empty($deploymentPlaces)) {
            foreach ($deploymentPlaces as $place) {
                SocialTecnologyDeploymentPlace::create([
                    'socialtecnology_id' => $model->id,
                    'neighborhood' => $place['neighborhood'],
                    'city' => $place['city'],
                    'state' => $place['state'],
                    'active' => $place['active']
                ]);
            }
        }

        return $model;
    }

    function deleteDeploymentPlacesRelationship($items)
    {
        if(!empty($items)) {
            $deployment_places = explode(',', $items);
            foreach ($deployment_places as $deployment_places_item) {
                if(!empty($deployment_places_item)) {
                    SocialTecnologyDeploymentPlace::find($deployment_places_item)->delete();
                }
            }
        }
    }

    //================| Deployment Places Relationship |================//

    function setPartnerRelationship($model, $items)
    {
        // verifica se não está vazio
        if(!empty($items)) {
            foreach ($items as $item) {
                SocialTecnologyPartner::create([
                    'socialtecnology_id' => $model->id,
                    'institution_name' => $item['new_institution'],
                    'acting' => $item['acting']
                ]);
            }
        }
    }

    //================| Attachments Relationship |================//

    /** adiciona relacionamento com Videos */
    function setSocialTecnologyVideo($model_id, $data) {
        if(!empty($data['videos'])) {
            foreach ($data['videos'] as $video_item) {
                if(!empty($video_item)) {
                    SocialTecnologyVideo::create([
                        'socialtecnology_id' => $model_id,
                        'video_url' => $video_item
                    ]);
                }
            }
        }
    }

    /** remove o relacionamento com video */
    function removeSocialTecnologyVideo($data) {
        // Verifica se há algum vídeo para excluir
        if(!empty($data['remove_videos'])){
            $arr_rm_videos = explode(',', $data['remove_videos']);
            foreach ($arr_rm_videos as $video_rm_item) {
                if(!empty($video_rm_item))
                    $video = SocialTecnologyVideo::find($video_rm_item);
                    if(!empty($video)) {
                        return $video->delete();
                    }
            }
        }
    }

    /** adiciona o relacionamento com file */
    public function setSocialTecnologyFile($model_id, $data) {
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
                    while (Storage::exists('socialtecnologies/'.$model_id.'/files/'.$fileName)) {
                        $aux_name++;
                        $fileName = $ori_fileName.'('.$aux_name.").".$extension;
                    }

                    // Salva a imagem no banco
                    if($file_item-> storeAs('socialtecnologies/'.$model_id."/files", $fileName))
                        SocialTecnologyFile::create([
                            'socialtecnology_id' => $model_id,
                            'file' => $fileName
                        ]);
                }
            }
        }

    }

    /** remove o relacionamento com file */
    public function removeSocialTecnologyFile($data) {
        if(!empty($data['remove_files'])){
            $arr_rm_files = explode(',', $data['remove_files']);
            foreach ($arr_rm_files as $file_rm_item) {
                if(!empty($file_rm_item)) {
                    // Busca o objeto para excluir o arquivo
                    $file_obj = SocialTecnologyFile::find($file_rm_item);
                    if(!empty($file_obj)) {
                        // excluimos do banco de dados
                        $file_obj->delete();
                    }
                }
            }
        }
    }


    /** Tarefa 4373 adiciona o relacionamento com imagens principal feita por marcio.rosa*/
    public function setSocialTecnologyImagesMain($model, $data) {
        if(!empty($data['main_images'])) {

            //Problem identificated here
            foreach ($data['main_images'] as $image_item){
                if(!empty($image_item) && $image_item->isValid()) {
                    // Pega o nome da imagem
                    $imageName = $image_item->getClientOriginalName();
                    // Recupera a extensão do arquivo
                    $extension = $image_item->getClientOriginalExtension();
                    // Verifica se já existe arquivo com o nome
                    $aux_name = 0;
                    $ori_imageName = str_replace('.'.$extension, '', $imageName);
                    while (Storage::exists('socialtecnologies/'.$model->id.'/images/'.$imageName)) {
                        $aux_name++;
                        $imageName = $ori_imageName.'('.$aux_name.").".$extension;
                    }
                    // Salva a imagem no banco
                    if($image_item->storeAs('socialtecnologies/'.$model->id.'/images', $imageName))
                        $model->update([
                            'image' => $imageName
                        ]);
                }
            }
        }
    }

    /** remove o relacionamento com imagem principal feita por marcio.rosa */
    public function removeSocialTecnologyImageMain($data) {
        if(!empty($data['remove_images'])){
            $arr_rm_images = explode(',', $data['remove_images']);
            foreach ($arr_rm_images as $image_rm_item) {
                if(!empty($image_rm_item)) {
                    // Busca o objeto para excluir o arquivo
                    $image_obj = SocialTecnologyImage::find($image_rm_item);

                    // Exclui do banco de dados
                    $image_obj->delete();
                }
            }
        }
    }
    // fim da tarefa 4373


    /** adiciona o relacionamento com imagens */
    public function setSocialTecnologyImages($model_id, $data) {
        if(!empty($data['add_images'])) {
            foreach ($data['add_images'] as $image_item) {
                if(!empty($image_item) && $image_item->isValid()) {

                    // Pega o nome da imagem
                    $imageName = $image_item->getClientOriginalName();

                    // Recupera a extensão do arquivo
                    $extension = $image_item->getClientOriginalExtension();

                    // Verifica se já existe arquivo com o nome
                    $aux_name = 0;
                    $ori_imageName = str_replace('.'.$extension, '', $imageName);
                    while (Storage::exists('socialtecnologies/'.$model_id.'/images/'.$imageName)) {
                        $aux_name++;
                        $imageName = $ori_imageName.'('.$aux_name.").".$extension;
                    }
                    // Salva a imagem no banco
                    if($image_item->storeAs('socialtecnologies/'.$model_id.'/images', $imageName))
                        SocialTecnologyImage::create([
                            'socialtecnology_id' => $model_id,
                            'image' => $imageName,
                        ]);
                }
            }
        }
    }

    /** remove o relacionamento com imagem */
    public function removeSocialTecnologyImage($data) {
        if(!empty($data['remove_images'])){
            $arr_rm_images = explode(',', $data['remove_images']);
            foreach ($arr_rm_images as $image_rm_item) {
                if(!empty($image_rm_item)) {
                    // Busca o objeto para excluir o arquivo
                    $image_obj = SocialTecnologyImage::find($image_rm_item);

                    // Exclui do banco de dados
                    $image_obj->delete();
                }
            }
        }
    }
}
