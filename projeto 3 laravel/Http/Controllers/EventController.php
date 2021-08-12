<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use MaddHatter\LaravelFullcalendar\Facades\Calendar;
use Carbon\Carbon;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }


    public function index()
    {
        $events = Event::all('id', 'titulo as title', 'data_inicial as start', 'data_final as end', 'local', 'hora_inicio');

        foreach($events as $key => $value){

            $events[$key]->start_correto =  (new \DateTime($value['start']))->format('d/m/Y');
            $events[$key]->end_correto =  (new \DateTime($value['end']))->format('d/m/Y');

            $events[$key]->start = (new \DateTime($value['start']))->format('Y-m-d');
            $events[$key]->end = (new \DateTime($value['end'] . ' +1 day'))->format('Y-m-d');
        }

        return view('users.agenda.index', compact('events'));
    }

    public function admin(Request $request)
    {
        $dados = $request->all();
    }
}
