<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AwardFile extends Model
{
    protected $table = 'award_files';

    protected $fillable = [
        'file',
        'file_caption',
        'award_id'
    ];

    public function award()
    {
        return $this->belongsTo(Award::class);
    }
}
