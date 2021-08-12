<?php
namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\AlertRepository;
use App\Repositories\VaccineRepository;

class AlertsController extends SiteBaseController
{
    private $alertRepository;
    private $vaccineRepository;
    public function __construct(AlertRepository $alertRepository, VaccineRepository $vaccineRepository) 
    {
        parent::__construct();
        $this->alertRepository = $alertRepository;
        $this->vaccineRepository = $vaccineRepository;
    }
    public function alerts()
    {
        return view('site.alerts.list');
    }

    public function vaccines()
    {
        try {
            $result = $this->vaccineRepository->sendAlert();
        } catch(\Exception $e) {
            return response('Erro ao enviar alertas', 500);
        }
        return response()->json($result, 200);
    }

    public function detail(Request $r, $slug)
    {
        $id = explode('-', $slug);
        $id = end($id);
        try {
            $alert = $this->alertRepository->findById($id);

            if($r->isMethod('post')) {
                $data = $r->get('comment');
                $data['user_id'] = $this->user->id;
                $data['pet_alert_id'] = $id;
                $this->alertRepository->doComment($alert, $data);
                return redirect()->back()->with('success', 'Comentário enviado');           
            }
        } catch(\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
        return view('site.alerts.detail', compact('alert'));
    }

}