<?php

namespace App\Enums;

class Permissoes
{
    const LerCurador = [ 'id' => 1, 'desc' => 'Ler Curador'];
    const LerFiscal = [ 'id' => 2, 'desc' => 'Ler Fiscal'];
    const LerComiteDeInvestimentos = [ 'id' => 3, 'desc' => 'Ler Comitê de Investimentos'];
    const AdministrarPapeisPermissoesUsuarios = [ 'id' => 4, 'desc' => 'Administrar papéis, permissões e usuários'];
    const EditarAdministrarUsuarios = [ 'id' => 5, 'desc' => 'Editar e administrar usuários'];

}
