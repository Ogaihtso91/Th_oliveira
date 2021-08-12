<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Enums\FormActions;
use App\Notifications\EventRefused;
use App\Notifications\UserNewEvent;
use Auth;
use Route;

class Event extends Model
{
    use SoftDeletes;

    /*********** PARAMETERS ***********/
    protected $fillable = [
    	'title',
        'theme_id',
    	'description',
    	'address',
    	'neighborhood',
    	'city',
    	'state',
        'image',
        'start_date',
        'end_date',
        'user_id',
        'institution_id',
        'location',
        'start_time',
        'end_time',
        'seo_url',
        'user_class',
        'summary'
    ];

    protected $date = ['start_date', 'end_date', 'deleted_at'];

    protected $appends = [
        'event_date_string',
        'fulltext_search_event_places',
        'event_start_full_date',
        'event_end_full_date'
    ];

    /*********** RELATIONS ***********/
    public function user()
    {
        return $this->belongsTo($this->user_class, 'user_id');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class, 'theme_id');
    }

    public function manager()
    {
        return $this->hasMany(ContentManager::class, 'model_id')->where('type', ContentManager::TYPE_EVENT);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, (new EventUser)->getTable(), 'event_id', 'user_id');
    }

    /*********** GET CUSTOM PARAMETERS ***********/
    public function getFulltextSearchEventPlacesAttribute()
    {
        $fulltext = "";

        if($this->address)      $fulltext .= $this->address;
        if($this->neighborhood) $fulltext .= (strlen($fulltext) > 0 ? ", " : "").$this->neighborhood;
        if($this->city)         $fulltext .= (strlen($fulltext) > 0 ? ", " : "").$this->city;
        if($this->state)        $fulltext .= (strlen($fulltext) > 0 ? ", " : "").$this->state;

        return $fulltext;
    }

    public function getEventDateStringAttribute()
    {
        return \Date::parse($this->start_date)->format('j \d\e F \d\e Y');
    }

    public function getEventStartFullDateAttribute()
    {
        $start_date = \Date::parse($this->start_date);
        return trans('front.days_week.'.$start_date->format('w')).$start_date->format(', j \d\e F \d\e Y');
    }

    public function getEventEndFullDateAttribute()
    {
        $end_date = \Date::parse($this->end_date);
        return trans('front.days_week.'.$end_date->format('w')).$end_date->format(', j \d\e F \d\e Y');
    }

    /**
     * Save Comment
     * @param Array $data
     * @return $this Event instance
     */
    protected function store(array $data)
    {
        // Formata o campo Datetime para salvar
        $data['start_date'] = Carbon::createFromFormat('Y/m/d H:i', $data['formated_start_date']);
        $data['end_date'] = Carbon::createFromFormat('Y/m/d H:i', $data['formated_end_date']);

        // Insere campos do usuário e da instituiçao
        if (!empty($data['id'])) {

            // Cria objeto para atualizar
            $event_obj = self::find($data['id']);

            // Armazena os valores antes da alteração para moderação de conteúdo
            $content_manager_old_data = [
                'title' => $event_obj->title,
                'institution_id' => $event_obj->institution_id,
                'description' => $event_obj->description,
                'address' => $event_obj->address,
                'neighborhood' => $event_obj->neighborhood,
                'city' => $event_obj->city,
                'state' => $event_obj->state,
                'image' => $event_obj->image,
                'start_date' => $event_obj->start_date,
                'end_date' => $event_obj->end_date,
                'location' => $event_obj->location,
                'start_time' => $event_obj->start_time,
                'end_time' => $event_obj->end_time,
                'seo_url' => $event_obj->seo_url,
            ];

            // Atualiza as informações
            $event_obj->update($data);

        } else {

            // Verifica se URL amigável é única
            $data['seo_url'] = Helpers::slug($data['title']);
            $data['seo_url'] = Helpers::generate_unique_friendly_url($data, new Event);

            if ($data['action'] == FormActions::Admin) {
                $data['user_id'] = Auth::guard('admin')->user()->id;
                $data['user_class'] = UserAdmin::class;
            } else {
                $data['user_id'] = Auth::guard()->user()->id;
                $data['user_class'] = User::class;
            }
            $event_obj = self::create($data);

            //Notifica os usuários que foi criado um novo Evento pela Instituição.
            $institution_obj = Institution::find($event_obj->institution_id);
            $ts =  $institution_obj->socialtecnologies;

            foreach ($ts as $item_ts) {
                foreach ($item_ts->recommends as $user_notify) {
                    $user_notify->notify(new UserNewEvent($event_obj->id));
                }
            }
        }

        // Armazena os valores para a moderação de conteúdo
        $event_obj->refresh();

        $content_manager_new_data = [
            'title' => $event_obj->title,
            'institution_id' => $event_obj->institution_id,
            'description' => $event_obj->description,
            'address' => $event_obj->address,
            'neighborhood' => $event_obj->neighborhood,
            'city' => $event_obj->city,
            'state' => $event_obj->state,
            'image' => $event_obj->image,
            'start_date' => $event_obj->start_date,
            'end_date' => $event_obj->end_date,
            'location' => $event_obj->location,
            'start_time' => $event_obj->start_time,
            'end_time' => $event_obj->end_time,
            'seo_url' => $event_obj->seo_url,
        ];

        // Salva os valores para a moderação
        ContentManager::create([
            'user_id' => ($data['action'] == FormActions::Admin ? null : Auth::guard()->user()->id),
            'user_admin_id' => ($data['action'] == FormActions::Admin ? Auth::guard('admin')->user()->id : null),
            'is_admin' => ($data['action'] == FormActions::Admin ? 1 : 0),
            'institution_id' => $event_obj->institution_id,
            'model_id' => $event_obj->id,
            'type' => ContentManager::TYPE_EVENT,
            'old_values' => isset($content_manager_old_data) ? json_encode($content_manager_old_data) : null,
            'new_values' => json_encode($content_manager_new_data),
        ]);

        return $event_obj;
    }

    /**
     * Revert Specific Event Update
     * @param Array $data
     * @return $this Event instance
     */
    protected function revert(array $data)
    {
        // Cria objeto para atualizar
        $event_obj = self::find($data['id']);

        // Atualiza as informações
        $event_obj->update($data);

        // Nofitica que foi recusado
        foreach ($event_obj->institution->users as $user_item) {
            $user_item->notify(new EventRefused($event_obj, $data['updated_date']));
        }

        return $event_obj;
    }
}