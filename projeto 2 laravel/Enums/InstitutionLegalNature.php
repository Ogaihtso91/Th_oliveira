<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class InstitutionLegalNature extends Enum
{
    const Associacao = 1;
    const Condominio = 2;
    const ConsorcioSociedades = 3;
    const Cooperativa = 4;
    const EmpresaPublica = 5;
    const EntidadeSindical = 6;
    const RepresentacaoBrasilFundacaoAssociacaoEstrangeira = 7;
    const RepresentacaoBrasilSociedadeEstrangeira = 8;
    const Fundacao = 9;
    const GrupoSociedades = 10;
    const InstituicaoEnsino = 11;
    const InstituicaoSaude = 12;
    const InstituicaoReligiosa = 13;
    const InstitutoPesquisa = 14;
    const InstitutoTecnologia = 15;
    const OrganizacaoSociedadeCivilInteressePublico = 16;
    const OrganizacaoNaoGovernamental= 17;
    const OrganizacaoSocial = 18;
    const OrgaoPublicoAutonomoUniao = 19;
    const OrgaoPublicoAutonomoestadualOuDoDistritoFederal = 20;
    const OrgaoPublicoAutonomomunicipal = 21;
    const OrgaoPublicoDoPoderExecutivoEstadualOuDoDistritoFederal = 22;
    const OrgaoPublicoDoPoderExecutivoFederal = 23;
    const OrgaoPublicoDoPoderExecutivoMunicipal = 24;
    const OrgaoPublicoDoPoderJudiciarioEstadual = 25;
    const OrgaoPublicoDoPoderJudiciarioFederal = 26;
    const OrgaoPublicoDoPoderLegislativoEstadualOuDoDistritoFederal = 27;
    const OrgaoPublicoDoPoderLegislativoFederal = 28;
    const OrgaoPublicoDoPoderLegislativoMunicipal = 29;
    const Partidopolitico = 30;
    const ServiçoSocialAutonomo = 31;
    const InstituicaoPrivadaComFinalidadeLucrativa = 32;
    const FundacaoFederal = 33;
    const AutarquiaFederal = 34;
    const AutarquiaEstadual = 35;
    const FundaçãoPrivadaSemFinsLucrativos = 36;
    const SociedadeAnonimaAberta = 37;
    const EmpresarioIndividual = 38;

    public static function getDescription($value): string
    {
    	if ($value === self::Associacao) {
    		return 'Associação';
        }

        if ($value === self::Condominio) {
    		return 'Condomínio';
        }

        if ($value === self::ConsorcioSociedades) {
    		return 'Consórcio de sociedades';
        }

        if ($value === self::Cooperativa) {
    		return 'Cooperativa';
        }

        if ($value === self::EmpresaPublica) {
    		return 'Empresa Pública';
    	}

    	return parent::getDescription($value);
    }
}
