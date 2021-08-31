<?php

class BaseModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function popula($dados)
    {

        foreach( (array) $dados as $column => $value):
            $this->{'set' . ucfirst($column)}($value);
        endforeach;
        return $this;
    }

}