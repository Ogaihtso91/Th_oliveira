<?php

namespace App\Http\Controllers;

use App\Noticia;
use App\NoticiaImagem;
use Illuminate\Http\Request;
use App\Http\Requests\StoreNoticiaRequest;

class NoticiaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Noticia::orderBy('created_at');

        if ($request->get('tipo')) {
            $query = $query->whereTipo($request->get('tipo'));
        }

        if ($request->get('titulo')) {
            $query = $query->where('titulo', 'like', '%'.$request->get('titulo').'%');
        }

        if ($request->get('subtitulo')) {
            $query = $query->where('subtitulo', 'like', '%'.$request->get('subtitulo').'%');
        }

        $noticias = $query->paginate(config('paginate.default'));

        return view('admin.noticias-fbb.index', [
            'noticias' => $noticias,
         ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
     	return view('admin.noticias-fbb.adicionar');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNoticiaRequest $request)
    {
        $data = $request->all();

        # Faz o upload da imagem da capa para o diretório public/noticias
        if ($request->file('imagem_capa')) {
            $data['imagem_capa'] = $this->imagemCapaUpload($request->file('imagem_capa'));
        }

        # Persiste o novo registro.
        $noticia = Noticia::create($data);

        # Atualiza a tabela de noticia_imagem para vincular o noticia_id
        if (!empty($data['galeria_id'])) {
            NoticiaImagem::where('galeria_id', $data['galeria_id'])->update(['noticia_id' => $noticia->id]);
        }

        return redirect()->route('noticias.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Noticia $noticia)
    {
        return view('admin.noticias-fbb.visualizar', [
            'noticia' => $noticia
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Noticia $noticia)
    {
     	return view('admin.noticias-fbb.editar', [
            'noticia' => $noticia
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Noticia $noticia)
    {
        $data = $request->all();

        # Faz o upload da imagem da capa para o diretório public/noticias
        if ($request->file('imagem_capa')) {
            $data['imagem_capa'] = $this->imagemCapaUpload($request->file('imagem_capa'));
        }

        $noticia->update($data);

        # Atualiza a tabela de noticia_imagem para vincular o noticia_id
        if (!empty($data['galeria_id'])) {
            NoticiaImagem::where('galeria_id', $data['galeria_id'])->update(['noticia_id' => $noticia->id]);
        }

        return redirect()->route('noticias.index')->with('success', 'Notícia atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Noticia $noticia)
    {
        # Verifica se existem imagens vinculadas a notícia e deleta.
        if (!$noticia->imagens->isEmpty()) {
            $noticia->imagens()->delete();
        }

        if ($noticia->delete()) {
            return redirect()->route('noticias.index')->with('success', 'Notícia removida com sucesso!');
        }
    }

    /**
     * Upload da galeria de imagens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $galeria_id
     * @return \Illuminate\Http\Response
     */
    public function galeriaUpload(Request $request)
    {
        sleep(3);

        # Faz o upload para o diretório public/noticias
        if ($request->file('filepond')) {
            $imagem = microtime().'.'.$request->file('filepond')->getClientOriginalExtension();
            $request->file('filepond')->move(public_path('noticias'), $imagem);

            $galeria_id = $request->get('galeria_id');

            # Cria um novo ID de galeria para ser usado durante o cadastro.
            if (empty($galeria_id)) {
                $galeria_id = NoticiaImagem::max('galeria_id') + 1;
            }

            # Salva a imagem na galeria e retorna o ID da galeria criado para possíveis próximos uploads
            $galeria = NoticiaImagem::create([
                'imagem' => $imagem,
                'galeria_id' => $galeria_id
            ]);

            return $galeria_id;
        }
    }

    /**
     * Upload da imagem de capa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $galeria_id
     * @return \Illuminate\Http\Response
     */
    public function imagemCapaUpload($file)
    {
        # Faz o upload da imagem da capa para o diretório public/noticias
        $imagem_capa = time().'.'.$file->getClientOriginalExtension();
        $file->move(public_path('noticias'), $imagem_capa);

        return $imagem_capa;
    }

}
