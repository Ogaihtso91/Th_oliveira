<?php

class ProdutoModel extends BaseModel
{
    private $id;
    private $codigo;
    private $nome;
    private $ativo;


    public function getAtivo()
    {
        return $this->ativo;
    }

    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }


    public function getCodigo()
    {
        return $this->codigo;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }


    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function inserir()
    {
        $dados = elements(['codigo', 'nome'], get_object_vars($this));
        return $this->db->insert("produto", $dados);
    }

    public function verificaSeExiste($codigo)
    {
        return $this->db->get_where('produto', array(
            'codigo'     => $codigo,
            'ativo'    => 's'
        ))->row(0, 'ProdutoModel');
    }

    public function buscar($limite = null, $offset = null)
    {
        if(is_null($limite) && is_null($offset)):
            $this->db->order_by("nome", "asc");
            return $this->db->get_where('produto', array(
                'ativo' => 's'
            ))->result('ProdutoModel');

        else:
            $this->db->order_by("nome", "asc");
            return $this->db->get_where('produto', array(
                'ativo' => 's'
            ), $limite, $offset)->result('ProdutoModel');

        endif;
    }

    public function exibir()
    {
        $this->db->order_by("id", "desc");
        $this->db->limit(7);
        return $this->db->get_where('produto', array(
            'ativo' => 's'
        ))->result('ProdutoModel');
    }

    public function buscarPorId($id)
    {
        return $this->db->get_where('produto', array(
            'id'    => $id,
            'ativo' => 's'
        ))->row(0, 'ProdutoModel');
    }

    public function excluir($id)
    {
        $this->db->where('id', $id);
        return $this->db->update('produto', array(
            'ativo' => 'n'
        ));
    }

    public function editar($dados)
    {
        $this->db->where('id', $dados->getId());
        return $this->db->update('produto', array(
            'codigo' => $dados->getCodigo(),
            'nome'   => $dados->getNome()
        ));

    }

}
