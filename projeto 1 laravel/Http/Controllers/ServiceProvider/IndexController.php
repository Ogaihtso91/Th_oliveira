<?php
/**
 * Created by PhpStorm.
 * User: thiag
 * Date: 11/08/2018
 * Time: 14:48
 */

namespace App\Http\Controllers\ServiceProvider;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\ServiceProvider;
use App\Repositories\BranchCommentRepository;

class IndexController extends ServiceProviderBaseController
{
    public function index(
        BranchCommentRepository $branchCommentRepository)
    {
        $ratings = $branchCommentRepository->toDashboard($this->serviceProvider)->get();
        $branches = $this->serviceProvider->branches;
        return view('serviceProvider.index.index', compact('ratings', 'branches'));
    }
}