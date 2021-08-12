<?php

class EstoqueModel extends BaseModel
{
    private $produtoIdVerificador;
    private $produtoId;
    private $id;
    private $data;
    private $quantidade;
    private $valor;
    private $lucro;

    private $qtdDisponivel;

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


    public function getProdutoId()
    {
        return $this->produtoId;
    }

    public function setProdutoId($produtoId)
    {
        $this->produtoId = $produtoId;

    }


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }


    public function getData()
    {
        if(empty($this->data)) return date("d/m/Y");
        $data = str_replace("-", "/", $this->data);
        $data = date('d/m/Y', strtotime($data));
        return $data;
    }

    public function setData($data)
    {
        $data = str_replace("/", "-", $data);
        $this->data = date('Y-m-d', strtotime($data));
    }


    public function getQuantidade()
    {
        return $this->quantidade;
    }

    public function setQuantidade($quantidade)
    {
        $this->quantidade = $quantidade;
    }


    public function getValor()
    {
        $this->valor = number_format((float)$this->valor, 2, ',', '.');
        return $this->valor;
    }

    public function setValor($valor)
    {
        $this->valor = str_replace(".", "", $valor);
        $this->valor = str_replace(",", ".", $valor);
    }


    public function getLucro()
    {
        return $this->lucro;
    }

    public function setLucro($lucro)
    {
        $this->lucro = $lucro;
    }

    public function getQtdDisponivel()
    {
        return $this->qtdDisponivel;
    }

    /* FIM MODEL */
    /* Metodos */


    public function inserir()
    {
        $dados = elements(['produtoId', 'data', 'quantidade', 'valor', 'lucro'], get_object_vars($this));

        return $this->db->insert("estoque", $dados);
    }


    public function buscar($limite = null, $offset = null)
    {
        if(is_null($limite) && is_null($offset)):
            $this->db->order_by("data", "desc");
            return $this->db->get_where('estoque', array(
                'ativo' => 's'
            ))->result('EstoqueModel');

        else:
            $this->db->order_by("data", "desc");
            return $this->db->get_where('estoque', array(
                'ativo' => 's'
            ), $limite, $offset)->result('EstoqueModel');
        endif;
    }

    public function produtosDisponiveis()
    {

        $sql = "SELECT
                  e.produtoId,
                  e.id,
                  e.data,
                  (SUM(e.quantidade) - (SELECT IF(ISNULL(SUM(quantidade)), 0, SUM(quantidade)) FROM produtobaixa WHERE produtoId = e.produtoId)) AS qtdDisponivel,
                  e.valor,
                  e.lucro,
                  e.ativo
                FROM estoque e
                WHERE e.ativo = 's'
                GROUP BY e.produtoId
                HAVING qtdDisponivel > 0";


        return $this->db->query($sql)->result('EstoqueModel');

    }

    public function buscarPorId($id)
    {
        return $this->db->get_where('estoque', array(
            'id'    => $id,
            'ativo' => 's'
        ))->row(0, 'EstoqueModel');
    }

    public function editar($dados)
    {

        $dados = elements(['id','produtoId', 'data', 'quantidade', 'valor', 'lucro'], get_object_vars($this));
        $this->db->where('id', $this->getId());
        return $this->db->update("estoque", $dados);

    }

    public function excluir($id)
    {
        $this->db->where('id', $id);
        return $this->db->update('estoque', array(
            'ativo' => 'n'
        ));
    }

}
