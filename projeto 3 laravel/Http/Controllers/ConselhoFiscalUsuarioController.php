<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reuniao;
use App\Topico;
use App\Documento;
use App\Download;
use DB;
use Carbon\Carbon;
use URL;
use Response;
use Auth;
use App\Enums\Permissoes;


class ConselhoFiscalUsuarioController extends Controller
{

    public function __construct() {
        $this->middleware(['auth']);
        $this->middleware(['permission:'.Permissoes::LerFiscal['id'].'|'.Permissoes::EditarAdministrarUsuarios['id']]);
    }

    private $paginate = 10;

    public function index()
    {

    $registros = Reuniao::where([['grupo', 'Conselho Fiscal'], ['arquivar', 'nao']])->orderBy('data', 'desc')->first();
        if(isset($registros))
            {
                $id = $registros->id;
                $reuniao = Reuniao::find($id);
                $topicos = Topico::where('conselho_id',($id))->orderBy('ordenacao')->get();
                $documentos = Documento::orderBy('ordenacao')->get();
            }

        return view("users.conselho-fiscal.index", compact("reuniao", "topicos", "documentos"));
    }

    public function pautasAnteriores()
    {
    	$registros = Reuniao::where([
            ['grupo','Conselho fiscal'],
            ['arquivar', 'nao'],
        ])
        ->orderBy('data', 'desc')
        ->paginate($this->paginate);
    	return view("users.conselho-fiscal.pautas-anteriores", compact("registros"));
    }

    public function visualizarPauta($id)
    {

        $reuniao = Reuniao::find($id);

        $url_current = str_contains(url()->previous(),'conselho-curador/pautas-anteriores');

    	//$topicos = conselho::find($id)->topico;
    	$topicos = Topico::where('conselho_id',($id))->orderBy('ordenacao')->get();

    	return view("users.conselho-fiscal.visualizar-pauta", compact("reuniao", "topicos", "url_current"))->with('documentos', Documento::all());
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
                ['grupo', 'Conselho fiscal'],
                ['arquivar', 'nao'],
            ])
            ->get();

        return view("users.conselho-fiscal.pesquisar", compact("registros"));

    }


    public function download($id)
    {

        $arquivo = Documento::find($id);
        $topico = $arquivo->topico;
        $topico_id = $topico->conselho_id;
        $conselho = Reuniao::find($topico_id);
        $caminho = $arquivo->documentos;
        $dados['topico'] = $topico->titulo;
        $dados['nome'] = Auth::user()->name;
        $dados['login'] = Auth::user()->email;
        $dados['arquivo'] = $arquivo->nomedocumento;
        $dados['conselho_id'] = $conselho->id;

        Download::create($dados);
        return response()->download($caminho);
    }
}

