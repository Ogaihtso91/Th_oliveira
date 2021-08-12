<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Blog;
use App\Theme;
use App\User;
use Auth;
use Redirect;
use Validator;
use Storage;
use URL;
use App\Notifications\UserNewBlog;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role_or_permission:Super Admin|blog.visualizacao'], ['only' => ['index']]);
        $this->middleware(['role_or_permission:Super Admin|blog.cadastro|blog.edicao'], ['only' => ['register','add_edit']]);
        $this->middleware(['role_or_permission:Super Admin|blog.exclusao'], ['only' => ['delete']]);
    }
    
    public function index()
    {
    	$registros = Blog::all();
        return view('admin.blog.index', compact('registros'));
    }

    public function add_edit(Int $id = null)
    {
        if (!empty($id)) $registro = blog::find($id);
        $themes = Theme::orderBy('name', 'asc')->get();
    	return view('admin.blog.add_edit', compact('registro','themes','users'));
    }

    public function register(Request $request)
    {

        $messages = [
            'title.required' => 'Campo "Título" em branco.',
            'summary.required' => 'Campo "Resumo" em branco.',
            'content.required' => 'Campo "Conteúdo" em branco.',
            'image.required' => 'Campo "Imagem" em branco.',
            'image.mimes' => 'Extensão não permitida no campo "Imagem".',
            'image.max' => 'Tamanho máximo permitido da imagem é 2Mb',
        ];

        $validation = Validator::make($request->all(), [
            'title'=>'required',
            'summary'=>'required',
            'content'=>'required',
            'image' => 'mimes:jpeg,bmp,png|max:2000'
        ], $messages);

        $validation->sometimes('image', 'required', function ($input) use ($request) {
            return empty($request->id);
        });

        $validation->after(function ($validator) {

            // Pega os valores passados pelo POST
            $input = $validator->attributes();

            // Verifica se está marcado para promover
            if ($input['promote'] == 1) {

                // Cria objeto do Blog para verificar a quantidade de conteudo promovido
                $blog_obj = Blog::where('promote', 1);

                // Se está editando, não considera o objeto em edição
                if (!empty($input['id'])) $blog_obj->where('id', '!=', $input['id']);

                // Verifica se já existem pelo menos 3
                if ($blog_obj->count() >= 3) {

                    // Adiciona mensagem no validador
                    $validator->errors()->add('promote', 'Existem 3 notícias promovidas.');
                }
            }
        });

        // Valida o formulário
        $validation->validate();

        $registro = $request->all();

        if (!empty($request->id)) {
            $blog_obj = Blog::find($request->id);
        } else {
            $blog_obj = new Blog();
        }

        $blog_image = null;
        if ($request->hasfile('image') && $request->file('image')->isValid()) {

            // Define um aleatório para o arquivo baseado no timestamps atual
            $unique_image_name = uniqid(date('HisYmd'));

            // Recupera a extensão do arquivo
            $extension = $request->image->extension();

            // Define finalmente o nome
            $blog_image = "{$unique_image_name}.{$extension}";
            // Salva a imagem no banco
            if(!$request->image->storeAs('blogs', $blog_image)) {

                return redirect()
                        ->back()
                        ->with('error', 'Falha ao fazer upload da imagem')
                        ->withInput();
            }
            else if (!empty($blog_obj->image)) {

                // Deleta a imagem do repositório
                Storage::delete('blogs/'.$blog_obj->image);
            }

            $registro['image'] = $blog_image;

        }

        if (!empty($blog_obj->id)) {

            $blog_obj->update($registro);

        } else {

            // Verifica se URL amigável é única
            $registro['seo_url'] = \App\Helpers::slug($registro['title']);
            $registro['seo_url'] = \App\Helpers::generate_unique_friendly_url($registro, new Blog);

            $blog_obj = $blog_obj->create($registro);

            //Notificação o usuário da criação de uma nova notícia
            $users = User::all();
            foreach ($users as $item_user) {
                try {
                    $item_user->notify(new UserNewBlog($blog_obj->id));
                } catch (\Exception $e) {
                    Log::error("Send E-mail Error: ".$e->getMessage());
                    continue;
                }
            }
        }

        return redirect('admin/blog')->with('message', 'Notícia salva com sucesso.')->withInput();
    }

    public function deletar($id)
    {
       	$registro = Blog::find($id);
       	Blog::find($id)->delete();
        Storage::delete('blogs/'.$registro->image);
       	return redirect('admin/blog')->with('message', 'Notícia excluída com sucesso.');
    }
}
