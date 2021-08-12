<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Filesystem\Storage;
use App\Event;
use App\Institution;
use App\Theme;
use Validator;
use Carbon\Carbon;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role_or_permission:Super Admin|agenda.visualizacao'], ['only' => ['index']]);
        $this->middleware(['role_or_permission:Super Admin|agenda.cadastro|agenda.edicao'], ['only' => ['register','add_edit']]);
        $this->middleware(['role_or_permission:Super Admin|agenda.exclusao'], ['only' => ['delete']]);
    }
    
    public function index()
    {

    	$registros = Event::all();
    	return view('admin.event.index', compact('registros'));

    }

    public function add_edit(Int $id = null)
    {
    	if (!empty($id)) $event = event::find($id);
        $institutions = Institution::orderBy('institution_name', 'asc')->get();
        $themes = Theme::orderBy('name','asc')->get();
    	return view('admin.event.add_edit', compact('event','institutions','themes'));

    }

    public function delete($id)
    {
       	$registro = Event::find($id);
       	Event::find($id)->delete();
        Storage::delete('events/'.$registro->image);
       	return redirect('admin/agenda')->with('message', 'Evento excluído com sucesso.');
    }
}
