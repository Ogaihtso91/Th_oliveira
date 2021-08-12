<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Notifications\InstitutionRefused;
use Auth;

class Institution extends Model
{
    use SoftDeletes, Notifiable;

    /*********** PARAMETERS ***********/
    protected $dates = ['deleted_at'];

    protected $notifications_type = [
        'App\Notifications\SocialTecnologyRefused' => 1,
        'App\Notifications\InstitutionRefused' => 2,
        'App\Notifications\EventRefused' => 3,
        'App\Notifications\SocialTecnologyNewComment' => 4,
        'App\Notifications\SocialTecnologyCommentRefused' => 5,
    ];

    protected $fillable = [
        'institution_name', 'image', 'cnpj', 'email', 'phone', 'phone2', 'legal_nature', 'cep', 'address',
        'street', 'number', 'complement', 'neighborhood', 'city', 'state', 'country', 'url', 'social_facebook_page',
        'social_twitter_page', 'social_youtube_page', 'social_instagram_page',
        'responsible_name', 'responsible_phone', 'responsible_email', 'responsible_office_post', 'responsible_other_office_post', 'cod_lumis','seo_url'
    ];

    protected $appends = array('cnpj_formated', 'fulltext_search_institution_places');

    /*********** RELATIONS ***********/
    public function users()
    {
        return $this->belongsToMany(User::class, (new InstitutionUser)->getTable(), 'institution_id', 'user_id');
    }

    public function representatives() {
        return $this->belongsToMany(User::class)
            ->withPivot('office_post', 'representative', 'other_office_post')
            ->wherePivot('representative', 1);
    }

    public function persons() {
        return $this->belongsToMany(User::class)
            ->withPivot('office_post', 'representative', 'other_office_post');
            // ->wherePivot('representative', 0);
    }

    public function socialtecnologies()
    {
        return $this->hasMany(SocialTecnology::class, 'institution_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'institution_id');
    }

    public function manager()
    {
        return $this->hasMany(ContentManager::class, 'model_id')->where('type', ContentManager::TYPE_INSTITUTION);
    }

    /*********** GET CUSTOM PARAMETERS ***********/
    public function getCnpjFormatedAttribute()
    {
        return Helpers::mask(str_pad($this->cnpj, 14, '0', STR_PAD_LEFT), "##.###.###/####-##");
    }

    public function getFulltextSearchInstitutionPlacesAttribute()
    {
        $fulltext = "";

        if($this->address)      $fulltext .= $this->address;
        if($this->neighborhood) $fulltext .= (strlen($fulltext) > 0 ? ", " : "").$this->neighborhood;
        if($this->city)         $fulltext .= (strlen($fulltext) > 0 ? ", " : "").$this->city;
        if($this->state)        $fulltext .= (strlen($fulltext) > 0 ? ", " : "").$this->state;

        return $fulltext;
    }

    /**
     * Save Institution
     * @param Array $data
     * @return $this Institution instance
     */
    public static function store(array $data)
    {
        if (!empty($data['id'])) {

            // Cria objeto para atualizar
            $institution_obj = self::find($data['id']);

            // Armazena os valores antes da alteração para moderação de conteúdo
            $content_manager_old_data = [
                'institution_name' => $institution_obj->institution_name,
                'seo_url' => $institution_obj->seo_url,
                'image' => $institution_obj->image,
                'cnpj' => $institution_obj->cnpj,
                'email' => $institution_obj->email,
                'phone' => $institution_obj->phone,
                'phone2' => $institution_obj->phone2,
                'legal_nature' => $institution_obj->legal_nature,
                'cep' => $institution_obj->cep,
                'address' => $institution_obj->address,
                'street' => $institution_obj->street,
                'number' => $institution_obj->number,
                'complement' => $institution_obj->complement,
                'neighborhood' => $institution_obj->neighborhood,
                'city' => $institution_obj->city,
                'state' => $institution_obj->state,
                'url' => $institution_obj->url,
                'social_facebook_page' => $institution_obj->social_facebook_page,
                'social_twitter_page' => $institution_obj->social_twitter_page,
                'social_youtube_page' => $institution_obj->social_youtube_page,
                'social_instagram_page' => $institution_obj->social_instagram_page,
                'responsible_name' => $institution_obj->responsible_name,
                'responsible_email' => $institution_obj->responsible_email,
                'responsible_office_post' => $institution_obj->responsible_office_post,
                'responsible_other_office_post' => $institution_obj->responsible_other_office_post,
            ];

            // Atualiza as informações
            $institution_obj->update($data);

        } else {

            //Criar url amigável
            $data["seo_url"] = \App\Helpers::slug($data['institution_name']);
            $data["seo_url"] = \App\Helpers::generate_unique_friendly_url($data, new Institution);

            $institution_obj = self::create($data);
        }

        // Armazena os valores para a moderação de conteúdo
        $content_manager_new_data = [
            'institution_name' => $institution_obj->institution_name,
            'seo_url' => $institution_obj->seo_url,
            'image' => $institution_obj->image,
            'cnpj' => $institution_obj->cnpj,
            'email' => $institution_obj->email,
            'phone' => $institution_obj->phone,
            'phone2' => $institution_obj->phone2,
            'legal_nature' => $institution_obj->legal_nature,
            'cep' => $institution_obj->cep,
            'address' => $institution_obj->address,
            'street' => $institution_obj->street,
            'number' => $institution_obj->number,
            'complement' => $institution_obj->complement,
            'neighborhood' => $institution_obj->neighborhood,
            'city' => $institution_obj->city,
            'state' => $institution_obj->state,
            'url' => $institution_obj->url,
            'social_facebook_page' => $institution_obj->social_facebook_page,
            'social_twitter_page' => $institution_obj->social_twitter_page,
            'social_youtube_page' => $institution_obj->social_youtube_page,
            'social_instagram_page' => $institution_obj->social_instagram_page,
            'responsible_name' => $institution_obj->responsible_name,
            'responsible_email' => $institution_obj->responsible_email,
            'responsible_office_post' => $institution_obj->responsible_office_post,
            'responsible_other_office_post' => $institution_obj->responsible_other_office_post
        ];

        // Salva os valores para a moderação
        ContentManager::create([
            'user_id' => ($data['action'] == 'admin.institution.register' ? null : Auth::guard()->user()->id),
            'user_admin_id' => ($data['action'] == 'admin.institution.register' ? Auth::guard('admin')->user()->id : null),
            'is_admin' => ($data['action'] == 'admin.institution.register' ? 1 : 0),
            'institution_id' => $institution_obj->id,
            'model_id' => $institution_obj->id,
            'type' => ContentManager::TYPE_INSTITUTION,
            'old_values' => isset($content_manager_old_data) ? json_encode($content_manager_old_data) : null,
            'new_values' => json_encode($content_manager_new_data),
        ]);

        return $institution_obj;
    }

    /**
     * Revert Specific Institution Update
     * @param Array $data
     * @return $this Institution instance
     */
    public static function revert(array $data)
    {
        // Cria objeto para atualizar
        $institution_obj = self::find($data['id']);

        // Atualiza as informações
        $institution_obj->update($data);

        // Nofitica que foi recusado
        foreach ($institution_obj->users as $user_item) {
            $user_item->notify(new InstitutionRefused($institution_obj, $data['updated_date']));
        }

        return $institution_obj;
    }

    /**
     * Route notifications for the mail channel.
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForMail($notification)
    {
        return $this->responsible_email;
    }
}
