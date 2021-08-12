<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\FavoriteCommentBlog;
use App\Notifications\UserCommentReplyBlog;
use App\Notifications\TimelineUserBlogComments;

class BlogComment extends Model
{
    use SoftDeletes;

    /*********** PARAMETERS ***********/
    protected $table = 'blog_comments';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'blog_id', 'user_id', 'comment_id', 'content'
    ];

    /*********** RELATIONS ***********/
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function blog()
    {
        return $this->belongsTo(Blog::class)->withTrashed();
    }

    public function parent_comment()
    {
        return $this->belongsTo(BlogComment::class, 'comment_id')->withTrashed();
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class, 'comment_id');
    }

    public function manager()
    {
        return $this->hasMany(ContentManager::class, 'model_id')->where('type', ContentManager::TYPE_BLOG_COMMENT);
    }

    /**
     * Save Comment
     * @param Array $data
     * @return $this BlogComment instance
     */
    public static function store(array $data, $user_obj)
    {
        $item_obj = Blog::find($data['blog_id']);
        if (empty($item_obj->id)) {
            return false;
        }

        $model_obj = self::create($data);

        /* Cria os valores para moderação */
        ContentManager::create([
            'user_id' => $data['user_id'],
            'institution_id' => (isset($item_obj->institution_id) ? $item_obj->institution_id : null),
            'model_id' => $model_obj->id,
            'type' => ContentManager::TYPE_BLOG_COMMENT,
            'new_values' => json_encode([
                'comment_id' => $data['comment_id'],
                'content' => $data['content'],
                'item_name' => $item_obj->title,
                'item_seo_url' => $item_obj->seo_url,
            ]),
        ]);

        //Notifica na timeline do usuário o comentário que ele realizou.
        $user_obj->notify(new TimelineUserBlogComments($model_obj->id));

        // Notifica os usuários que favoritaram sobre o novo comentário
        foreach($user_obj->followers as $user_notify) {
            $user_notify->notify(new FavoriteCommentBlog($model_obj->id));
        }

        if (!empty($model_obj->comment_id))
        {
            // Notifica o usuário que teve uma resposta de seu comentário
            $parent_comment = $model_obj->parent_comment;
            $parent_comment->user->notify(new UserCommentReplyBlog($model_obj->id,$data['user_id']));
        }

        return $model_obj;
    }
}