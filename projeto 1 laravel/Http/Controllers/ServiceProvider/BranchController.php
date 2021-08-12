<?php
/**
 * Created by PhpStorm.
 * User: thiag
 * Date: 19/08/2018
 * Time: 07:42
 */

namespace App\Http\Controllers\ServiceProvider;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Branch;
use App\Service;
use App\Contact;
use App\BusinessHour;
use App\Repositories\BranchRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\LocationRepository;
use App\Repositories\PaymentMethodRepository;
use App\Repositories\BranchCommentRepository;

class BranchController extends ServiceProviderBaseController
{
    private $repository;
    private $serviceRepository;
    private $locationRepository;
    private $branchCommentRepository;
    private $paymentMethodRepository;

    public function __construct(
        BranchRepository $branchRepository,
        ServiceRepository $serviceRepository,
        LocationRepository $locationRepository,
        BranchCommentRepository $branchCommentRepository,
        PaymentMethodRepository $paymentMethodRepository)
    {
        parent::__construct();
        $this->repository = $branchRepository;
        $this->serviceRepository = $serviceRepository;
        $this->locationRepository = $locationRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->branchCommentRepository = $branchCommentRepository;
    }

    public function list()
    {
        $branches = $this->serviceProvider->branches()->get();
        return view('serviceProvider.branch.list', compact('branches'));
    }

    public function addEdit(Request $r, $slug = null)
    {
        if($r->isMethod('post')) {
            try {
                DB::beginTransaction();
                    $this->repository->save($r, $this->user);
                DB::commit();
                return redirect()->route('serviceProvider.branches.list')->with('success', !empty($r->input('id')) ? 'Filial atualizada com sucesso!' : 'Filial cadastrada com sucesso!');
            } catch(\Exception $e) {
                DB::rollBack();
                return redirect()->back()->withInput($r->all())->with('error', $e->getMessage());
            }
        }
        if(!is_null($slug)){
            $tmp = explode('-', $slug); $id = end($tmp);
            try {
                $branch     = $this->repository->first($id, $this->user);
                $branch_business_hours = $branch->businessHour ? $branch->businessHour : new BusinessHour();
                $location_ids = [
                    'state_id'      => $branch->address->district->city->state_id,
                    'city_id'       => $branch->address->district->city_id,
                    'district_id'   => $branch->address->district_id,
                    'address_id'    => $branch->address_id
                ];
                $states     = $this->locationRepository->getStates();
                $cities     = $this->locationRepository->getCities( $location_ids['state_id'] );
                $districts  = $this->locationRepository->getDistricts( $location_ids['city_id'] );
                $addresses  = $this->locationRepository->getAddresses( $location_ids['address_id'] );
            } catch(\Exception $e) {
                return redirect()->route('serviceProvider.branches.list')->with('error', $e->getMessage());
            }
        } else {
            $branch     = new Branch;
            $branch_business_hours = $branch->businessHour ? $branch->businessHour : new BusinessHour();
            $states     = $this->locationRepository->getStates();
            $cities     = [];
            $districts  = [];
            $addresses  = [];
            $location_ids = ['state_id' => '', 'city_id' => '', 'district_id' => '', 'address_id' => ''];
        }

        $contacts = $branch->contacts;
      
        $services       = $this->serviceRepository->getList();
        $paymentMethods = $this->paymentMethodRepository->getList();
        $typeContacts   = Contact::typeContacts();
        return view('serviceProvider.branch.addEdit', compact('services', 'typeContacts', 'contacts', 'paymentMethods', 'branch','states','cities','districts','addresses','location_ids', 'branch_business_hours'));
    }

    public function gallery(Request $r, $slug)
    {
        if(is_null($slug))
            return redirect()->back()->withInput($r->all())->with('error', 'Filial não encontrada');
        $tmp = explode('-', $slug); $id = end($tmp);
        try {
            $branch     = $this->repository->first($id, $this->user);
        } catch(\Exception $e) {
            return redirect()->back()->withInput($r->all())->with('error', 'Filial não encontrada');
        }
        return view('serviceProvider.branch.gallery', compact('branch', 'gallery'));

    }

    public function galleryUpload(Request $r, $slug)
    {
        $action = $r->get('action');
        $tmp = explode('-', $slug); $id = end($tmp);
        try {
            $branch     = $this->repository->first($id, $this->user);
            if($action == 'create') {
                return $this->repository->uploadGallery($r->file('qqfile'), $r->get('branch_id'));
            } elseif($action == 'delete') {
                $result = $this->repository->removeGallery($branch, $r->get('id'));
                return response()->json(['success' => true], 200);
            }
        } catch(\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);   
        }
    }

    public function rating(Request $r, $slug)
    {
        $tmp = explode('-', $slug); $id = end($tmp);
        try {
            $page       = $r->get('page');
            $branch     = $this->repository->first($id, $this->user);
            $comments   = $this->branchCommentRepository->listPendingFirst($branch->rating())->paginate(10);
        } catch(\Exception $e) {
            return redirect()->back()->withInput($r->all())->with('error', $e->getMessage());
        }
        return view('serviceProvider.branch.rating', compact('branch', 'comments'));
    }

    public function ratingAction(Request $r, $slug, $rate_id, $action)
    {
        $tmp = explode('-', $slug); $id = end($tmp);
        try {
            $branch     = $this->repository->first($id, $this->user);
            $this->branchCommentRepository->allowReject($branch, $rate_id, $action);
            return redirect()->back()->withInput($r->all())->with('success', 'Avaliação ' . ($action == 'aprovar' ? 'aprovada' : 'reprovada') . ' com sucesso');
        } catch(\Exception $e) {
            return redirect()->back()->withInput($r->all())->with('error', $e->getMessage());
        }

    }

}