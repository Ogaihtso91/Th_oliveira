<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ContentManager;
use App\NotificationsConfig;
use App\Notifications\SocialTecnologyNewComment;
use App\Notifications\SocialTecnologyCommentRefused;
use App\Notifications\FavoriteCommentSocialTecnology;
use App\Notifications\FollowedSocialTecnologyComment;
use App\Notifications\UserCommentReplySocialTecnology;
use App\Notifications\TimelineUserSocialTecnologiesComments;
use Auth;

class SocialTecnologyComment extends Model
{
    use SoftDeletes;

    /*********** PARAMETERS ***********/
    protected $table = 'social_tecnologies_comments';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'socialtecnology_id', 'user_id', 'comment_id', 'content'
    ];

    /*********** RELATIONS ***********/
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function socialtecnology()
    {
        return $this->belongsTo(SocialTecnology::class, 'socialtecnology_id')->withTrashed();
    }

    public function parent_comment()
    {
        return $this->belongsTo(SocialTecnologyComment::class, 'comment_id')->withTrashed();
    }

    public function comments()
    {
        return $this->hasMany(SocialTecnologyComment::class, 'comment_id');
    }

    public function manager()
    {
        return $this->hasMany(ContentManager::class, 'model_id')->where('type', ContentManager::TYPE_SOCIALTECNOLOGY_COMMENT);
    }

    /**
     * Save Social Tecnology Comment
     * @param Array $data
     * @return $this SocialTecnologyComment instance
     */
    public static function store(array $data, $user_obj)
    {
        $item_obj = SocialTecnology::find($data['socialtecnology_id']);
        if (empty($item_obj->id)) {
            return false;
        }

        $model_obj = self::create($data);

        /* Cria os valores para moderação */
        ContentManager::create([
            'user_id' => $data['user_id'],
            'institution_id' => (isset($item_obj->institution_id) ? $item_obj->institution_id : null),
            'model_id' => $model_obj->id,
            'type' => ContentManager::TYPE_SOCIALTECNOLOGY_COMMENT,
            'new_values' => json_encode([
                'comment_id' => $data['comment_id'],
                'content' => $data['content'],
                'item_name' => $item_obj->socialtecnology_name,
                'item_seo_url' => $item_obj->seo_url,
            ]),
        ]);

        //Notifica na timeline do usuário o comentário que ele realizou.
        $user_obj->notify(new TimelineUserSocialTecnologiesComments($model_obj->id));

        // Notifica a instituição
        foreach ($item_obj->users as $user_item) {
            $user_item->notify(new SocialTecnologyNewComment($model_obj));
        }

        // Notifica os usuários que favoritaram sobre o novo comentário
        foreach($user_obj->followers as $user_notify) {
            $user_notify->notify(new FavoriteCommentSocialTecnology($model_obj->id));
        }

        // Notifica todos os usuários que seguem a TS sobre o novo comentário
        $followed_social_tecnology_comment_notif_obj = new FollowedSocialTecnologyComment($model_obj->id);

        foreach ($item_obj->recommends as $user_follower) {

            //Retorna a configuração de notificação do usuário
            $user_notifications_config = NotificationsConfig::getUserNotificationConfig($user_follower->id);

            //Verifica se tem configuração de notificação do usuário
            if(!empty($user_notifications_config)){

                //Grava o valor do campo "silenced"
                $silenced = $user_notifications_config[get_class($followed_social_tecnology_comment_notif_obj)]['silenced'];
            }

            //Se for vazio ou o valor do campo "silenced" for falso, grava a notificação
            if(empty($silenced)) {

                $user_follower->notify($followed_social_tecnology_comment_notif_obj);

            }

        }

        if (!empty($model_obj->comment_id))
        {
            // Notifica o usuário que teve uma resposta de seu comentário
            $parent_comment = $model_obj->parent_comment;
            $parent_comment->user->notify(new UserCommentReplySocialTecnology($model_obj->id,$data['user_id']));
        }

        return $model_obj;
    }

    /**
     * Delete Comment from Database
     * @param Array $data
     * @return $this SocialTecnology instance
     */
    public function delete()
    {
        if (is_null($this->getKeyName())) {
            throw new Exception('No primary key defined on model.');
        }

        // If the model doesn't exist, there is nothing to delete so we'll just return
        // immediately and not do anything else. Otherwise, we will continue with a
        // deletion process on the model, firing the proper events, and so forth.
        if (! $this->exists) {
            return;
        }

        if ($this->fireModelEvent('deleting') === false) {
            return false;
        }

        // Nofitica a instituição
        if (!empty($this->socialtecnology())) {
            foreach ($this->socialtecnology()->with('users')->first()->users as $user_item) {
                $user_item->notify(new SocialTecnologyCommentRefused($this));
            }
        }

        // Here, we'll touch the owning models, verifying these timestamps get updated
        // for the models. This will allow any caching to get broken on the parents
        // by the timestamp. Then we will go ahead and delete the model instance.
        $this->touchOwners();

        $this->performDeleteOnModel();

        // Once the model has been deleted, we will fire off the deleted event so that
        // the developers may hook into post-delete operations. We will then return
        // a boolean true as the delete is presumably successful on the database.
        $this->fireModelEvent('deleted', false);

        return true;
    }
}