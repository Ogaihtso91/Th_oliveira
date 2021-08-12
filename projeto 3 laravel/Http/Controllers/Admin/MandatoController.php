<?php

namespace App\Http\Controllers\Admin;

use App\Mandato;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\CargosFbb;
use DB;

class MandatoController extends Controller {

    private $paginate = 10;
    private $aColegiado = array(
        "1" => array(
            "ENUM" => 1, # IPC - como se fosse ID, cuidado ao alterar
            "nome" => "Conselho Curador",
            "icon" => ""
        ),
        "2" => array(
            "ENUM" => 2, # IPC - como se fosse ID, cuidado ao alterar
            "nome" => "Conselho Fiscal",
            "icon" => ""
        ),
        "3" => array(
            "ENUM" => 3, # IPC - como se fosse ID, cuidado ao alterar
            "nome" => "Comitê de Investimentos",
            "icon" => ""
        ),
        "4" => array(
            "ENUM" => 4, # IPC - como se fosse ID, cuidado ao alterar
            "nome" => "Diretoria Executiva",
            "icon" => ""
        ),
    );
    private $aPesquisa = array(
        "Colegiado" => array(
            "ENUM" => 1, # IPC - como se fosse ID, cuidado ao alterar
            "nome" => "Colegiado",
            "div" => "colegiado",
        ),
        "Data de posição" => array(
            "ENUM" => 2, # IPC - como se fosse ID, cuidado ao alterar
            "nome" => "Data de posição",
            "div" => "data_posicao",
        ),
        "Nome" => array(
            "ENUM" => 3, # IPC - como se fosse ID, cuidado ao alterar
            "nome" => "Nome",
            "div" => "nome",
        ),
        "Cargo FBB" => array(
            "ENUM" => 4, # IPC - como se fosse ID, cuidado ao alterar
            "nome" => "Cargo FBB",
            "div" => "cargo",
        ),
        "Intervalo de tempo de mandatos" => array(
            "ENUM" => 5, # IPC - como se fosse ID, cuidado ao alterar
            "nome" => "Intervalo de tempo de mandatos",
            "div" => "intervalo",
        ),
    );

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $aColegiado = $this->aColegiado;
        $aPesquisa = $this->aPesquisa;
        $cargos = CargosFbb::toSelectArray();

        $mandato = Mandato::query();

        $aWhere = array();
        if ($aWhere)
            $mandato = $mandato->where($aWhere);

        $aJoin = array();
        if ($aJoin)
            $mandato = $mandato->join($aJoin);

        $registros = $mandato->paginate($this->paginate);

        return view('admin.mandato.index', compact("aColegiado", "aPesquisa", "cargos", "registros"));
    }

    public function search(Request $request) {
        $aColegiado = $this->aColegiado;
        $aPesquisa = $this->aPesquisa;
        $cargos = CargosFbb::toSelectArray();

        $filtro = (object) $request['filtro'];
        $mandato = Mandato::query();

        switch (true) {
            case!empty($filtro->colegiado):
                $mandato->where('colegiado', $filtro->colegiado);
                break;
            case!empty($filtro->data_posicao):
                $mandato->where('data_eleicao', $filtro->data_posicao);
                break;
            case!empty($filtro->nome):
                $mandato->whereHas('usuario', function($query) use ($filtro) {
                    $query->where('name', 'like', '%' . $filtro->nome . '%');
                });
                break;
            case!empty($filtro->cargo):
                $mandato->whereHas('usuario', function($query) use ($filtro) {
                    $query->where('cargo_FBB', 'like', '%' . $filtro->cargo . '%');
                });
                break;
            case!empty($filtro->intervalo):
                !empty($filtro->intervalo['colegiado']) ? $mandato->where('colegiado', $filtro->intervalo['colegiado']) : null;
                $mandato->whereBetween('data_eleicao', [$filtro->intervalo['data1'], $filtro->intervalo['data2']]);
                break;
        }

        $registros = $mandato->paginate($this->paginate);

        // dd($filtro, $registros);
        return view('admin.mandato.index', compact("aColegiado", "aPesquisa", "cargos", "registros"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function adicionar($id = null) {
        $aColegiado = $this->aColegiado;
        $cargos = CargosFbb::toSelectArray();

        if ($id)
            $registro = Mandato::find($id);

        $aUser = DB::table('users')->orderBy('name', 'asc')->get();

        return view('admin.mandato.create', compact("aColegiado", "cargos", "registro", "aUser"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function salvar(Request $request) {
        $aColegiado = $this->aColegiado;
        $cargos = CargosFbb::toSelectArray();

        # validacao
        $messages = Mandato::getValidate();
        $this->validate($request, $messages["validate"], $messages["messages"]);

        # dados para salvar
        $dados = $request->all();
        $dados["colegiado"] = $aColegiado[$dados["colegiado"]]["nome"];
        $dados["humanograma_id"] = 1;

        if ($request->file('termo_posse'))
            $dados['termo_posse'] = prepareFile($request->file('termo_posse'), "arquivos/mandato");

        Mandato::salvar($dados);

        return redirect()->route('admin.mandato')->with('message', 'Mandato adicionado com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Mandato  $mandato
     * @return \Illuminate\Http\Response
     */
    public function editar($id) {
        $aColegiado = $this->aColegiado;
        $cargos = CargosFbb::toSelectArray();
        $registro = Mandato::find($id);
        
        $cargos_user = array_keys($registro->getCargosIds());        

        $aUser = DB::table('users')->orderBy('name', 'asc')->get();

        return view('admin.mandato.update', compact("aColegiado", "cargos", "cargos_user", "registro", "aUser"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Mandato  $mandato
     * @return \Illuminate\Http\Response
     */
    public function atualizar(Request $request, Mandato $mandato) {
        $aColegiado = $this->aColegiado;
        $cargos = CargosFbb::toSelectArray();

        # validacao
        $messages = Mandato::getValidate();
        $this->validate($request, $messages["validate"], $messages["messages"]);

        # dados para salvar
        $dados = $request->all();
        $dados["colegiado"] = $aColegiado[$dados["colegiado"]]["nome"];
        $dados["humanograma_id"] = 1;

        if ($request->file('termo_posse'))
            $dados['termo_posse'] = prepareFile($request->file('termo_posse'), "arquivos/mandato");

        Mandato::salvar($dados);

        return redirect()->route('admin.mandato')->with('message', 'Mandato editado com sucesso.');
    }

    public function deletar($id) {
        Mandato::find($id)->delete();
        return redirect()->route('admin.mandato')->with('message', 'Mandto excluído com sucesso.');
    }

}
