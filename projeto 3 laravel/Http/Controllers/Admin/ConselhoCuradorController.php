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

class ConselhoCuradorController extends Controller {

    public function __construct() {
        $this->middleware(['auth']);
        $this->middleware(['permission:'.Permissoes::AdministrarPapeisPermissoesUsuarios['id'].'|'.Permissoes::EditarAdministrarUsuarios['id']]);
    }

    private $paginate = 10;

    # categorias estaticas #7335
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

    public function pesquisar(Request $req) {
        $texto = $req->texto;
        /* $data = Carbon::createFromFormat('d/m/Y', $texto)->toDateString();
          $registros = conselho::where('data', 'like', "%{$data}%")->get(); */
        //$registros = topico::where('titulo', 'like', "%{$texto}%")->get();

        $registros = DB::table('topicos')
                ->select('topicos.id', 'finalidade', 'titulo', 'topicos.created_at', 'conselho_id')
                ->join('reunioes', 'reunioes.id', '=', 'conselho_id')
                ->where([
                    ['titulo', 'like', "%{$texto}%"],
                    ['grupo', 'Conselho Curador'],
                ])
                ->get();

        return view("admin.conselho-curador.pesquisar", compact("registros"));
    }

    public function index() {
        $registros = reuniao::where('grupo', 'Conselho Curador')->orderBy('data', 'desc')->paginate($this->paginate);
        //$registros = conselho::orderBy('data','desc')->paginate(5);
        return view("admin.conselho-curador.index", compact("registros"));
    }

    public function adicionar() {
        return view("admin.conselho-curador.adicionar");
    }

    public function salvar(Request $req) {
        $messages = [
            'finalidade.required' => 'Campo "Nome da reunião" em branco.',
            'data.required' => 'Campo "Data da reunião" em branco.',
        ];

        $this->validate($req, [
            'finalidade' => 'required|max:120',
            'data' => 'required',
                ], $messages);

        $dados = $req->all();
        $data = $dados['data'];
        $dados['data'] = Carbon::createFromFormat('d/m/Y', $data)->toDateString();
        $event = reuniao::create($dados);
        event::create(['titulo' => 'Conselho Curador', 'data_inicial' => $event['data'], 'data_final' => $event['data'],
            'reuniao_id' => $event['id']]);
        return redirect()->route('admin.conselho-curador')->with('message', 'Reunião adicionada com sucesso.');
    }

    public function editar($id) {
        $registro = reuniao::find($id);
        return view('admin.conselho-curador.editar', compact('registro'));
    }

    public function deletar($id) {
        reuniao::find($id)->event()->delete();
        reuniao::find($id)->delete();
        return redirect()->route('admin.conselho-curador')->with('message', 'Reunião excluída com sucesso.');
    }

