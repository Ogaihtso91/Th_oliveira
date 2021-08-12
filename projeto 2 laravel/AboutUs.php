<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
    protected $table = 'about_us';

    protected $fillable = [
        'banner_section_one',
        'banner_section_three',
        'banner_section_five',
        'text_one_section_one',
        'text_one_section_two',
        'text_two_section_two',
        'text_one_section_three',
        'text_one_section_four',
        'text_two_section_four',
        'text_one_section_five',
        'text_one_section_six',
        'text_two_section_six',
        'text_three_section_six',
    ];
}
