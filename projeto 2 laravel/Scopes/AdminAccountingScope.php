<?php

namespace App\Scopes;

use Adldap\Query\Builder;
use Adldap\Laravel\Scopes\ScopeInterface;

class AdminAccountingScope implements ScopeInterface
{
    /**
     * Apply the scope to a given LDAP query builder.
     *
     * @param Builder $query
     *
     * @return void
     */
    public function apply(Builder $query)
    {
        if(env('LDAP_CHECK_SCOPES', true)) { 
            // The distinguished name of our LDAP group.
            $accountingGepem = 'CN=Gepem,OU=Grupos de Seguranca,OU=FBB,DC=fundacaobb,DC=org,DC=br';
            $accountingGetec = 'CN=Getec,OU=Grupos de Seguranca,OU=FBB,DC=fundacaobb,DC=org,DC=br';
            $accountingGetecEsc = 'CN=Getec - ESC,OU=Grupos de Seguranca,OU=FBB,DC=fundacaobb,DC=org,DC=br';

            $query->orWhereMemberOf($accountingGepem);
            $query->orWhereMemberOf($accountingGetec);
            $query->orWhereMemberOf($accountingGetecEsc);
        }
    }
}