<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
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
use App\Enums\Permissoes;

class ComiteInvestimentosController extends Controller
{

	public function __construct() {
        $this->middleware(['auth']);
        $this->middleware(['permission:'.Permissoes::AdministrarPapeisPermissoesUsuarios['id'].'|'.Permissoes::EditarAdministrarUsuarios['id']]);
    }

	private $paginate = 10;

    public function pesquisar(Request $req)
    {


        $texto = $req->texto;

        /*$data = Carbon::createFromFormat('d/m/Y', $texto)->toDateString();
        $registros = conselho::where('data', 'like', "%{$data}%")->get(); */
        //$registros = topico::where('titulo', 'like', "%{$texto}%")->get();

        $registros = DB::table('topicos')
            ->select('topicos.id','finalidade', 'titulo', 'topicos.created_at', 'conselho_id')
            ->join('reunioes', 'reunioes.id', '=', 'conselho_id')
            ->where([
                ['titulo', 'like',"%{$texto}%"],
                ['grupo', 'Comite Investimentos'],
            ])
            ->get();

        return view("admin.comite-investimentos.pesquisar", compact("registros"));

    }

    public function index()
    {

        $registros = reuniao::where('grupo','Comite Investimentos')->orderBy('data', 'desc')->paginate($this->paginate);
    	//$registros = conselho::orderBy('data','desc')->paginate(5);
    	return view("admin.comite-investimentos.index", compact("registros"));
    }


    public function adicionar()
    {
    	return view("admin.comite-investimentos.adicionar");
    }


