<?php

class ProdutoBaixaModel extends BaseModel
{

    private $id;
    private $produtoId;
    private $baixaId;
    private $valor;
    private $quantidade;
    private $ativo;


    public function getProduto()
    {
        $this->load->model('ProdutoModel');

        $produto = $this->ProdutoModel->buscarPorId($this->produtoId);

        if($produto):
            return $produto;
        else:
            return false;
        endif;
    }

    public function getValorTotalPorUnidade()
    {

        return (number_format( ((float) str_replace(',','.', $this->getValor())) * $this->getQuantidade(), 2, ',','.'));
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }


    public function getProdutoId()
    {
        return $this->produtoId;
    }

    public function setProdutoId($produtoId)
    {
        $this->produtoId = $produtoId;
    }


    public function getBaixaId()
    {
        return $this->baixaId;
    }

    public function setBaixaId($baixaId)
    {
        $this->baixaId = $baixaId;
    }


    public function getValor()
    {
        $this->valor = number_format(str_replace(',','.', $this->valor), 2, ',', '.');
        return $this->valor;
    }

    public function setValor($valor)
    {
        $this->valor = str_replace(".", "", $valor);
        $this->valor = str_replace(",", ".", $valor);
    }


    public function getQuantidade()
    {
        return $this->quantidade;
    }

    public function setQuantidade($quantidade)
    {
        $this->quantidade = $quantidade;
    }


    public function getAtivo()
    {
        return $this->ativo;
    }

    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }

    /*metodos*/

    public function inserir()
    {
        $dados = elements(['produtoId', 'baixaId', 'valor', 'quantidade'], get_object_vars($this));

        return $this->db->insert("produtobaixa", $dados);

    }

    public function excluir($id)
    {
        $this->db->where('baixaId', $id);
        return $this->db->update('produtobaixa', array(
            'ativo' => 'n'
        ));
    }

    public function buscarProdutos($id)
    {
        return $this->db->get_where('produtobaixa', array(
            'baixaId'    => $id,
            'ativo' => 's'
        ))->result('ProdutoBaixaModel');
    }

}
