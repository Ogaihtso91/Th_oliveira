<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;
use App\Enums\SocialTecnologiesAwardStatus;
use App\Filesystem\Storage;
use App\Notifications\SocialTecnologyRefused;
use App\Notifications\FollowedSocialTecnologyUpdated;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notifiable;

class SocialTecnology extends Model
{
    use Notifiable, SoftDeletes;

    /*********** PARAMETERS ***********/
    protected $table = 'social_tecnologies';

    protected $dates = ['deleted_at'];

    protected $appends = array('recommended', 'seen', 'award_icon','values_from_custom_fields');

    protected $fillable = [
        'registration',
        'institution_id',
        'award_id',
        'socialtecnology_name',
        'image',
        'summary',
        'objective',
        'non_profit',
        /** tarefa 4373 realizada por marcio.rorsa */
        'general_objective',
        'especific_objective',
        /** fim da tarefa 4373 */
        'problem_solution',
        'description',
        'necessary_resources',
        'deployment_time',
        'result_archieved',
        'remains_active',
        'subscribed_previous',
        'already_had_investment',
        'deploymeny_cost',
        'audience_served',
        'seo_url',
        'status',
        'award_status', // 'w' => Vendecor, 'f' => Finalista, 'c' => Certificado
        'challenge_award_status', // 'w' => Vendecor, 'f' => Finalista // status para categoria do tipo desafios (Não Certificação)
        'award_year',
        'cod_lumis',
        'testimonial',
        'other_audience_options',

    ];



    public function getValuesFromCustomFieldsAttribute() {
        return $this->customStepFieldValues()->get()
        ->map(function($item){
            return [
                'customStepField_id' => $item->customStepField_id,
                'name' => $item->stepField->name,
                'title' => $item->stepField->title,
                'customFieldType_id' => $item->stepField->customFieldType_id,
                'value' => $item->value,
            ];
        })
        ;
    }

    /*********** RELATIONS ***********/

    public function award() {
        return $this->belongsTo(Award::class);
    }

    public function categoryAwardsSubscriptions()
    {
        return $this->belongsToMany(CategoryAward::class)
                ->using(\App\CategoryAwardSocialTecnology::class)
                ->withPivot([
                    'acceptTerms',
                ]);
    }

    public function customStepFieldValues() {
        return $this->hasMany(SocialTecnologyCustomStepFieldValues::class, 'socialTecnology_id');
    }

    public function evaluations() {
        return $this->hasMany(SocialTecnologyEvaluatorStepEvaluation::class, 'socialTecnology_id');
    }
    public function evaluationCriteria() {
        return $this->hasMany(SocialTecnologyCategoryAwardEvaluationCriteria::class, 'socialTecnology_id');
    }
    public function evaluationsStep() {
        return $this->hasMany(SocialTecnologyCategoryAwardEvaluationStep::class, 'socialTecnology_id');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function themes()
    {
        return $this->belongsToMany(Theme::class, (new SocialTecnologyTheme)->getTable(), 'socialtecnology_id', 'theme_id')
            ->withPivot('is_primary_theme');
    }

    public function primaryTheme()
    {
        return $this->belongsToMany(Theme::class, (new SocialTecnologyTheme)->getTable(), 'socialtecnology_id', 'theme_id')
            ->wherePivot('is_primary_theme', 1);
    }

    public function secondaryTheme()
    {
        return $this->belongsToMany(Theme::class, (new SocialTecnologyTheme)->getTable(), 'socialtecnology_id', 'theme_id')
            ->wherePivot('is_primary_theme', 0);
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, (new SocialTecnologyKeyword)->getTable(),'socialtecnology_id', 'keyword_id');
    }

    public function ods()
    {
        return $this->belongsToMany(Ods::class, (new SocialTecnologyOds)->getTable(), 'socialtecnology_id', 'ods_id')->orderBy('number');
    }

    public function deployment_places()
    {
        return $this->hasMany(SocialTecnologyDeploymentPlace::class, 'socialtecnology_id');
    }

    public function files()
    {
        return $this->hasMany(SocialTecnologyFile::class, 'socialtecnology_id');
    }

    public function images()
    {
        return $this->hasMany(SocialTecnologyImage::class, 'socialtecnology_id');
    }

    public function videos()
    {
        return $this->hasMany(SocialTecnologyVideo::class, 'socialtecnology_id');
    }

