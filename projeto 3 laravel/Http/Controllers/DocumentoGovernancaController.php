<?php

namespace App\Http\Controllers;

use App\DocumentoGovernanca;
use App\DocumentoGovernancaAnexo;
use App\Enums\TipoDocumentosGovernanca;
use App\Http\Requests\DocumentoGovernancaStoreRequest;
use App\Http\Requests\DocumentoGovernancaUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Storage;
use Carbon\Carbon;

class DocumentoGovernancaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $documentosGovernanca = DocumentoGovernanca::query()
            ->groupBy('tipo_documento')
            ->selectRaw('tipo_documento, count(*) as countDocumentos')
            ->get();

        return view('admin.documentos-de-governanca.index')->with(compact('documentosGovernanca'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.documentos-de-governanca.adicionar');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentoGovernancaStoreRequest $request)
    {
        // dd('ESTOU DENTRO DO CONTROLLER', $request->all());

        $data = $request->validated();

        try {
            $this->uploadAnexos($data['tipo_documento'], $data['anexo'], $data['nome']);
        } catch (\Exception $e) {
            dd('Exception capturada: ' . $e->getMessage() . "\n", $e);
        }

        return redirect()->route('admin.documentos-governanca.index')->with('message', 'Documentos do tipo '. TipoDocumentosGovernanca::getDescriptionById($data['tipo_documento']) .' adicionados com sucesso.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DocumentoGovernanca  $documentoGovernanca
     * @return \Illuminate\Http\Response
     */
    public function show($tipoDocumentoGovernanca)
    {
        $documentosGovernanca = DocumentoGovernanca::where('tipo_documento', $tipoDocumentoGovernanca)->get();
        return view('admin.documentos-de-governanca.visualizar')->with(compact('documentosGovernanca', 'tipoDocumentoGovernanca'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DocumentoGovernanca  $documentoGovernanca
     * @return \Illuminate\Http\Response
     */
    public function edit(DocumentoGovernanca $documentoGovernanca)
    {
        // dd($documentoGovernanca);
        return view('admin.documentos-de-governanca.editar')->with(compact('documentoGovernanca'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DocumentoGovernanca  $documentoGovernanca
     * @return \Illuminate\Http\Response
     */
    public function update(DocumentoGovernancaUpdateRequest $request, DocumentoGovernanca $documentoGovernanca)
    {
        // dd($request->all());

        $data = $request->validated();

        try {
            $this->updateAnexo($data, $documentoGovernanca);
        } catch (\Exception $e) {
            dd('Exception capturada: ' . $e->getMessage() . "\n", $e);
        }

        return redirect()->route('admin.documentos-governanca.index')->with('message', 'Documento '. $documentoGovernanca->nome.' atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DocumentoGovernanca  $documentoGovernanca
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocumentoGovernanca $documentoGovernanca)
    {
        $documentoGovernanca->delete();
        return redirect()->route('admin.documentos-governanca.index')->with('message', 'Documento '. $documentoGovernanca->nome.' excluído com sucesso.');
    }

    public function uploadAnexos(int $tipo_documento, array $anexos = [], array $nomeAnexos = [])
    {
        foreach ($anexos as $key => $anexo) {
            if (!empty($anexos) && $anexo->isValid()) {
                $nomeArquivo = $anexo->getClientOriginalName();
                $nomeArquivo = create_file_name_from_existing_name($nomeArquivo);
                $extensao = $anexo->getClientOriginalExtension();

                $aux_nome = 0;
                $nomeOriginalArquivo = str_replace('.'.$extensao, '', $nomeArquivo);
                $caminhoArquivo = 'documentos-governanca'. DIRECTORY_SEPARATOR . $tipo_documento . DIRECTORY_SEPARATOR;
                while (Storage::exists($caminhoArquivo . $nomeArquivo)) {
                    $aux_nome++;
                    $nomeArquivo = $nomeOriginalArquivo.'('.$aux_nome.").".$extensao;
                }

                if ($anexo->storeAs($caminhoArquivo, $nomeArquivo)) {
                    DocumentoGovernanca::create([
                        'nome' => $nomeAnexos[$key],
                        'nomeArquivo' => $nomeArquivo,
                        'tipo_documento' => $tipo_documento,
                    ]);
                }
            }
        }
    }

    public function updateAnexo($data, DocumentoGovernanca $documentoGovernanca)
    {
        $nomeAnexo = $data['nome'];
        $anexo = $data['anexo'] ?? null;
        if (!is_null($anexo) && $anexo->isValid()) {
            $nomeArquivo = $anexo->getClientOriginalName();
            $nomeArquivo = create_file_name_from_existing_name($nomeArquivo);
            $extensao = $anexo->getClientOriginalExtension();

            $aux_nome = 0;
            $nomeOriginalArquivo = str_replace('.'.$extensao, '', $nomeArquivo);
            $caminhoArquivo = 'documentos-governanca'. DIRECTORY_SEPARATOR . $documentoGovernanca->tipo_documento . DIRECTORY_SEPARATOR;
            while (Storage::exists($caminhoArquivo . $nomeArquivo)) {
                $aux_nome++;
                $nomeArquivo = $nomeOriginalArquivo.'('.$aux_nome.").".$extensao;
            }

            if ($anexo->storeAs($caminhoArquivo, $nomeArquivo)) {
                $documentoGovernanca->update([
                    'nome' => $nomeAnexo,
                    'nomeArquivo' => $nomeArquivo,
                ]);
            }
        } else {
            if (empty($anexos)) {
                $documentoGovernanca->update([
                    'nome' => $nomeAnexo,
                ]);
            }
        }
    }
}
