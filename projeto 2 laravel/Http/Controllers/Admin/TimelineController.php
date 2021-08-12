<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Theme;
use App\ContentManager;
use App\Event;
use App\Institution;
use App\Ods;
use App\SocialTecnology;
use App\SocialTecnologyComment;
use App\BlogComment;

class TimelineController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role_or_permission:Super Admin|linha-do-tempo.visualizacao'], ['only' => ['index','detail']]);
        $this->middleware(['role_or_permission:Super Admin|linha-do-tempo.revercao'], ['only' => ['revert']]);
        $this->middleware(['role_or_permission:Super Admin|linha-do-tempo.exclusao'], ['only' => ['deletecomment']]);
    }

    public function index(Request $request)
    {
        $filters = $request->all();

        $timeline_items = ContentManager::query_to_timeline($filters);

        $institutions = ContentManager::distinct()->whereNotNull('institution_id')->get(['institution_id']);

        return view('admin.timeline.index', compact('timeline_items', 'institutions'));
    }

    public function deletecomment($id)
    {
        // Busca o item de moderação do banco de dados
    	$timeline = ContentManager::find($id);
    	//dd($timeline);

    	// Se não existe, volta para a página anterior
    	if (empty($timeline->id)) return redirect()->back();

    	// Verifica o tipo
    	switch ($timeline->type) {

    		// Tecnologia Social
    		case ContentManager::TYPE_SOCIALTECNOLOGY_COMMENT:

    			$comment_obj = SocialTecnologyComment::find($timeline->model_id)->delete();
    			break;

    		// Blog
    		case ContentManager::TYPE_BLOG_COMMENT:

    			$comment_obj = BlogComment::find($timeline->model_id)->delete();
    			break;

    		default:

    			redirect()->back();
    			break;
    	}

    	$timeline->reverted = 1;
    	$timeline->note = "Comentário excluído por ".Auth::guard('admin')->user()->name;
    	$timeline->save();

    	// Retorna à timeline
    	return redirect()->route('admin.timeline.index')->with('message', 'Comentário excluído com sucesso');

    }

    public function detail(Int $id) {

    	// Busca o item de moderação do banco de dados
    	$timeline = ContentManager::find($id);

    	// Se não existe, volta para a página da linha do tempo
    	if (empty($timeline->id) || $timeline->reverted == 1) return redirect()->route('admin.timeline.index');

        // Busca os temas
        $themes = Theme::all();

        // Busca as ods
        $ods = Ods::all();

        // Marca como lido
        $timeline->markAsRead();

    	// Chama a view
    	return view('admin.timeline.detail', compact('timeline', 'themes', 'ods'));
    }

    public function revert(Request $request) {

    	// Busca o item de moderação do banco de dados
    	$timeline = ContentManager::find($request->input('content_id'));

    	// Se não existe ou já foi revertido, volta para a página anterior
        if (empty($timeline->id) || $timeline->reverted == 1) return redirect()->back();

    	// Busca os valores para reverter
        if (!empty($timeline->old_values)) {
    	   $data = json_decode($timeline->old_values, true);
           $data['action'] = 'revert';
        } else {
           $data['action'] = 'remove';
        }
    	$data['id'] = $timeline->model_id;
        $data['updated_date'] = $timeline->created_at->format('d/m/Y \à\s H:i\h\r\s');

    	// Verifica o tipo
    	switch ($timeline->type) {

    		// Tecnologia Social
    		case ContentManager::TYPE_SOCIALTECNOLOGY:

                if(!empty($timeline->old_values))
                    SocialTecnology::revert($data);
                else
                    SocialTecnology::find($data['id'])->delete();
    			break;

    		// Tecnologia Social
    		case ContentManager::TYPE_EVENT:

                if(!empty($timeline->old_values))
                    Event::revert($data);
                else
                    Event::find($data['id'])->delete();
                break;

    		// Tecnologia Social
    		case ContentManager::TYPE_INSTITUTION:

                if(!empty($timeline->old_values))
                    Institution::revert($data);
                else
                    Institution::find($data['id'])->delete();
                break;

    		default:

    			redirect()->back();
    			break;
    	}

        if(!empty($timeline->old_values))
            $timeline->note = "Alteração revertida por ".Auth::guard('admin')->user()->name;
        else
            $timeline->note = "Deletado por ".Auth::guard('admin')->user()->name;

        $timeline->reverted = 1;
    	$timeline->save();

    	// Retorna à timeline
    	return redirect()->route('admin.timeline.index')->with('message', 'Alteração revertida com sucesso!');
    }
}
