<?php


namespace App\Repositories\Repository\SocialTecnology;

use App\CategoryAward;
use App\SocialTecnology;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;

class GetSocialTecnologyRepository
{
    /**
     * @param CategoryAward $category_award
     * @return mixed
     */
    public function getByCategoryAward(CategoryAward $category_award)
    {
        return SocialTecnology::whereHas('categoryAwardsSubscriptions',
            function($query) use ($category_award) {
                $query->where('category_award_id', $category_award->id);
            }
        )
        ->whereDoesntHave('evaluations',
            function($query) {
                $query->where('evaluator_id', Auth::guard('admin')->user()->id);
            }
        )
        ->whereNull('award_status')
        ->get();
    }

    public function countAll(): int
    {
        return SocialTecnology::count();
    }

    public function countCertifiedTechnologies(): int
    {
        return SocialTecnology::whereNotNull('award_status')->count();
    }
}