    public function recommends()
    {
        return $this->belongsToMany(User::class, (new SocialTecnologyRecommend)->getTable(), 'socialtecnology_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(SocialTecnologyComment::class, 'socialtecnology_id');
    }

    public function views()
    {
        return $this->hasMany(SocialTecnologyView::class, 'socialtecnology_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, (new SocialTecnologyUser)->getTable(), 'socialtecnology_id', 'user_id');
    }

    public function manager()
    {
        return $this->hasMany(ContentManager::class, 'model_id')->where('type', ContentManager::TYPE_SOCIALTECNOLOGY);
    }

    public function partners()
    {
        return $this->hasMany(SocialTecnologyPartner::class, 'socialtecnology_id');
    }

    /*********** GET CUSTOM PARAMETERS ***********/
    public function getRecommendedAttribute()
    {
        if (empty(Auth::guard()->user())) return 0;
        return SocialTecnologyRecommend::where('user_id', Auth::guard()->user()->id)->where('socialtecnology_id', $this->id)->count();
    }

    public function getSeenAttribute()
    {
        return SocialTecnologyView::where('ip', preg_replace("/\D/", "", \App\Helpers::get_user_ip()))->where('socialtecnology_id', $this->id)->count();
    }

    public function getAwardIconAttribute()
    {
        switch ($this->award_status) {
            case SocialTecnologiesAwardStatus::WINNER:
                return 'fas fa-trophy';
                break;

            case SocialTecnologiesAwardStatus::FINALIST:
                return 'fas fa-medal';
                break;

            case SocialTecnologiesAwardStatus::CERTIFIED:
                return 'fas fa-medal';
                break;

            default:
                return false;
                break;
        }
    }

    public function getAwardFullNameAttribute()
    {
        switch ($this->award_status) {
            case SocialTecnologiesAwardStatus::WINNER:
                return 'Vencedor';
                break;

            case SocialTecnologiesAwardStatus::FINALIST:
                return 'Finalista';
                break;

            case SocialTecnologiesAwardStatus::CERTIFIED:
                return 'Certificado';
                break;

            default:
                return 'Não certificada';
                break;
        }
    }

    public function getSocialtecnologyNameAttribute($value)
    {
        return mb_convert_case($value, MB_CASE_TITLE);
    }

    public function getSocialTecnologyKeywords()
    {
        if(!empty($this->secondaryTheme()->first()) && !empty($this->primaryTheme()->first())) {
            return $this->primaryTheme()->first()->keywords->merge($this->secondaryTheme()->first()->keywords);
        } elseif(empty($this->secondaryTheme()->first()) && !empty($this->primaryTheme()->first())) {
            return $this->primaryTheme()->first()->keywords;
        } else {
            return [];
        }
    }

    /**
     * Save Social Tecnology
     * @param Array $data
     * @return $this SocialTecnology instance
     */

    public static function store(array $data)
    {
        // Verifica se está atualizando ou salvando
        if (!empty($data['id'])) {

            // Cria objeto para atualizar
            $socialtecnology_obj = self::find($data['id']);

            // Armazena os valores antes da alteração para moderação de conteúdo
            $content_manager_old_data = [
                'institution_id' => $socialtecnology_obj->institution_id,
                'award_id' => $socialtecnology_obj->award_id,
                'socialtecnology_name' => $socialtecnology_obj->socialtecnology_name,
                'image' => $socialtecnology_obj->image,
                //'summary_title' => $socialtecnology_obj->summary_title,
                'summary' => $socialtecnology_obj->summary,
                'objective' => $socialtecnology_obj->objective,
                'problem_solution' => $socialtecnology_obj->problem_solution,
                'description' => $socialtecnology_obj->description,
                'necessary_resources' => $socialtecnology_obj->necessary_resources,
                'result_archieved' => $socialtecnology_obj->result_archieved,
                'status'    => $socialtecnology_obj->status,
                'seo_url' => $socialtecnology_obj->seo_url,
                'videos' => $socialtecnology_obj->videos->pluck('video_url')->toArray(),
                'images' => $socialtecnology_obj->images->pluck('image')->toArray(),
                'files' => $socialtecnology_obj->files->pluck('file')->toArray(),
                'deployment_places' => $socialtecnology_obj->deployment_places->toArray(),
                'themes' => $socialtecnology_obj->themes->pluck('id')->toArray(),
                'keywords' => $socialtecnology_obj->keywords->pluck('id')->toArray(),
                'ods' => $socialtecnology_obj->ods->pluck('id')->toArray(),
                'testimonial' => $socialtecnology_obj->testimonial,
            ];

            // Atualiza as informações
            $socialtecnology_obj->update($data);

        } else {

            // Verifica se URL amigável é única
            $data['seo_url'] = Helpers::slug($data['socialtecnology_name']);
            $data['seo_url'] = Helpers::generate_unique_friendly_url($data, new SocialTecnology);

            // Cria um novo registro
            $socialtecnology_obj = self::create($data);
        }

        /*==================================================
        =                      VÍDEOS                      =
        ==================================================*/

        // Verifica se há algum vídeo para excluir
        if(!empty($data['remove_videos'])){
            $arr_rm_videos = explode(',', $data['remove_videos']);
            foreach ($arr_rm_videos as $video_rm_item) {
                if(!empty($video_rm_item))
                    SocialTecnologyVideo::find($video_rm_item)->delete();
            }
        }

        // Verifica se há novos vídeos adicionados
        if(!empty($data['videos'])) {
            foreach ($data['videos'] as $video_item) {
                if(!empty($video_item))
                    SocialTecnologyVideo::create([
                        'socialtecnology_id' => $socialtecnology_obj->id,
                        'video_url' => $video_item
                    ]);
            }
        }

        /*===============  Fim de VIDEOS ================*/


        /*===================================================
        =                      IMAGENS                      =
        ===================================================*/

        // Verifica se há alguma imagem para excluir
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
                    while (Storage::exists('socialtecnologies/'.$socialtecnology_obj->id.'/images/'.$imageName)) {
                        $aux_name++;
                        $imageName = $ori_imageName.'('.$aux_name.").".$extension;
                    }

                    // Salva a imagem no banco
                    if($image_item->storeAs('socialtecnologies/'.$socialtecnology_obj->id.'/images', $imageName))
                        SocialTecnologyImage::create([
                            'socialtecnology_id' => $socialtecnology_obj->id,
                            'image' => $imageName
                        ]);
                }
            }
        }

