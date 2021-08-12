<?php


namespace App\Repositories\Repository\AboutUs;


use App\AboutUs;

class GetAboutUsRepository
{
    public function first()
    {
        return AboutUs::first();
    }
}
