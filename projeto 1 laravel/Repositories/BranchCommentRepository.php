<?php

namespace App\Repositories;

use App\BranchComment;
use App\Branch;

class BranchCommentRepository
{
    private $model;
    private $branch;

    public function __construct(Branch $branch)
    {
        $this->model = new BranchComment;
        $this->branch = $branch;
    }

    public function create($branch, $data)
    {
        $data['branch_id']  = $branch->id;
        $data['status']     = BranchComment::APPROVED;
        $branchComment      = new BranchComment();
        $branchComment      = $branchComment->create($data);

        $this->updateStarRate($branch);

        return $branchComment;
    }

    public function update($branch, $comment_id, $data)
    {
        $comment = $branch->comments->where('id', $comment_id);
        if(!$comment) throw new \Exception('Comentário não encontrado');
        $comment->update($data);
        return $comment;
    }

    public function updateStarRate($branch)
    {
        $star = $branch->rating()
            ->where('status', BranchComment::APPROVED)
            ->selectRaw('branch_id, IF(LEFT(SUBSTRING_INDEX(AVG(star),".",-1),1) < 5, FLOOR(AVG(star)), CEIL(AVG(star))) AS averange')
            ->groupBy('branch_id')->first();
            
        $star = $star ? $star->averange : 0;
        $branch->update(['number_of_stars' => $star]);
    }

    public function allowReject($branch, $comment_id, $action) 
    {
        $comment = $branch->rating()->where('id', $comment_id)->first();
        if(!$comment) throw new \Exception('Comentário não encontrado');
        $comment->update(['status' => $action == 'aprovar' ? BranchComment::APPROVED : BranchComment::REFUSED ]);
        $this->updateStarRate($branch);
        return $comment;
    }

    public function listPendingFirst($rating)
    {
        return $rating
            ->orderByRaw('IF(status = "P", 0, 1) ASC')
            ->orderBy('created_at', 'DESC');
    }

    public function toDashboard($serviceProvider, $limit = 5)
    {
        $model = $this->model
            ->with('branch')
            ->whereHas('branch', function($b) use($serviceProvider) {
                $b->where('service_provider_id', $serviceProvider->id);
            })
            ->limit($limit);
        
        $ratings = $this
            ->listPendingFirst($model);

        return $ratings;
    }

}