    public function salvar(Request $req)
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
        $data = $dados['data'];
        $dados['data'] = Carbon::createFromFormat('d/m/Y', $data)->toDateString();
        $event = reuniao::create($dados);
        event::create(['titulo' => 'Comitê de Investimentos', 'data_inicial' => $event['data'], 'data_final' => $event['data'],'reuniao_id' => $event['id']]);
	   	return redirect()->route('admin.comite-investimentos')->with('message', 'Reunião adicionada com sucesso.');
    }

    public function editar($id)
	{
		$registro = reuniao::find($id);
		return view('admin.comite-investimentos.editar', compact('registro'));
	}

     public function deletar($id)
    {
       	reuniao::find($id)->event()->delete();
        reuniao::find($id)->delete();
       	return redirect()->route('admin.comite-investimentos')->with('message', 'Reunião excluída com sucesso.');
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
        $data = $dados['data'];
        $dados['data'] = Carbon::createFromFormat('d/m/Y', $data)->toDateString();
		reuniao::find($id)->update($dados);
        $event = reuniao::find($id)->event;
        $event->data_inicial = $dados['data'];
        $event->data_final = $dados['data'];
        $event->save();
		return redirect()->route('admin.comite-investimentos')->with('message', 'Reunião atualizada com sucesso.')->withInput();
	}



    public function visualizarTopicos($id)
    {
    	$reuniao = reuniao::find($id);

    	//$topicos = conselho::find($id)->topico;
    	$topicos = DB::table('topicos')->where('conselho_id',($id))->orderBy('ordenacao')->get();

    	return view("admin.comite-investimentos.visualizartopicos", compact("reuniao", "topicos"));
    }


    public function adicionarTopicos($id)
	{

		$registro = reuniao::find($id);
		return view("admin.comite-investimentos.adicionar-topicos", compact("registro"));
	}

    public function salvarTopicos(Request $request){

     	$messages = [
            'ordenacao.required' => 'Campo "Ordenação" em branco.',
            'titulo.required' => 'Campo "Nome do tópico" em branco'

        ];

        $validation = Validator::make($request->all(), [

            'ordenacao'=>'required|max:120',
            'titulo' => 'required'

        ], $messages);

        $validation->after(function ($validator) {

            // Pega os valores passados pelo POST
            $input = $validator->attributes();

                // Cria objeto  para verificar a qtde de ordenação existente
                $ordenacao = Topico::where([['ordenacao', $input['ordenacao']], ['conselho_id', $input['conselho_id']]]);

                // Verifica se já existe a ordenação
                if ($ordenacao->count() >= 1) {

                    // Adiciona mensagem no validador
                    $validator->errors()->add('ordenacao', 'Ordenação já existente.');
                }
        });

        $validation->validate();

        $dados = $request->all();
        $id_conselho = $dados['conselho_id'];

     	topico::create($dados);
     	return redirect()->route('admin.comite-investimentos.visualizartopicos',$id_conselho)->with('message', 'Tópico adicionado com sucesso.');

    }


    public function adicionarDocumentos($id)
	{
		$registro = topico::find($id);
		return view("admin.comite-investimentos.adicionar-documentos", compact("registro"));
	}

	public function editarDocumentos($id)
    {

        $registro = documento::find($id);
        $topico = $registro->topico['id'];

        return view('admin.comite-investimentos.editar-documentos', compact('registro','topico'));
    }

    public function atualizarDocumentos(Request $request, $id)
    {

        $messages = [
            'nomedocumento.required' => 'Campo "Nome do documento" em branco.',
            'documentos.required' => 'Documento não anexado.',
            'documentos.max' => 'Tamanho máximo permitido do documento é 5Mb.',
            'documentos.mimetypes' => 'Extensão não permitida.',
            'ordenacao.required' => 'Campo "Ordenação" em branco.',

        ];

        $validation = Validator::make($request->all(), [
            'ordenacao'=>'required|max:120',
            'nomedocumento'=>'required|max:120',
            'documentos' => 'mimetypes:application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.openxmlformats-officedocument.presentationml.slideshow,application/msword,application/vnd.ms-excel,application/vnd.ms-powerpoint|max:125000',
        ], $messages);

        $validation->sometimes('documentos', 'required', function ($input) use ($request) {
            return empty($request->id);
        });

        $validation->after(function ($validator) use ($request) {

            // Pega os valores passados pelo POST
            $input = $validator->attributes();


                // Cria objeto  para verificar a qtde de ordenação existente
                $ordenacao = Documento::where([['ordenacao', $input['ordenacao']], ['topico_id', $input['topico_id']]]);

                if(!empty($request->id)) $ordenacao->where('id', '!=',$request->id);

                // Verifica se já existe a ordenação
                if ($ordenacao->count() >= 1) {

                    // Adiciona mensagem no validador
                    $validator->errors()->add('ordenacao', 'Ordenação já existente.');
                }
        });

        $validation->validate();

        $dados = $request->all();

        if ($request->hasfile('documentos')) {

                $documento = $request->file('documentos');
                $nomearquivo = $documento->getClientOriginalName();
                $diretorio = "arquivo/comite-investimentos";
                $documento->move($diretorio,$nomearquivo);
                $dados['documentos'] = $diretorio . "/" . $nomearquivo;

        }

        documento::find($id)->update($dados);
        $id_topico = $dados['topico_id'];
        return redirect()->route('admin.comite-investimentos.visualizar-documentos', $id_topico)->with('message', 'Documento atualizado com sucesso.');
    }

    public function salvarDocumentos(Request $request){

    	$messages = [
            'nomedocumento.required' => 'Campo "Nome do documento" em branco.',
            'documentos.required' => 'Documento não anexado.',
            'documentos.max' => 'Tamanho máximo permitido do documento é 120Mb.',
            'documentos.mimetypes' => 'Extensão não permitida.',
            'ordenacao.required' => 'Campo "Ordenação" em branco.',

        ];

       $validation = Validator::make($request->all(), [
            'ordenacao'=>'required|max:120',
            'nomedocumento'=>'required|max:120',
            'documentos' => 'required|mimetypes:application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.openxmlformats-officedocument.presentationml.slideshow,application/msword,application/vnd.ms-excel,application/vnd.ms-powerpoint|max:125000',
        ], $messages);

        $validation->after(function ($validator) {

            // Pega os valores passados pelo POST
            $input = $validator->attributes();
            //dd($input);

                // Cria objeto  para verificar a qtde de ordenação existente
                $ordenacao = Documento::where([['ordenacao', $input['ordenacao']], ['topico_id', $input['topico_id']]]);

                // Verifica se já existe a ordenação
                if ($ordenacao->count() >= 1) {

                    // Adiciona mensagem no validador
                    $validator->errors()->add('ordenacao', 'Ordenação já existente.');
                }
        });

        $validation->validate();

        $dados = $request->all();

        if ($request->hasfile('documentos')) {

                $documento = $request->file('documentos');
                $nomearquivo = $documento->getClientOriginalName();
                $diretorio = "arquivo/comite-investimentos";
                $documento->move($diretorio,$nomearquivo);
                $dados['documentos'] = $diretorio . "/" . $nomearquivo;
                documento::create($dados);

        }

        $id_topico = $dados['topico_id'];
     	return redirect()->route('admin.comite-investimentos.visualizar-documentos', $id_topico)->with('message', 'Documento adicionado com sucesso.');
    }


    public function visualizarDocumentos($id)
    {
    	$topico = topico::find($id);
        $documentos = DB::table('documentos')->where('topico_id', $id)->orderBy('ordenacao', 'asc')->get();
        $url_previous = str_contains(url()->previous(),'admin/comite-investimentos/visualizar-topicos');
    	//$documentos = $topico->documento;
    	return view("admin.comite-investimentos.visualizar-documentos", compact("topico", "documentos", "url_previous"));

	}

    public function deletarDocumentos($id)
    {

       	$id_topico = documento::find($id)->topico['id'];
    	$documento = documento::find($id);
        $caminho = $documento['documentos'];
        unlink($caminho);
        documento::find($id)->delete();
       	return redirect()->route('admin.comite-investimentos.visualizar-documentos', $id_topico)->with('message', 'Documento excluído com sucesso.');
    }


    public function logDocumentos($id)
    {

        $reuniao = reuniao::find($id)->finalidade;
        $registros = download::where('conselho_id', $id)->orderBy('created_at', 'desc')->paginate($this->paginate);

        return view('admin.comite-investimentos.log-documentos', compact('registros', 'reuniao'));

    }

    public function editarTopicos($id)
	{

		$registro = topico::find($id);
		return view('admin.comite-investimentos.editar-topicos', compact('registro'));
	}


	public function atualizarTopicos(Request $request, $id)
	{
		$messages = [
            'ordenacao.required' => 'Campo "Ordenação" em branco.',
            'titulo.required' => 'Campo "Nome do tópico" em branco'

        ];

        $validation = Validator::make($request->all(), [

            'ordenacao'=>'required|max:120',
            'titulo' => 'required'

        ], $messages);

        $validation->after(function ($validator) use ($request) {

            // Pega os valores passados pelo POST
            $input = $validator->attributes();

                $conselho_id = Topico::find($input['id'])->conselho_id;

                // Cria objeto  para verificar a qtde de ordenação existente
                $ordenacao = Topico::where([['ordenacao', $input['ordenacao']], ['conselho_id', $conselho_id]]);

                if(!empty($request->id)) $ordenacao->where('id', '!=',$request->id);
                // Verifica se já existe a ordenação
                if ($ordenacao->count() >= 1)
                {
                    // Adiciona mensagem no validador
                    $validator->errors()->add('ordenacao', 'Ordenação já existente.');
                }
        });

        $validation->validate();

        $id_conselho = topico::find($id)->conselho_id;

		$dados = $request->all();
		topico::find($id)->update($dados);
		return redirect()->route('admin.comite-investimentos.visualizartopicos', $id_conselho)->with('message', 'Tópico atualizado com sucesso.');    ;
	}

	 public function deletarTopicos($id)
    {

       	$id_conselho = topico::find($id);
        $id_conselho = $id_conselho->conselho_id;
       	topico::find($id)->delete();
       	return redirect()->route('admin.comite-investimentos.visualizartopicos', $id_conselho)->with('message', 'Tópico excluído com sucesso.');
    }
}


