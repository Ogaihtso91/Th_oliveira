<?php

namespace App\Http\Controllers;

use App\Enums\CargosFbb;
use App\Http\Requests\HumanogramaRequest;
use App\Http\Requests\HumanogramaUpdate;
use App\Humanograma;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use PDF;

class HumanogramaController extends Controller {

    /**
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $limit = $request->limit ?? 10;

        $humanogramas = Humanograma::paginate($limit);
        $users = User::role(['Conselho Fiscal', 'Conselho Curador', 'Comitê de Investimentos', 'Secex'])->orderby('name', 'asc')->get();
        $roles = Role::whereNotIn('name', ['Admin'])->get();
        $cargos = CargosFbb::toSelectArray();

        return view('users.humanograma.index', compact(['humanogramas', 'users', 'roles', 'cargos', 'limit']));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response
     */
    public function search(Request $request) {
        $query = User::query()->whereHas('humanograma');

        switch ($request['filter']) {
            case 'nome':
                $query->where('name', 'like', '%' . $request['value'] . '%');
                break;
            case 'papel':
                $query->whereHas('roles', function($query) use ($request) {
                    $query->where('name', $request['value']);
                });
                break;
            case 'cargo_externo':
                $query->where('cargo', 'like', '%' . $request['value'] . '%');
                break;
            case 'cargo_FBB':
                $query->where('cargo_FBB', 'like', '%' . $request['value'] . '%');
                break;
        }

        $results = $query->with('roles')->get();

        // dd($results);

        return view('users.humanograma.result', compact('results'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Collection $research
     */
    public function searchReport(Request $request) {
        // dd(json_decode($request->research));
        $research = Collection::wrap(json_decode($request->research));
        $today = Carbon::now();

        $pdf = PDF::loadView('users.humanograma.report', compact('research', 'today'))
                ->setOption("encoding", "UTF-8")
                ->setOption('margin-left', 2)
                ->setOption('margin-right', 2)
                ->setOrientation('landscape');

        return $pdf->inline('research_report.pdf');
    }

    public function create() {
        $cargoFBB = CargosFbb::toSelectArray();
        $roles = Role::whereNotIn('name', ['Admin'])->get();

        return view('admin.humanograma.create', compact('cargoFBB', 'roles'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(HumanogramaRequest $request) {
        $validado = $request->validated();
        $validado['cargo_FBB'] = implode(',', $validado['cargo_FBB']);

        // dd($validado);

        if ($request->hasfile('foto')) {
            $validado['foto'] = prepareFile($request->file('foto'), "img/humanograma");
        }

        if ($request->hasfile('curriculo')) {
            $validado['curriculo'] = prepareFile($request->file('curriculo'), "arquivos/humanograma");
        }

        if ($request->hasfile('termo_posse')) {
            $validado['termo_posse'] = prepareFile($request->file('termo_posse'), "arquivos/humanograma");
        }

        // Inicializamos uma transaction para caso ocorra erro ao persistir
        // algums dos dados de usuário ou humanograma.
        DB::beginTransaction();

        try {
            $usuario = User::create($validado);
            $usuario->syncRoles($validado['grupo']);

            $validado['usuario_id'] = $usuario->id;
            Humanograma::create($validado);

            DB::commit();
            $feedback = ['success' => 'Humanograma adicionado com sucesso!'];
        } catch (\Exception $e) {
            DB::rollBack();
            $feedback = ['Error' => $e->getMessage()];
        }

        // dd($validado, $feedback, $humanograma);
        return redirect()
                        ->route('users.humanograma.index')
                        ->with($feedback);
    }

    public function show(Humanograma $humanograma) {
        $cargosFBB = explode(',', CargosFbb::getDescriptionFromModel($humanograma->usuario->cargo_FBB));

        return view('users.humanograma.show', compact('humanograma', 'cargosFBB'));
    }

    public function edit(Humanograma $humanograma) {
        $cargoFBB = CargosFbb::toSelectArray();
        $roles = Role::whereNotIn('name', ['Admin'])->get();

        return view('admin.humanograma.edit', compact('humanograma', 'cargoFBB', 'roles'));
    }

    public function update(HumanogramaUpdate $request, Humanograma $humanograma) {
        $validado = $request->validated();
        $validado['cargo_FBB'] = implode(',', $validado['cargo_FBB']);

        if ($request->hasfile('foto')) {
            $validado['foto'] = prepareFile($request->file('foto'), "img/humanograma");
        }

        if ($request->hasfile('curriculo')) {
            $validado['curriculo'] = prepareFile($request->file('curriculo'), "arquivos/humanograma");
        }

        if ($request->hasfile('termo_posse')) {
            $validado['termo_posse'] = prepareFile($request->file('termo_posse'), "arquivos/humanograma");
        }

        DB::beginTransaction();

        try {
            $humanograma->usuario->update($validado);
            $humanograma->usuario->syncRoles($validado['grupo']);

            $humanograma->update($validado);

            DB::commit();
            $feedback = ['success' => 'Humanograma editado com sucesso!'];
        } catch (\Exception $e) {
            DB::rollBack();
            $feedback = ['Error' => $e->getMessage()];
        }

        return redirect()
                        ->route('users.humanograma.index')
                        ->with($feedback);
    }

    /**
     * @param \Illuminate\Http\Request $request->item_id
     * @return \Illuminate\Http\Response::JSON
     */
    public function destroy(Request $request) {
        try {
            // TODO: verificar se temos alguma regra de negocio.
            Humanograma::find($request->item_id)->delete();

            return response()->json([], 204);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), $e->getCode());
        }
    }

    public function dataByUser(Request $request) {
        # retorna os dados do humanograma pelo id do usuario
        $id_usuario = ($request->usuario_id);
        $ret = array();
        $ret["usuario"] = User::find($id_usuario)->toArray();
        $ret["cargos"] = array_keys(CargosFbb::getByIds($ret["usuario"]["cargo_FBB"]));
        $humanograma = Humanograma::whereHas('usuario', function($query) use ($request) {
                    $query->where("id", $request->usuario_id);
                })->first();
        $ret["humanograma"] = $humanograma;
        $ret["humanograma"]["curriculo"] = $humanograma ? asset($humanograma->curriculo) : null;
//        dd($ret);
        return json_encode($ret);
    }

}
