<?php

namespace App\Repositories;

use App\Contact;

class ContactRepository
{
    private $model;

    public function __construct()
    {
        $this->model = new Contact;
    }

    public function save($branch, $contacts = [])
    {
        foreach($contacts as $contact) {
            if($contact['delete'] == 'S'){ // Apagar
                if(!empty($contact['id'])){
                    $this->model->where('id', $contact['id'])->delete();
                }
            } elseif(!empty($contact['id'])) { // Atualizar
                $this->model->where('id', $contact['id'])->update(array_only($contact, ['type','value']));
            } else { // Adicionar
                $_contact = new Contact;
                $contact['branch_id'] = $branch->id;
                $_contact->create(array_only($contact, ['branch_id','type','value']));
            }

        }
    }

}