    public function atualizar(Request $req, $id) {
        $messages = [
            'finalidade.required' => 'Campo "Nome da reunião" em branco.',
            'data.required' => 'Campo "Data da reunião" em branco.',
        ];

        $this->validate($req, [
            'finalidade' => 'required|max:120',
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
        return redirect()->route('admin.conselho-curador')->with('message', 'Reunião atualizada com sucesso.');
    }

    public function visualizarTopicos($id) {
        $reuniao = reuniao::find($id);
        $aCategoriaTopico = $this->aCategoriaTopicos;

        //$topicos = conselho::find($id)->topico; #7335
        $topicos = DB::table('topicos')->where('conselho_id', ($id))->orderBy('categoria_ordenacao', 'ASC')->orderBy('ordenacao', 'ASC')->get();
        $aTopicos = array();
        if ($topicos) {
            foreach ($topicos as $topico) {
                $aTopicos[$topico->categoria]["topicos"][] = $topico;
            }
        }
        
        # para ter todos os itens mesmo senao tiver no banco
        $aDiff = array_diff_key($aCategoriaTopico, $aTopicos);
        if ($aDiff) {
            foreach ($aDiff as $categoria => $diff) {
                $aTopicosOut[$categoria]["topicos"][] = null;
            }
        }

        # TODO
        $documentos = array("nome" => "TODO DOCUMENTO");
        if ($documentos) {
            foreach ($documentos as $documento) {
//                $aTopicos["Informes"]["documentos"][] = $documento;
            }
        }        

        return view("admin.conselho-curador.visualizartopicos", compact("reuniao", "aTopicos", "aTopicosOut", "aCategoriaTopico"));
    }

    public function adicionarTopicos($id) {
        $registro = reuniao::find($id);
        $categoria_old = isset($_GET["categoria"]) ? $_GET["categoria"] : null;
        return view("admin.conselho-curador.adicionar-topicos", compact("registro", "categoria_old"));
    }

    public function salvarTopicos(Request $request) {
        $messages = [
            'titulo.required' => 'Campo "Nome do tópico" em branco'
        ];

        $validation = Validator::make($request->all(), [
                    'titulo' => 'required'
                        ], $messages);

        $validation->after(function ($validator) {
            // Pega os valores passados pelo POST
            $input = $validator->attributes();
        });

        $validation->validate();

        $dados = $request->all();
        #7335
        $ordenacao = Topico::where([['conselho_id', $request->get('conselho_id')]]);
        $dados["ordenacao"] = $ordenacao->count() + 1;
        $id_conselho = $dados['conselho_id'];

        topico::create($dados);
        return redirect()->route('admin.conselho-curador.visualizartopicos', $id_conselho)->with('message', 'Tópico adicionado com sucesso.');
    }

    public function adicionarDocumentos($id) {
        $registro = topico::find($id);
        return view("admin.conselho-curador.adicionar-documentos", compact("registro"));
    }

    public function editarDocumentos($id) {
        $registro = documento::find($id);
        $topico = $registro->topico['id'];

        return view('admin.conselho-curador.editar-documentos', compact('registro', 'topico'));
    }

    public function atualizarDocumentos(Request $request, $id) {
        $messages = [
            'nomedocumento.required' => 'Campo "Nome do documento" em branco.',
            'documentos.required' => 'Documento não anexado.',
            'documentos.max' => 'Tamanho máximo permitido do documento é 120Mb.',
            'documentos.mimetypes' => 'Extensão não permitida.',
            'ordenacao.required' => 'Campo "Ordenação" em branco.',
        ];

        $validation = Validator::make($request->all(), [
                    'ordenacao' => 'required|max:120',
                    'nomedocumento' => 'required|max:120',
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

            if (!empty($request->id))
                $ordenacao->where('id', '!=', $request->id);

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
            $diretorio = "arquivo/conselho-curador";
            $documento->move($diretorio, $nomearquivo);
            $dados['documentos'] = $diretorio . "/" . $nomearquivo;
        }

        documento::find($id)->update($dados);
        $id_topico = $dados['topico_id'];
        return redirect()->route('admin.conselho-curador.visualizar-documentos', $id_topico)->with('message', 'Documento atualizado com sucesso.');
    }

    public function salvarDocumentos(Request $request) {
        $messages = [
            'nomedocumento.required' => 'Campo "Nome do documento" em branco.',
            'documentos.required' => 'Documento não anexado.',
            'documentos.max' => 'Tamanho máximo permitido do documento é 120Mb.',
            'documentos.mimetypes' => 'Extensão não permitida.',
            'ordenacao.required' => 'Campo "Ordenação" em branco.',
        ];

        $validation = Validator::make($request->all(), [
                    'ordenacao' => 'required|max:120',
                    'nomedocumento' => 'required|max:120',
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
            $diretorio = "arquivo/conselho-curador";
            $documento->move($diretorio, $nomearquivo);
            $dados['documentos'] = $diretorio . "/" . $nomearquivo;
            documento::create($dados);
        }

        $id_topico = $dados['topico_id'];

        return redirect()->route('admin.conselho-curador.visualizar-documentos', $id_topico)->with('message', 'Documento adicionado com sucesso.');
    }

    public function visualizarDocumentos($id) {
        $topico = topico::find($id);
        $documentos = DB::table('documentos')->where('topico_id', $id)->orderBy('ordenacao', 'asc')->get();
        $url_previous = str_contains(url()->previous(), 'admin/conselho-curador/visualizar-topicos');
        //$documentos = $topico->documento;
        return view("admin.conselho-curador.visualizar-documentos", compact("topico", "documentos", "url_previous"));
    }

    public function deletarDocumentos($id) {
        $id_topico = documento::find($id)->topico['id'];
        $documento = documento::find($id);
        $caminho = $documento['documentos'];
        unlink($caminho);
        documento::find($id)->delete();
        return redirect()->route('admin.conselho-curador.visualizar-documentos', $id_topico)->with('message', 'Documento excluído com sucesso.');
    }

    public function logDocumentos($id) {
        $reuniao = reuniao::find($id)->finalidade;
        $registros = download::where('conselho_id', $id)->orderBy('created_at', 'desc')->paginate($this->paginate);

        return view('admin.conselho-curador.log-documentos', compact('registros', 'reuniao'));
    }

    public function editarTopicos($id) {
        $registro = topico::find($id);
        return view('admin.conselho-curador.editar-topicos', compact('registro'));
    }

    public function atualizarTopicos(Request $request, $id) {
        $messages = [
            'titulo.required' => 'Campo "Nome do tópico" em branco'
        ];

        $validation = Validator::make($request->all(), [
                    'titulo' => 'required'
                        ], $messages);

        $validation->after(function ($validator) use ($request) {

            // Pega os valores passados pelo POST
            $input = $validator->attributes();

            $conselho_id = Topico::find($input['id'])->conselho_id;
        });

        $validation->validate();

        $id_conselho = topico::find($id)->conselho_id;

        $dados = $request->all();
        #7335
        $ordenacao = Topico::where([['conselho_id', $request->get('conselho_id')]]);
        $dados["ordenacao"] = $ordenacao->count() + 1;
        topico::find($id)->update($dados);
        return redirect()->route('admin.conselho-curador.visualizartopicos', $id_conselho)->with('message', 'Tópico atualizado com sucesso.');
    }

    public function deletarTopicos($id) {
        $id_conselho = topico::find($id);
        $id_conselho = $id_conselho->conselho_id;
        topico::find($id)->delete();
        return redirect()->route('admin.conselho-curador.visualizartopicos', $id_conselho)->with('message', 'Tópico excluído com sucesso.');
    }

    public function ordenarTopicos(Request $request) {
        #7335 ordenacao
        $data = $request->all();
        $ids_data = (explode("&", $data["data"]));
        $aId = array();
        if ($ids_data) {
            foreach ($ids_data as $id) {
                $aId[] = (int) str_replace("sort_topico[]=", "", $id);
            }

            # para reodenar sem zerar
//            $oTopico = topico::find($aId[0]);
//            $ordenacao = Topico::where([['conselho_id', $oTopico->conselho_id]]);
//            $ultOrdenacao = $ordenacao->count() + 1;
            $ultOrdenacao = 1;

            foreach ($aId as $i => $id) {
                $dados = array("ordenacao" => ($ultOrdenacao + $i));
                Topico::find($id)->update($dados);
            }
        }
    }

    public function ordenarCategoriaTopicos(Request $request) {
        #7335 ordenacao da categoria - campo novo no topico
        $data = $request->all();
        $ids_data = (explode("&", $data["data"]));
        $aId = array();
        if ($ids_data) {
            foreach ($ids_data as $id) {
                $aId[] = (int) str_replace("sort_categoria[]=", "", $id);
            }

            $ultOrdenacao = 1;

            # aqui é para associar pelo enum a categoria
            $aCategoriaTopico = $this->aCategoriaTopicos;
            $aCategoriaTopicoEnum = array();
            foreach ($aCategoriaTopico as $categoria => $def) {
                $aCategoriaTopicoEnum[$def["ENUM"]] = $def["nome"];
            }
            $id_conselho = ($data["extra"]);

            foreach ($aId as $i => $id) {
                # acha os topicos pela categoria e depois atualiza
                $CategoriaTopico = $aCategoriaTopicoEnum[$id];
                $aTopicosUpdate = Topico::where('conselho_id', $id_conselho)->where('categoria', $CategoriaTopico)->get();
                if ($aTopicosUpdate)
                    foreach ($aTopicosUpdate as $topico) {
                        $dados = array("categoria_ordenacao" => ($ultOrdenacao + $i));
                        $topico->update($dados);
                    }
            }
        }
    }

    public function deletarCategoriaTopicos($id_conselho, $categoria) {
        $aTopicosDelete = Topico::where('conselho_id', $id_conselho)->where('categoria', $categoria)->get();
        if ($aTopicosDelete)
            foreach ($aTopicosDelete as $topico) {
                $topico->delete();
            }

        return redirect()->route('admin.conselho-curador.visualizartopicos', $id_conselho)->with('message', 'Categoria excluída com sucesso.');
    }

}
