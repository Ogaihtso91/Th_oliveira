<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use App\Filesystem\Storage;
use App\Notifications\UserResetPassword;
use App\Notifications\UserFavorited;
use Auth;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes;

    /*********** PARAMETERS ***********/
    protected $dates = ['deleted_at', 'last_login'];

    protected $fillable = [
        'name',
        'institution_id',
        'cpf',
        'email',
        'image',
        'password',
        'fb_login',
        'google_login',
        'seo_url',
        'sex',
        'schooling',
        'phone',
        'cell_phone',
        'address',
        'neighborhood',
        'city',
        'state',
        'country',
        'birth_date',
        'theme_id'
    ];

    protected $hidden = [
        'password', 'remember_token', 'fb_login', 'google_login'
    ];

    /*********** RELATIONS ***********/

    public function events()
    {
        return $this->belongsToMany(Event::class, (new EventUser)->getTable(), 'user_id', 'event_id');
    }

    // Instituição do usuário
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    // Instituições que o usuário é responsável
    public function institutions () {
        return $this->belongsToMany(Institution::class)
            ->withPivot('office_post', 'representative');
    }

    // Tecnologias que o usuário é responsável
    public function socialtecnologies()
    {
        return $this->belongsToMany(SocialTecnology::class, (new SocialTecnologyUser)->getTable(), 'user_id', 'socialtecnology_id');
    }

    // Tecnologias que o usuário segue
    public function socialtecnologiesrecommended()
    {
        return $this->belongsToMany(SocialTecnology::class, (new SocialTecnologyRecommend)->getTable(), 'user_id', 'socialtecnology_id');
    }

    // Usuários favoritos do usuário
    public function favorites()
    {
        return $this->belongsToMany(User::class, (new UserFavorite)->getTable(), 'user_id', 'fav_user_id');
    }

    // Usuários que favoritaram o usuário
    public function followers()
    {
        return $this->belongsToMany(User::class, (new UserFavorite)->getTable(), 'fav_user_id', 'user_id');
    }

    public function themes()
    {
        return $this->belongsToMany(Theme::class, (new UserTheme)->getTable(), 'user_id', 'theme_id');
    }

    /*********** FUNCTIONS ***********/
    public function isResponsible($socialtecnology_id) {
        return $this->socialtecnologies->contains($socialtecnology_id);
    }

    public function isFavorited($user_id) {
        return $this->favorites->contains($user_id);
    }

    /**
     * Send the password reset notification.
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new UserResetPassword($token));
    }

    /**
     * Send notification when somenone favorited user's profile.
     * @param  string  $token
     * @return void
     */
    public function sendUserFavoritedNotification($user_send_id)
    {
        $this->notify(new UserFavorited($user_send_id));
    }

    /**
     * Save User
     * @param Array $data
     * @return $this User instance
     */
    protected function store(array $data)
    {
        // Formata o campo Date para salvar no BD
        if(!empty($data['birth_date'])) {

            $data['birth_date'] = Carbon::createFromFormat('d/m/Y', $data['birth_date']);
        }

        //Seo_url para usuário
        $data["seo_url"] = Helpers::slug($data['name']);
        $data["seo_url"] = Helpers::generate_unique_friendly_url($data, new User);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $data['institution_id'] = $data['institution_id'] ?: null;

        if (!empty($data['id'])) {

            $user_obj = User::find($data['id']);

            // Remove a imagem anterior
            if (!empty($data['image']) && !empty($user_obj->image)) Storage::delete('users/'.$user_obj->image);

            // Atualiza Dados
            $user_obj->update($data);

        } else {

            // Cria o Usuário
            $user_obj = self::create($data);

        }

        //Deleta valores anteriores do banco de dados
        $user_obj->themes()->detach();

        if(!empty($data['themes'])) {

            // Salva as themes adicionadas no banco de dados
            if (!is_array($data['themes'])) $data['themes'] = explode(',', $data['themes']);

            // Salva os temas adicionados no banco de dados
            foreach ($data['themes'] as $themes_item) {
                if(!empty($themes_item))
                    $user_obj->themes()->attach($themes_item);
            }
        }

        return $user_obj;
    }

    /**
     * Comment on specific Item
     * @param string $type
     * @param array $data
     * @return $this User instance
     */
    public function commentItem($type, $data) {

        // Verifica o tipo de comentário
        switch ($type) {

            // Tecnologia Social
            case 'socialtecnology':

                if (!SocialTecnologyComment::store([
                    'user_id'               => $this->id,
                    'socialtecnology_id'    => $data['item_id'],
                    'comment_id'            => $data['comment_id'],
                    'content'               => $data['content'],
                ], $this)) {
                    return false;
                }
            break;

            /* Blog */
            case 'blog':

                if (!BlogComment::store([
                    'user_id'       => $this->id,
                    'blog_id'       => $data['item_id'],
                    'comment_id'    => $data['comment_id'],
                    'content'       => $data['content'],
                ], $this)) {
                    return false;
                }
            break;

            default:
                return false;
                break;
        }

        return true;
    }
}
