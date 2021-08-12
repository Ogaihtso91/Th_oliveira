<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Institution;
use App\Keyword;
use App\Ods;
use App\Services\Admin\SocialTecnologies\SocialTecnologyService;
use App\SocialTecnology;
use App\SocialTecnologyUser;
use App\Theme;
use App\User;
use Illuminate\Support\Collection;
use Storage;
use PDF;

class SocialTecnologiesController extends Controller
{
    protected $socialTecnologyService;

    public function __construct()
    {
        $this->middleware(['role_or_permission:Super Admin|tecnologia-social.visualizacao'], ['only' => ['index']]);
        $this->middleware(['role_or_permission:Super Admin|tecnologia-social.cadastro|tecnologia-social.edicao'], ['only' => ['register','add_edit','save_responsibles']]);
        $this->middleware(['role_or_permission:Super Admin|tecnologia-social.exclusao'], ['only' => ['deletar']]);

        $this->socialTecnologyService = new SocialTecnologyService();
    }

    public function index()
    {
    	$registros = SocialTecnology::all();
        return view('admin.social-tecnology.index', compact('registros'));
    }

    //mostra a tecnologia social tarefa 4430
     public function show(Int $id = null)
    {
        if (!empty($id)) {
            $socialtecnology = SocialTecnology::find($id);

            if (empty($socialtecnology->id)) return redirect()->route('admin.socialtecnology.index')->with('error', trans('adminlte.socialtecnology.messages.not-found'));
        }

        // Busca os usuários da instituição
        $users_institutions = User::whereNotNull('institution_id');

        if (!empty($socialtecnology)) {
            $users_institutions->whereDoesntHave('socialtecnologies', function ($q) use ($socialtecnology) {
                $q->where('id', $socialtecnology->id);
            });
        }

        $socialtecnologyImages = $this->socialTecnologyService->getSocialTecnologyBase64Images($socialtecnology);

        $users_institutions = $users_institutions->orderBy('name')->get();
        return view('admin.social-tecnology.show', compact('socialtecnology', 'users_institutions', 'socialtecnologyImages'));
    }

    public function add_edit(Int $id = null)
    {
        if (!empty($id)) {
            $socialtecnology = SocialTecnology::find($id);

            if (empty($socialtecnology->id)) return redirect()->route('admin.socialtecnology.index')->with('error', trans('adminlte.socialtecnology.messages.not-found'));
        }

        // Busca as ods
        $ods = Ods::all();

        // Busca os temas
        $themes = Theme::orderBy('name')->get();

        // Busca as palavras-chave
        $keywords = Keyword::orderBy('name')->get();

        // Busca as instituições
        $institutions = Institution::orderBy('institution_name','asc')->get();

        // Busca os usuários da instituição
        $users_institutions = User::whereNotNull('institution_id');

        if (!empty($socialtecnology)) {
            $users_institutions->whereDoesntHave('socialtecnologies', function ($q) use ($socialtecnology) {
                $q->where('id', $socialtecnology->id);
            });
        }

        $users_institutions = $users_institutions->orderBy('name')->get();

    	return view('admin.social-tecnology.add_edit', compact('socialtecnology', 'themes', 'ods', 'institutions', 'keywords', 'users_institutions'));
    }

    public function responsibles(Int $id = null)
    {
        // Busca a tecnologia social
        $socialtecnology = SocialTecnology::find($id);

        // Se não encontrou a tecnologia social, volta para a index
        if (empty($socialtecnology->id)) return redirect()->route('admin.socialtecnology.index')->with('error', trans('adminlte.socialtecnology.messages.not-found'));

        // Busca os usuários da instituição
        $users_institutions = User::where('institution_id', $socialtecnology->institution_id);

        if (!empty($socialtecnology)) {
            $users_institutions->whereDoesntHave('socialtecnologies', function ($q) use ($socialtecnology) {
                $q->where('id', $socialtecnology->id);
            });
        }

        $users_institutions = $users_institutions->orderBy('name')->get();

        return view('admin.social-tecnology.responsibles', compact('socialtecnology', 'users_institutions'));
    }

    public function save_responsibles(Request $request)
    {
        // Busca a tecnologia social
        $socialtecnology_obj = SocialTecnology::find($request->input('id'));

        // Se não encontrou a tecnologia social, volta para a index
        if (empty($socialtecnology_obj->id)) return redirect()->route('admin.socialtecnology.index')->with('error', trans('adminlte.socialtecnology.messages.not-found'));

        // Verifica se há algum responsável para excluir
        if(!empty($request->input('remove_users'))) {
            $arr_rm_users = explode(',', $request->input('remove_users'));
            foreach ($arr_rm_users as $user_rm_item) {
                if(!empty($user_rm_item))
                    SocialTecnologyUser::where('socialtecnology_id', $socialtecnology_obj->id)
                        ->where('user_id', $user_rm_item)->delete();
            }
        }

        // Adiciona se há novos usuários
        if(!empty($request->input('users'))) {

            // Salva os usuários adicionados no banco de dados
            foreach ($request->input('users') as $user_item) {

                if(!empty($user_item)) {

                    $user_obj = User::find($user_item);

                    if (empty($user_obj->id)) {
                        return redirect()->route('admin.socialtecnology.responsibles', ['id' => $socialtecnology_obj->id])
                            ->with('message', trans('front.errors.cod-st0001'));
                    }

                    if (empty($user_obj->institution_id) || $user_obj->institution_id != $socialtecnology_obj->institution_id) {
                        return redirect()->route('admin.socialtecnology.responsibles', ['id' => $socialtecnology_obj->id])
                            ->with('message', trans('front.errors.cod-st0002', [ 'username' => $user_obj->name ]));
                    }

                    SocialTecnologyUser::create([
                        'socialtecnology_id' => $socialtecnology_obj->id,
                        'user_id' => $user_obj->id
                    ]);
                }
            }
        }

        return redirect()->route('admin.socialtecnology.index')->with('message', trans('adminlte.socialtecnology.messages.responsibles-success'));
    }

    public function delete($id)
    {
        //$registro = SocialTecnology::find($id);
        SocialTecnology::find($id)->delete();
        //Storage::delete('socialtecnologies/'.$registro->image);
        return redirect('admin/tecnologia-social')->with('message', trans('adminlte.socialtecnology.messages.deleted-success'));
    }

}
