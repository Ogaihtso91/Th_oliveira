<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class TipoDocumentosGovernanca
{
    const Prestacao_de_contas = 1;
    const Normativos_internos = 2;
    const Fundo_patrimonial = 3;
    const Demais_documentos = 4;
    const Estatuto = 5;
    const Regimento_FBB = 6;
    const Codigo_de_etica_e_normas_de_conduta = 7;
    const Organograma = 8;
    const Composicao_dos_orgaos_colegiados = 9;
    const Relatorio_de_atividades = 10;
    const Plano_estrategico = 11;
    const Orcamento = 12;
    const Programa_de_integridade = 13;
    const Regimento_dos_comites_internos = 14;
    const Regimento_do_comite_de_investimentos = 15;
    const Politicas_institucionais = 16;
    const Programas_estruturados = 17;
    const Ato_de_delegacão_de_competencia = 18;
    const Acordo_de_trabalho = 19;
    const Riscos_e_controles_internos = 20;
    const Demandas_judiciais = 21;

    public static function getDescriptionById($id)
    {
        return collect(self::getArray())->filter(function ($item, $key) use ($id) {
            return $key == $id;
        })->first();
    }

    public static function getArray()
    {
        return [
            19 => 'Acordo de trabalho',
            18 => 'Ato de delegação de competência',
            7 => 'Código de ética e normas de conduta',
            9 => 'Composição dos órgãos colegiados',
            4 => 'Demais documentos',
            21 => 'Demandas judiciais',
            5 => 'Estatuto',
            3 => 'Fundo patrimonial',
            2 => 'Normativos internos',
            12 => 'Orçamento',
            8 => 'Organograma',
            11 => 'Plano estratégico',
            16 => 'Políticas institucionais',
            1 => 'Prestação de contas',
            13 => 'Programa de integridade',
            17 => 'Programas estruturados',
            15 => 'Regimento do comitê de investimentos',
            14 => 'Regimento dos comitês internos',
            6 => 'Regimento FBB',
            10 => 'Relatório de atividades',
            20 => 'Riscos e controles internos',
        ];
    }
}
