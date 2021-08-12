<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class InstitutionOfficePosts extends Enum
{


/*
|-------- tarefe 4373  inclusão feita por marcio.rosa-------        
*/  
    const President = 6;
    const Director = 7;
    const Cordinator = 8;
    const Researcher = 9;
    const Teacher = 10;
/*
|------------------------*/
    const Assessor = 1;
    const AssistenteSocial = 2;
    const ChefeDepartamento = 3;
    const ChefeDivisao = 4;
    const ChefeGeral = 5;

    const other = 11;


    public static function getDescription($value): string
    {
    	
        /*
        |-------- tarefe 4373  inclusão feita por marcio.rosa-------        
        */              
        if ($value === self::President) {
            return 'Presidente';
        }
        if ($value === self::Director) {
            return 'Diretor(a)';
        }
        if ($value === self::Cordinator) {
            return 'Coordenador(a)';
        }
        if ($value === self::Researcher) {
            return 'Pesquisador(a)';
        }
        if ($value === self::Teacher) {
            return 'Professor(a)';
        }

        if ($value === self::Assessor) {
    		return 'Assessor(a)';
        }

    	if ($value === self::AssistenteSocial) {
    		return 'Assistente social';
        }

    	if ($value === self::ChefeDepartamento) {
    		return 'Chefe de departamento';
        }

    	if ($value === self::ChefeDivisao) {
    		return 'Chefe de divisão';
        }

    	if ($value === self::ChefeGeral) {
    		return 'Chefe de geral';
        }

        # Ticket 4747
        if ($value === self::other) {
            return 'Outro';
        }

    	return parent::getDescription($value);
    }
}
