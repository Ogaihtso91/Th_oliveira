<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AutoMessage;
use App\Award;
use Illuminate\Routing\Route;

class AutoMessageController extends Controller
{
    protected $auto_messages;
    protected $awards;

    public function __construct(AutoMessage $auto_messages, Award $awards)
    {
        $this->auto_messages = $auto_messages;
        $this->awards = $awards;
    }

    /**
     * show list of messages all msg
     *
     * @param \Illuminate\Http\Request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $messages = $this->auto_messages->all();
        // dump($messages);
        return view('admin.automessage.index', compact('messages'));
    }

    /**
     * Show the form to create a Auto Message
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $awards = $this->awards->all();

        return view('admin.automessage.create', compact('awards'));
    }

    /**
     * store a AutoMessage into the database
     *
     * @method POST
     * @param Illuminate\Http\Request
     *
     * @return Illuminate\Http\Response
     */
    public function register(Request $request) {

        $validator = $request->validate([
            'id' => 'sometimes|integer|exists:auto_messages,id',
            'award' => 'required|integer|exists:awards,id',
            'type' => 'required|integer',
            'message_body' => 'required'
        ]);

        if(empty($validator['award'])) {
            return redirect(route('admin.automessage.register'))
                ->withErrors('Error lost args, entrar em contato com o administrador [ERR0055]');
        }

        $msg = $this->auto_messages->store($validator);

        if(!$msg) {
            if(Route::is('admin.automessage.register')) {
                return redirect(route('admin.automessage.register'))
                    ->withErrors('Erro ao adicionar mensagem.');
            } elseif (Route::is('admin.automessage.edit')) {
                return redirect(route('admin.automessage.register'))
                    ->withErrors('Erro ao editar a mensagem.');
            }
        }

        return redirect(route('admin.automessage.index'))
                    ->with('message', 'Messagem criada com sucesso!');
    }

    /**
     * show form for edit a message
     *
     * @param Illuminate\Http\Request
     *
     * @return Illuminate\Http\Response
     */
    public function edit(Request $request) {

        if(empty($request->message_id)) {
            return redirect(route('admin.automessage.register'))
                ->withErrors('Não foi possivel achar a mensagem. [ERR - 403]');
        }

        $msg = $this->auto_messages->find($request->message_id);
        $awards = $this->awards->all();

        if(empty($msg)) {
            return redirect(route('admin.automessage.register'))
                ->withErrors('Não foi possivel achar a mensagem. [ERR - 404]');
        }

        return view('admin.automessage.create', ['message_id' => $request->message_id],compact('msg', 'awards'));
    }

    /**
     * delete a message
     *
     * @param Illuminate\Http\Request
     *
     * @return Illuminate\Http\Response
     */
    public function delete(Request $request) {

        if(empty($request->message_id)) {
            return redirect(route('admin.automessage.index'))
                ->withErrors('Não foi possivel excluir a mensagem. [ERR - 2313]');
        }

        $award = $this->auto_messages->find($request->message_id)->award;

        // find the message
        AutoMessage::find($request->message_id)->award()
            ->dissociate($award)->delete();

        return redirect(route('admin.automessage.index'))
            ->with('message', 'Mensagem excluida com sucesso!');
    }
}
