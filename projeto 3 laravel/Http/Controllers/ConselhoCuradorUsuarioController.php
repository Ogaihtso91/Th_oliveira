<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reuniao;
use App\Topico;
use App\Documento;
use App\Download;
use App\User;
use DB;
use Carbon\Carbon;
use URL;
use Response;
use Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Enums\Permissoes;



class ConselhoCuradorUsuarioController extends Controller
{

    public function __construct() {
        $this->middleware(['auth']);
        $this->middleware(['permission:'.Permissoes::LerCurador['id'].'|'.Permissoes::EditarAdministrarUsuarios['id']]);
    }

    private $paginate = 10;

    public function index()
    {

    $registros = reuniao::where([['grupo', 'Conselho Curador'], ['arquivar', 'nao']])->orderBy('data', 'desc')->first();
        if(isset($registros) )
            {
                $id = $registros->id;
                $reuniao = reuniao::find($id);
                $topicos = Topico::where('conselho_id',($id))->orderBy('ordenacao')->get();
                $documentos = Documento::orderBy('ordenacao')->get();
            }

        return view("users.conselho-curador.index", compact("reuniao", "topicos", "documentos"));
    }

    public function pautasAnteriores()
    {
    	$registros = reuniao::where([
            ['grupo','Conselho Curador'],
            ['arquivar', 'nao'],
        ])
        ->orderBy('data', 'desc')
        ->paginate($this->paginate);
    	return view("users.conselho-curador.pautas-anteriores", compact("registros"));
    }

    public function visualizarPauta($id)
    {

        $reuniao = reuniao::find($id);

        $url_current = str_contains(url()->previous(),'conselho-curador/pautas-anteriores');

    	//$topicos = conselho::find($id)->topico;
    	$topicos = Topico::where('conselho_id',($id))->orderBy('ordenacao')->get();

    	return view("users.conselho-curador.visualizar-pauta", compact("reuniao", "topicos", "url_current"))->with('documentos', Documento::all());
    }

    public function pesquisar(Request $req)
    {

        $texto = $req->texto;

        /*$teste = session()->put('textosalvo', '$texto');

        $data = Carbon::createFromFormat('d/m/Y', $texto)->toDateString();

        $registros = conselho::where('data', 'like', "%{$data}%")->get();*/

         $registros = DB::table('topicos')
            ->join('reunioes', 'reunioes.id', '=', 'conselho_id')
            ->where([
                ['titulo', 'like',"%{$texto}%"],
                ['grupo', 'Conselho Curador'],
                ['arquivar', 'nao'],
            ])
            ->get();

        return view("users.conselho-curador.pesquisar", compact("registros"));

    }


    public function download($id)
    {

        $arquivo = Documento::find($id);
        $topico = $arquivo->topico;
        $topico_id = $topico->conselho_id;
        $conselho = reuniao::find($topico_id);
        $caminho = $arquivo->documentos;
        $dados['topico'] = $topico->titulo;
        $dados['nome'] = Auth::user()->name;
        $dados['login'] = Auth::user()->email;
        $dados['arquivo'] = $arquivo->nomedocumento;
        $dados['conselho_id'] = $conselho->id;

        download::create($dados);
        return response()->download($caminho);
    }



}

