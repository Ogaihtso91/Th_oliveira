<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Testimony;
use Validator;
use Storage;

class TestimonyController extends Controller
{
    public function index()
    {
    	$registros = Testimony::all();
        return view('admin.testimony.index', compact('registros'));
    }

    public function add_edit(Int $id = null)
    {
        if (!empty($id)) $registro = Testimony::find($id);
    	return view('admin.testimony.add_edit', compact('registro'));
    }

    public function register(Request $request)
    {

        $messages = [
            'title.required' => 'Campo "Título" em branco.',
            'description.required' => 'Campo "Descrição" em branco.',
            'image.required' => 'Campo "Imagem" em branco.',
            'image.image' => 'Extensão não permitida no campo "Imagem".',
            'image.size' => 'Tamanho máximo permitido da imagem é 3Mb',
        ];

        $validation = Validator::make($request->all(), [
            'title'=>'required',
            'description'=>'required',
            'image' => 'image|max:3000'
        ], $messages);

        $validation->sometimes('image', 'required', function ($input) use ($request) {
            return empty($request->id);
        });

        $validation->after(function ($validator) {

            // Pega os valores passados pelo POST
            $input = $validator->attributes();

            // Verifica se está marcado para ativar
            if ($input['active'] == 1) {

                // Cria objeto do depoimento para verificar a quantidade de conteudo ativado
                $testimony_obj = Testimony::where('active', 1);

                // Se está editando, não considera o objeto em edição
                if (!empty($input['id'])) $testimony_obj->where('id', '!=', $input['id']);

                // Verifica se já existem pelo menos 1
                if ($testimony_obj->count() >= 1) {

                    // Adiciona mensagem no validador
                    $validator->errors()->add('active', 'Existe depoimento inicial selecionado');
                }
            }
        });

        // Valida o formulário
       $validation->validate();

       $registro = $request->all();

       if (!empty($request->id)) {
            $testimony_obj = Testimony::find($request->id);
       } else {
            $testimony_obj = new Testimony();
       }

        $testimony_image = null;
        if ($request->hasfile('image') && $request->file('image')->isValid()) {

            // Define um aleatório para o arquivo baseado no timestamps atual
            $unique_image_name = uniqid(date('HisYmd'));

            // Recupera a extensão do arquivo
            $extension = $request->image->extension();

            // Define finalmente o nome
            $testimony_image = "{$unique_image_name}.{$extension}";

            // Salva a imagem no banco
            if(!$request->image->storeAs('testimonies', $testimony_image)) {

                return redirect()
                        ->back()
                        ->with('error', 'Falha ao fazer upload da imagem')
                        ->withInput();
            }
            else if (!empty($testimony_obj->image)) {

                // Deleta a imagem do repositório
                Storage::delete('testimonies/'.$testimony_obj->image);
            }

            $registro['image'] = $testimony_image;

        }

        if (!empty($testimony_obj->id)) $testimony_obj->update($registro);
        else $testimony_obj->create($registro);

        return redirect('admin/depoimentos')->with('message', 'Depoimento salvo com sucesso.')->withInput();
    }

    public function delete($id)
    {
       	$registro = Testimony::find($id);
       	Testimony::find($id)->delete();
        Storage::delete('testimonies/'.$registro->image);
       	return redirect('admin/depoimentos')->with('message', 'Depoimento excluído com sucesso.');
    }
}
