<?php
namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\BranchRepository;
use App\Repositories\BranchCommentRepository;
use App\Repositories\TypeProviderRepository;
use Illuminate\Support\Facades\DB;

class PagesController extends SiteBaseController
{
    private $branchRepository;
    private $typeProviderRepository; 
    public function __construct(
        BranchRepository $branchRepository,
        TypeProviderRepository $typeProviderRepository,
        BranchCommentRepository $branchCommentRepository
        )
    {
        $this->branchRepository = $branchRepository;
        $this->typeProviderRepository = $typeProviderRepository;
        $this->branchCommentRepository = $branchCommentRepository;
        parent::__construct();
    }

    public function index()
    {
        $typeProviders = [null => 'Todos'] + $this->typeProviderRepository->getList()->toArray();
        return view('site.pages.index', compact('typeProviders'));
    }

    public function details(Request $r, $slug)
    {
        $id = explode('-', $slug);
        $id = end($id);
        try {
           $branch = $this->branchRepository->findDetails($id);
        } catch(\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return view('site.pages.details', compact('branch'));
    }

    public function rating(Request $r, $slug)
    {
        $id = explode('-', $slug);
        $id = end($id);
        try {
           $branch = $this->branchRepository->findDetails($id);
           if($r->isMethod('post')) {
                DB::beginTransaction();
                    $this->branchCommentRepository->create($branch, array_only($r->get('rating'), ['commentary', 'star', 'user_id', 'branch_id']));
                DB::commit();

                return redirect()->back()->with('success', 'Comentário recebido com sucesso, em breve aparecerá no site');
           }
        } catch(\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
        return view('site.pages.rating', compact('branch'));
    }
}