<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchGallery extends Model
{
    private $id;
    private $branch_id;
    private $path;
    private $order;

    protected $fillable = ['branch_id', 'path', 'order'];

    protected $appends = ['full_path'];

    const PATH = 'assets/uploads/branches/:branch_id:/';
    
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getFullPathAttribute()
    {
        $path = self::getFolder($this->attributes['branch_id']);

        if(!is_dir($path)) mkdir($path, 0755, true);
        
        if(stripos($this->attributes['path'], 'http') !== false) {
            return $this->attributes['path'];
        } else {
            return $path . $this->attributes['path'];
        }
    }

    public static function getFolder($branch_id)
    {
        return str_replace(':branch_id:', $branch_id, self::PATH);
    }
}
