<?php

namespace App\Rules;

use Adldap\Laravel\Validation\Rules\Rule;

class OnlyActiveAccounts extends Rule
{
    /**
     * Determines if the user is allowed to authenticate.
     *
     * @return bool
     */
    public function isValid()
    {
    	//return $this->user->inGroup('Getec');
        // Comando CMDLet para definir eses valor: Set-ADUser -Identity "CN=User,CN=Users,DC=eqweb,DC=com" -Replace @{employeeId=666}
        return $this->user->getEmployeeId() !== null;
    }
}