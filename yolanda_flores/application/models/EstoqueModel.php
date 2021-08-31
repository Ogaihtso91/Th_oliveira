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

    public function getValorAjustado()
    {
        $valor = $this->getValor() + ($this->getValor() * ($this->getLucro()/100));
        $valor = number_format((float) $valor, 2, ',', '.');
        return $valor;
    }


    public function getValor()
    {
        $value = (float) str_replace(',','.', $this->valor);
        $this->valor = number_format($value, 2, ',', '.');
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

    public function produtosDisponiveis($limite = null, $offset = null)
    {

        $sql = "SELECT
  e.produtoId,
  e.id,
  e.data,
  SUM(e.quantidade),
  (SUM(e.quantidade) - COALESCE((SELECT SUM(pbe.quantidade) FROM produtobaixaestoque pbe LEFT JOIN estoque e1 ON e1.id = pbe.estoqueId WHERE e1.ativo = 's' AND e1.produtoId = e.produtoId GROUP BY e1.produtoId),0)) AS qtdDisponivel,
  (SELECT
     TRUNCATE(valor,2)
   FROM estoque
   WHERE produtoId = e.produtoid
       AND ativo = 's'
   ORDER BY valor DESC
   LIMIT 1) AS valor,
  (SELECT
     lucro
   FROM estoque
   WHERE produtoId = e.produtoid
       AND ativo = 's'
   ORDER BY valor DESC
   LIMIT 1) AS lucro,
  e.ativo
FROM estoque e
  LEFT JOIN produto p
    ON e.produtoId = p.id
WHERE e.ativo = 's'
GROUP BY e.produtoId
HAVING qtdDisponivel > 0
ORDER BY e.valor DESC";


        $result = $this->db->query($sql)->result('EstoqueModel');

        return $result;

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

    public function exibir()
    {
        $this->db->order_by("id", "desc");
        $this->db->limit(7);
        return $this->db->get_where('estoque')->result('EstoqueModel');
    }

    public function excluir($id)
    {
        $this->db->where('id', $id);
        return $this->db->update('estoque', array(
            'ativo' => 'n'
        ));
    }



}
