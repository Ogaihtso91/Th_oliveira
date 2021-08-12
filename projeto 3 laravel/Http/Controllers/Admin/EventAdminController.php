<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use App\Event;
use App\Reuniao;
use Carbon\Carbon;
use Validator;
use App\Enums\Permissoes;

class EventAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:'.Permissoes::AdministrarPapeisPermissoesUsuarios['id'].'|'.Permissoes::EditarAdministrarUsuarios['id']]);
    }

    private $paginate = 10;

    public function index()
    {
        $registros = Event::orderBy('data_inicial', 'desc')->paginate($this->paginate);
        return view('admin.agenda.index', compact('registros'));
    }

    public function adicionar()
    {
        $roles = Role::all();
        return view('admin.agenda.adicionar', compact('roles'));
    }

    public function salvar(Request $request)
    {
        $messages = [
            'titulo.required' => 'O preenchimento do campo "Título" é obrigatório para concluir a operação.',
            'data_inicial.required' => 'O preenchimento do campo "Data inicial" é obrigatório para concluir a operação.',
            'data_final.required' => 'O preenchimento do campo "Data final" é obrigatório para concluir a operação.',
            'data_final.after_or_equal' => 'O campo "Data final" deve possuir uma data igual ou posterior ao campo "Data Inicial" para concluir a operação.',
            'hora_inicio.required' => 'O preenchimento do campo "Hora início" é obrigatório para concluir a operação.',
            'papel.*.required' => 'O preenchimento do campo "Papel" é obrigatório para concluir a operação.',
            'local.required' => 'O preenchimento do campo "Local" é obrigatório para concluir a operação.',

        ];

        $validation = Validator::make($request->all(), [
            'titulo'=>'required|string',
            'data_inicial' => 'required|date',
            'data_final' => 'required|date|after_or_equal:data_inicial',
            'hora_inicio' => 'required',
            'local' => 'required',
            'papel.*' => 'required',

        ], $messages);

        $validation->after(function ($validator) {

            // Pega os valores passados pelo POST
            $input = $validator->attributes();

            // Verifica se está a data inicial e maior que a data final
            if ($input['data_inicial'] >  $input['data_final']) {

                    // Adiciona mensagem no validador
                $validator->errors()->add('data_inicial', 'Data inicial maior que a data final');
            }
        });

        // Valida o formulário
        $registro = $validation->validate();

        $event = Event::create($registro);

        if(!empty($registro['papel'])){
            $event->role()->sync($registro['papel']);
        }

        return redirect()->route('admin.agenda')->with('message', 'Evento adicionado com sucesso.');
    }

    public function editar($id)
    {
        $roles = Role::all();
        $registro = Event::find($id);
        $rolesSelected = $registro->role;
        return view('admin.agenda.editar', compact('registro', 'roles', 'rolesSelected'));
    }

    public function atualizar(Request $request, $id)
    {
        $messages = [
            'titulo.required' => 'O preenchimento do campo "Título" é obrigatório para concluir a operação.',
            'data_inicial.required' => 'O preenchimento do campo "Data inicial" é obrigatório para concluir a operação.',
            'data_final.required' => 'O preenchimento do campo "Data final" é obrigatório para concluir a operação.',
            'data_final.after_or_equal' => 'O campo "Data final" deve possuir uma data igual ou posterior ao campo "Data Inicial" para concluir a operação.',
            'hora_inicio.required' => 'O preenchimento do campo "Hora início" é obrigatório para concluir a operação.',
            'papel.*.required' => 'O preenchimento do campo "Papel" é obrigatório para concluir a operação.',
            'local.required' => 'O preenchimento do campo "Local" é obrigatório para concluir a operação.',

        ];

        $validation = Validator::make($request->all(), [
            'titulo'=>'required|string',
            'data_inicial' => 'required|date',
            'data_final' => 'required|date|after_or_equal:data_inicial',
            'hora_inicio' => 'required',
            'local' => 'required',
            'papel.*' => 'required',

        ], $messages);

        $validation->after(function ($validator) {

            // Pega os valores passados pelo POST
            $input = $validator->attributes();

            // Verifica se está a data inicial e maior que a data final
            if ($input['data_inicial'] >  $input['data_final']) {

                    // Adiciona mensagem no validador
                $validator->errors()->add('data_inicial', 'Data inicial maior que a data final');
            }
        });

        // Valida o formulário
        $registro = $validation->validate();

        //reuniao::update(['titulo' => 'Comitê de Investimentos', 'data_inicial' => $event['data'], 'data_final' => $event['data'],'reuniao_id' => $event['id']]);
        
        $reuniao = Reuniao::find(Event::find($id)->reuniao_id);

        if (!empty($reuniao)) {
            $reuniao->data = $registro['data_inicial'];
            $reuniao->save();
        }

        $event = Event::find($id);

        $event->update($registro);

        if (!empty($registro['papel'])) {
            $event->role()->sync($registro['papel']);
        }

        return redirect()->route('admin.agenda')->with('message', 'Evento atualizado com sucesso.');
    }

    public function deletar($id)
    {
        Event::find($id)->delete();
        return redirect()->route('admin.agenda')->with('message', 'Evento excluído com sucesso.');
    }
}
