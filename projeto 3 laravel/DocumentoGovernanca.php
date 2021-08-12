<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Enums\TipoDocumentosGovernanca;

class DocumentoGovernanca extends Model
{
    protected $table = "documentos_governanca";

    protected $fillable = [
        'nome',
        'nomeArquivo',
        'tipo_documento',
    ];

    protected $casts = [
        'tipo_documento' => 'integer',
    ];

    protected $appends = [
        'tipo_documento_descricao',
    ];

    public function getTipoDocumentoDescricaoAttribute()
    {
        return TipoDocumentosGovernanca::getDescriptionById($this->tipo_documento);
    }

    public function getCaminhoArquivo()
    {
        return 'documentos-governanca'. DIRECTORY_SEPARATOR . $this->tipo_documento . DIRECTORY_SEPARATOR . $this->nomeArquivo;
    }
}