        /*===============  Fim de IMAGENS  ================*/


        /*====================================================
        =                      ARQUIVOS                      =
        ====================================================*/

        // Verifica se há algum arquivo para excluir
        if(!empty($data['remove_files'])){
            $arr_rm_files = explode(',', $data['remove_files']);
            foreach ($arr_rm_files as $file_rm_item) {
                if(!empty($file_rm_item)) {

                    // Busca o objeto para excluir o arquivo
                    $file_obj = SocialTecnologyFile::find($file_rm_item);

                    // Exclui do banco de dados
                    $file_obj->delete();
                }
            }
        }

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
                    while (Storage::exists('socialtecnologies/'.$socialtecnology_obj->id.'/files/'.$fileName)) {
                        $aux_name++;
                        $fileName = $ori_fileName.'('.$aux_name.").".$extension;
                    }

                    // Salva a imagem no banco
                    if($file_item-> storeAs('socialtecnologies/'.$socialtecnology_obj->id."/files", $fileName))
                        SocialTecnologyFile::create([
                            'socialtecnology_id' => $socialtecnology_obj->id,
                            'file' => $fileName
                        ]);
                }
            }
        }

        /*=====  Fim de ARQUIVOS  ======*/


        /*=======================================================
        =                 LOCAIS DE IMPLANTAÇÃO                 =
        =======================================================*/
        // Verifica se há algum local de implantação para excluir
        if(!empty($data['remove_deployment_places'])){
            $arr_rm_deployment_places = explode(',', $data['remove_deployment_places']);
            foreach ($arr_rm_deployment_places as $deployment_places_rm_item) {
                if(!empty($deployment_places_rm_item))
                    SocialTecnologyDeploymentPlace::find($deployment_places_rm_item)->delete();
            }
        }

        // Atualiza os registros já cadastrados
        if(!empty($data['stored_deployed_places']) && is_array($data['stored_deployed_places'])) {
            foreach ($data['stored_deployed_places'] as $stored_deployed_place_id) {
                if (!empty($stored_deployed_place_id)
                    && (!empty($data['stored_address_'.$stored_deployed_place_id])
                        || !empty($data['stored_neighborhood_'.$stored_deployed_place_id])
                        || !empty($data['stored_city_'.$stored_deployed_place_id])
                        || !empty($data['stored_state_'.$stored_deployed_place_id])
                        || !empty($data['stored_zipcode_'.$stored_deployed_place_id])))
                    SocialTecnologyDeploymentPlace::find($stored_deployed_place_id)->update([
                        'zipcode' => $data['stored_zipcode_'.$stored_deployed_place_id],
                        'address' => $data['stored_address_'.$stored_deployed_place_id],
                        'neighborhood' => $data['stored_neighborhood_'.$stored_deployed_place_id],
                        'city' => $data['stored_city_'.$stored_deployed_place_id],
                        'state' => $data['stored_state_'.$stored_deployed_place_id],
                    ]);
            }
        }

        // Salva os locais de implantação adicionados no banco de dados
        if(isset($data['count_new_deployment_places']) && $data['count_new_deployment_places'] >= 0) {
            for ($i=0;$i<=$data['count_new_deployment_places'];$i++) {
                if(!empty($data['address_'.$i])
                    || !empty($data['neighborhood_'.$i])
                    || !empty($data['city_'.$i])
                    || !empty($data['state_'.$i])
                    || !empty($data['zipcode_'.$i]))
                    SocialTecnologyDeploymentPlace::create([
                        'socialtecnology_id' => $socialtecnology_obj->id,
                        'zipcode' => $data['zipcode_'.$i],
                        'address' => $data['address_'.$i],
                        'neighborhood' => $data['neighborhood_'.$i],
                        'city' => $data['city_'.$i],
                        'state' => $data['state_'.$i],
                    ]);
            }
        }

        /*==========  Fim de LOCAIS DE IMPLANTAÇÃO  ===========*/


        if (isset($data['action']) && $data['action'] == 'admin') {

            /*==================================================
            =                      TEMAS                       =
            ==================================================*/

            // Deleta valores anteriores do banco de dados
            SocialTecnologyTheme::where('socialtecnology_id', $socialtecnology_obj->id)->delete();

            if(!empty($data['themes'])) {
                // Salva os temas adicionados no banco de dados
                foreach ($data['themes'] as $theme_item) {
                    if(!empty($theme_item))
                        SocialTecnologyTheme::create([
                            'socialtecnology_id' => $socialtecnology_obj->id,
                            'theme_id' => $theme_item
                        ]);
                }
            }

            /*===============  Fim de TEMAS  ================*/

            /*===============================================
            =                      ODS                      =
            ===============================================*/

            // Deleta valores anteriores do banco de dados
            SocialTecnologyOds::where('socialtecnology_id', $socialtecnology_obj->id)->delete();

            if(!empty($data['ods'])) {

                // Salva as ODS adicionadas no banco de dados
                if (!is_array($data['ods'])) $data['ods'] = explode(',', $data['ods']);
                foreach ($data['ods'] as $ods_item) {
                    if(!empty($ods_item))
                        SocialTecnologyOds::create([
                            'socialtecnology_id' => $socialtecnology_obj->id,
                            'ods_id' => $ods_item
                        ]);
                }
            }

            /*===============  Fim de ODS  ================*/
        }


        /*==================================================
        =                      KEYWORDS                       =
        ==================================================*/

            // Deleta valores anteriores do banco de dados
        SocialTecnologyKeyword::where('socialtecnology_id', $socialtecnology_obj->id)->delete();

        if(!empty($data['keywords'])) {

            // Salva os temas adicionados no banco de dados
            foreach ($data['keywords'] as $keyword_item) {

                if(!empty($keyword_item))
                    SocialTecnologyKeyword::create([
                        'socialtecnology_id' => $socialtecnology_obj->id,
                        'keyword_id' => $keyword_item
                    ]);
            }
        }

        /*===============  Fim de KEYWORDS  ================*/



        /*=============================================
        =            USUÁRIOS RESPONSÁVEIS            =
        =============================================*/
        // Verifica se há algum local de implantação para excluir
        if(!empty($data['remove_users'])){
            $arr_rm_users = explode(',', $data['remove_users']);
            foreach ($arr_rm_users as $user_rm_item) {
                if(!empty($user_rm_item))
                    SocialTecnologyUser::where('socialtecnology_id', $socialtecnology_obj->id)
                        ->where('user_id', $user_rm_item)->delete();
            }
        }

        // Adiciona se há novos usuários
        if(!empty($data['users'])) {

            // Salva os usuários adicionados no banco de dados
            foreach ($data['users'] as $user_item) {

                if(!empty($user_item))
                    SocialTecnologyUser::create([
                        'socialtecnology_id' => $socialtecnology_obj->id,
                        'user_id' => $user_item
                    ]);
            }
        }

        /*=====  End of USUÁRIOS RESPONSÁVEIS  ======*/

        // Reinicia as relações do objeto
        $socialtecnology_obj->load('videos', 'images', 'files', 'deployment_places', 'themes', 'keywords', 'ods');

        $socialtecnology_obj->fulltext_keywords = $socialtecnology_obj->keywords->implode('name', ',');
        $socialtecnology_obj->fulltext_themes = $socialtecnology_obj->themes->implode('name', ',');
        $socialtecnology_obj->fulltext_institution = $socialtecnology_obj->institution->institution_name;
        $socialtecnology_obj->save();

        // Armazena os valores para a moderação de conteúdo (novos valores)
        $content_manager_new_data = [
            'institution_id' => $socialtecnology_obj->institution_id,
            'award_id' => $socialtecnology_obj->award_id,
            'socialtecnology_name' => $socialtecnology_obj->socialtecnology_name,
            'image' => $socialtecnology_obj->image,
            //'summary_title' => $socialtecnology_obj->summary_title,
            'summary' => $socialtecnology_obj->summary,
            'objective' => $socialtecnology_obj->objective,
            'problem_solution' => $socialtecnology_obj->problem_solution,
            'description' => $socialtecnology_obj->description,
            'necessary_resources' => $socialtecnology_obj->necessary_resources,
            'result_archieved' => $socialtecnology_obj->result_archieved,
            'status' => $socialtecnology_obj->status,
            'seo_url' => $socialtecnology_obj->seo_url,
            'videos' => $socialtecnology_obj->videos->pluck('video_url')->toArray(),
            'images' => $socialtecnology_obj->images->pluck('image')->toArray(),
            'files' => $socialtecnology_obj->files->pluck('file')->toArray(),
            'deployment_places' => $socialtecnology_obj->deployment_places->toArray(),
            'themes' => $socialtecnology_obj->themes->pluck('id')->toArray(),
            'keywords' => $socialtecnology_obj->keywords->pluck('id')->toArray(),
            'ods' => $socialtecnology_obj->ods->pluck('id')->toArray(),
            'testimonial' => $socialtecnology_obj->testimonial,
        ];

        // Salva os valores para a moderação
        ContentManager::create([
            'user_id' => ($data['action'] == 'admin' ? null : Auth::guard()->user()->id),
            'user_admin_id' => ($data['action'] == 'admin' ? Auth::guard('admin')->user()->id : null),
            'is_admin' => ($data['action'] == 'admin' ? 1 : 0),
            'institution_id' => $socialtecnology_obj->institution_id,
            'model_id' => $socialtecnology_obj->id,
            'type' => ContentManager::TYPE_SOCIALTECNOLOGY,
            'old_values' => isset($content_manager_old_data) ? json_encode($content_manager_old_data) : null,
            'new_values' => json_encode($content_manager_new_data),
        ]);

        // Notifica o usuário que segue a Tecnologia

        if (!empty($data['id'])) {

            foreach ($socialtecnology_obj->recommends as $item_user) {

                $item_user->notify(new FollowedSocialTecnologyUpdated($socialtecnology_obj->id));

            }
        }

        return $socialtecnology_obj;
    }

    /**
     * Revert Specific Social Tecnology Update
     * @param Array $data
     * @return $this SocialTecnology instance
     */
    public static function revert(array $data)
    {

        // Verifica se URL amigável é única
        $data['seo_url'] = Helpers::generate_unique_friendly_url($data, new SocialTecnology);

        // Cria objeto para atualizar
        $socialtecnology_obj = self::find($data['id']);

        // Restaura as informações
        $socialtecnology_obj->update($data);

        /*==================================================
        =                      VÍDEOS                      =
        ==================================================*/

        // Verifica se há algum vídeo para excluir
        SocialTecnologyVideo::where('socialtecnology_id', $socialtecnology_obj->id)->delete();

        // Verifica se há novos vídeos adicionados
        if(!empty($data['videos'])) {
            foreach ($data['videos'] as $video_item) {
                if(!empty($video_item))
                    SocialTecnologyVideo::create([
                        'socialtecnology_id' => $socialtecnology_obj->id,
                        'video_url' => $video_item
                    ]);
            }
        }

        /*===============  Fim de VIDEOS ================*/


        /*===================================================
        =                      IMAGENS                      =
        ===================================================*/

        // Verifica se há alguma imagem para excluir
        SocialTecnologyImage::where('socialtecnology_id', $socialtecnology_obj->id)->delete();

        // Verifica se há imagens para restaurar
        if(!empty($data['images'])) {
            foreach ($data['images'] as $image_item) {
                if(!empty($image_item))
                    SocialTecnologyImage::create([
                        'socialtecnology_id' => $socialtecnology_obj->id,
                        'image' => $image_item
                    ]);
            }
        }
        /*===============  Fim de IMAGENS  ================*/


        /*====================================================
        =                      ARQUIVOS                      =
        ====================================================*/

        // Verifica se há algum arquivo para excluir
        SocialTecnologyFile::where('socialtecnology_id', $socialtecnology_obj->id)->delete();

        // Verifica se há imagens para restaurar
        if(!empty($data['files'])) {
            foreach ($data['files'] as $file_item) {
                if(!empty($file_item))
                    SocialTecnologyFile::create([
                        'socialtecnology_id' => $socialtecnology_obj->id,
                        'file' => $file_item
                    ]);
            }
        }

        /*=====  Fim de ARQUIVOS  ======*/


        /*=======================================================
        =                 LOCAIS DE IMPLANTAÇÃO                 =
        =======================================================*/

        // Verifica se há algum local de implantação para excluir
        SocialTecnologyDeploymentPlace::where('socialtecnology_id', $socialtecnology_obj->id)->delete();

        // Salva os locais de implantação adicionados no banco de dados
        if(!empty($data['deployment_places'])) {
            foreach ($data['deployment_places'] as $deployment_place) {
                if(!empty($deployment_place['address'])
                    || !empty($deployment_place['neighborhood'])
                    || !empty($deployment_place['city'])
                    || !empty($deployment_place['state'])
                    || !empty($deployment_place['zipcode']))
                    SocialTecnologyDeploymentPlace::create([
                        'socialtecnology_id' => $socialtecnology_obj->id,
                        'zipcode' =>$deployment_place['zipcode'],
                        'address' =>$deployment_place['address'],
                        'neighborhood' =>$deployment_place['neighborhood'],
                        'city' =>$deployment_place['city'],
                        'state' =>$deployment_place['state'],
                    ]);
            }
        }

        /*==========  Fim de LOCAIS DE IMPLANTAÇÃO  ===========*/


        /*==================================================
        =                      TEMAS                       =
        ==================================================*/

        // Deleta valores anteriores do banco de dados
        SocialTecnologyTheme::where('socialtecnology_id', $socialtecnology_obj->id)->delete();

        // Salva os temas adicionados no banco de dados
        if(!empty($data['themes'])) {
            foreach ($data['themes'] as $theme_item) {
                if(!empty($theme_item))
                    SocialTecnologyTheme::create([
                        'socialtecnology_id' => $socialtecnology_obj->id,
                        'theme_id' => $theme_item
                    ]);
            }
        }

        /*===============  Fim de TEMAS  ================*/

        /*==========  Fim de LOCAIS DE IMPLANTAÇÃO  ===========*/


        /*==================================================
        =                      KEYWORDS                    =
        ==================================================*/

        // Deleta valores anteriores do banco de dados
        SocialTecnologyKeyword::where('socialtecnology_id', $socialtecnology_obj->id)->delete();

        // Salva os temas adicionados no banco de dados
        if(!empty($data['keywords'])) {
            foreach ($data['keywords'] as $keyword_item) {
                if(!empty($keyword_item))
                    SocialTecnologyKeyword::create([
                        'socialtecnology_id' => $socialtecnology_obj->id,
                        'keyword_id' => $keyword_item
                    ]);
            }
        }

        /*===============  Fim de TEMAS  ================*/


        /*===============================================
        =                      ODS                      =
        ===============================================*/

        // Deleta valores anteriores do banco de dados
        SocialTecnologyOds::where('socialtecnology_id', $socialtecnology_obj->id)->delete();

        // Salva as ODS adicionadas no banco de dados
        if(!empty($data['ods'])) {
            if (!is_array($data['ods'])) $data['ods'] = explode(',', $data['ods']);
            foreach ($data['ods'] as $ods_item) {
                if(!empty($ods_item))
                    SocialTecnologyOds::create([
                        'socialtecnology_id' => $socialtecnology_obj->id,
                        'ods_id' => $ods_item
                    ]);
            }
        }

        /*===============  Fim de ODS  ================*/


        // Nofitica que foi recusado
        foreach ($socialtecnology_obj->users as $user_item) {
            $user_item->notify(new SocialTecnologyRefused($socialtecnology_obj, $data['updated_date']));
        }

        return $socialtecnology_obj;
    }
}
