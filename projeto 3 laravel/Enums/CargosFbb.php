<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CargosFbb extends Enum
{
    const ConselheiroCurador = 1;
    const ConselheiroCuradorNato = 2;
    const ConselheiroCuradorTemporarioPúblico = 3;
    const ConselheiroCuradorTemporarioPrivado = 4;
    const Presidente = 5;
    const DiretorExecutivo = 6;
    const Gerente = 7;
    const GovernançaInstituidor = 8;
    const NegocialInstituidor = 9;
    const OrgaosDeControleEFiscalizacao = 10;
    const Assessor = 11;
    const Assistente = 12;
    const Contratado = 13;
    const Estagiário = 14;
    const ConselheiroFiscal = 15;
    const ConselheiroDoComitêDeInvestimentos = 16;

    public static function getDescription($value): string
    {
        if ($value === self::OrgaosDeControleEFiscalizacao) {
            return 'Órgãos de controle e Fiscalização';
        }

        return parent::getDescription($value);
    }

    /*
     * Helper para listagens exibir as descrições dos Enums
     * pegando os ids do banco separados por ,
     * Ex: 1,2,3 vai exibir a descrição
     * Conselheiro curador,Conselheiro curador nato,Conselheiro curador temporario público
     */
    public static function getDescriptionFromModel($keys)
    {
        if (empty($keys)) {
            return '';
        }

        $descriptions = [];

        foreach (explode(',', $keys) as $value) {
            $descriptions[] = self::getDescription((int)$value);
        }

        return implode(', ', $descriptions);
    }


    public static function getByIds($selected): array
    {
        $arr = [];

        foreach (explode(',', $selected) as $value) {
            $arr[$value] = self::getDescription((int)$value);
        }

        return $arr;
    }

    public static function toOptions($selected = []): string
    {
        $html = '';
        $data = static::toArray();

        foreach($data as $key => $value) {
            $html .= '<option value="';
            $html .= $key;
            $html .= '"';
            if (array_key_exists($key, $selected)) {
                $html .= ' selected="selected"';
            }
            $html .= '>';
            $html .= $value;
            $html .= '</option>';
        }

        return $html;
    }
}
