<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use App\Reuniao;
use App\Topico;
use App\Documento;
use App\Download;
use App\Event;
use DB;
use Carbon\Carbon;
use Auth;
use Validator;
use App\User;
use App\Enums\Permissoes;

class ReuniaoController extends Controller
{
    public function __construct() {
        $this->middleware(['auth']);
        $this->middleware(['permission:'.Permissoes::AdministrarPapeisPermissoesUsuarios['id'].'|'.Permissoes::EditarAdministrarUsuarios['id']]);
    }

    private $paginate = 10;

    private $aCategoriaTopicos = array(
        "Para Deliberação" => array(
            "ENUM" => 1, # IPC - como se fosse ID, cuidado ao alterar
            "nome" => "Para Deliberação",
            "icon" => "fa-handshake"
        ),
        "Informes" => array(
            "ENUM" => 2, # IPC - como se fosse ID, cuidado ao alterar
            "nome" => "Informes",
            "icon" => "fa-comment-alt"
        ),
        "Para Conhecimento" => array(
            "ENUM" => 3, # IPC - como se fosse ID, cuidado ao alterar
            "nome" => "Para Conhecimento",
            "icon" => "fa-lightbulb"
        ),
    );

    public function index()
    {
        $registros = reuniao::orderBy('data', 'desc')->paginate($this->paginate);
    	//dd($registros);
    	return view("admin.reuniao.index", compact("registros"));
    }

    public function adicionar()
    {
        $aCategoriaTopico = $this->aCategoriaTopicos;

        foreach ($aCategoriaTopico as $categoria => $diff) {
            $aTopicosOut[$categoria]["topicos"][] = null;
        }

    	return view("admin.reuniao.adicionar", compact('aCategoriaTopico', 'aTopicosOut'));
    }

    public function salvar(Request $req)
    {
        //dd($req->all());
        $messages = [
            'finalidade.required' => 'Campo "Nome da reunião" em branco.',
            'data.required' => 'Campo "Data da reunião" em branco.',

        ];

        $this->validate($req, [
            'finalidade'=>'required|max:120',
            'data' => 'required',
        ], $messages);

        $dados = $req->all();

        (@$dados['arquivar']) ? $dados['arquivar'] = 'sim' : $dados['arquivar'] = 'nao';

        (@$dados['votacao_eletronica']) ? $dados['votacao_eletronica'] = 1 : $dados['votacao_eletronica'] = 0;

        $event = reuniao::create($dados);

        event::create([
                'titulo' => $dados['grupo'],
                'data_inicial' => $event['data'],
                'data_final' => $event['data'],
                'reuniao_id' => $event['id']
            ]);

        if(!empty($dados['participantes'])){

            $event->users()->sync($dados['participantes']);
        }

        if(!empty($dados['topicos'])){

            $topicos = $dados['topicos'];

            foreach ($topicos as $key => $topico) {
                Topico::create([
                    'titulo' => $topico['titulo'],
                    'categoria' => $topico['categoria'],
                    'conselho_id' => $event['id'],
                    'ordenacao' => ($key + 1),
                ]);
            }

        }

	   	return redirect()->route('admin.reuniao')->with('message', 'Reunião adicionada com sucesso.');
    }


    public function editar($id)
	{
        $registro = reuniao::find($id);
        $aCategoriaTopico = $this->aCategoriaTopicos;

        foreach ($aCategoriaTopico as $categoria => $diff) {
            $aTopicosOut[$categoria]["topicos"][] = null;
        }

		return view('admin.reuniao.editar', compact('registro', 'aCategoriaTopico', 'aTopicosOut'));
    }

    public function atualizar(Request $req, $id)
    {
		$messages = [
            'finalidade.required' => 'Campo "Nome da reunião" em branco.',
            'data.required' => 'Campo "Data da reunião" em branco.',
        ];

        $this->validate($req, [
            'finalidade'=>'required|max:120',
            'data' => 'required',

        ], $messages);

        $dados = $req->all();

        (@$dados['arquivar']) ? $dados['arquivar'] = 'sim' : $dados['arquivar'] = 'nao';

        (@$dados['votacao_eletronica']) ? $dados['votacao_eletronica'] = 1 : $dados['votacao_eletronica'] = 0;

        $reuniao = reuniao::find($id);

        $reuniao->update($dados);

        $event = reuniao::find($id)->event;
        $event->data_inicial = $dados['data'];
        $event->data_final = $dados['data'];
        $event->save();

        if(!empty($dados['participantes'])){
            $reuniao->users()->sync($dados['participantes']);
        }

		return redirect()->route('admin.reuniao')->with('message', 'Reunião atualizada com sucesso.');
	}

    public function deletar($id)
    {
       	reuniao::find($id)->event()->delete();
        reuniao::find($id)->delete();
       	return redirect()->route('admin.reuniao')->with('message', 'Reunião excluída com sucesso.');
    }

    public function logDocumentos($id)
    {
        $reuniao = reuniao::find($id)->finalidade;
        $registros = download::where('id', $id)->orderBy('created_at', 'desc')->paginate($this->paginate);
        return view('admin.reuniao.log-documentos', compact('registros', 'reuniao'));

    }

    public function visualizarPauta($id)
    {
        $reuniao = reuniao::find($id);
        $url_current = str_contains(url()->previous(),'reuniao/pautas-anteriores');
    	//$topicos = conselho::find($id)->topico;
    	$topicos = Topico::where('id',($id))->orderBy('ordenacao')->get();
    	return view("admin.reuniao.visualizar-pauta", compact("reuniao", "topicos", "url_current"))->with('documentos', Documento::all());
    }

    public function iniciar(Request $req, $id)
    {
        $dados['hora_inicio'] = date('Y-m-d H:i');
        $teste = reuniao::find($id)->update($dados);
		return redirect()->route('admin.reuniao.editar', $id)->with('message', 'Reunião iniciada com sucesso.');
    }

    public function parar(Request $req, $id)
    {

        $dados['hora_fim'] = date('Y-m-d H:i');
        $teste = reuniao::find($id)->update($dados);
		return redirect()->route('admin.reuniao.editar', $id)->with('message', 'Reunião parada com sucesso.');
	}

    public function buscar_usuario_jquery(Request $request)
    {
        $tipoParticipante = $request->tipo_participante;
        $usuariosSelecionados = json_decode($request->usuarios_selecionados);
        $colegiado = $request->colegiado;

        $usuario = User::query()->whereKeyNot($usuariosSelecionados);

        switch ($tipoParticipante) {
            case '2':
                $usuario->whereHas('humanograma');
                break;
            case '3':
                $usuario->whereHas('mandato', function($query) use ($colegiado) {
                   $query->where('colegiado', $colegiado);
                });
                break;
        }

        return response()->json($usuario->get());
    }

}
