<?php

class BaixaModel extends BaseModel
{

    private $id;
    private $usuarioId;
    private $data;
    private $valorTotal;
    private $tipo;
    private $comentario;
    private $ativo;


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }


    public function getUsuarioId()
    {
        return $this->usuarioId;
    }

    public function setUsuarioId($usuarioId)
    {
        $this->usuarioId = $usuarioId;
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


    public function getValorTotal()
    {
        $this->valorTotal = number_format($this->valorTotal, 2, ',', '.');
        return $this->valorTotal;

    }

    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = str_replace(".", "", $valorTotal);
        $this->valorTotal = str_replace(",", ".", $valorTotal);
    }


    public function getTipo()
    {
        return $this->tipo;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }


    public function getComentario()
    {
        return $this->comentario;
    }

    public function setComentario($comentario)
    {
        $this->comentario = $comentario;
    }


    public function getAtivo()
    {
        return $this->ativo;
    }

    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }



    /*Metodos*/


    public function inserir()
    {
        $this->data = date('Y-m-d');

        $this->valorTotal = 0;

        $dados = elements(['usuarioId', 'data', 'valorTotal', 'tipo', 'comentario'], get_object_vars($this));

        if($this->db->insert("baixa", $dados)):
            return $this->db->insert_id(); // Retorna o id da baixa para que eu possa colocá-lo na tabela de baixa produto
        else:
            return false;
        endif;
    }

    public function inserirValorTotal($id,$valorTotal)
    {
        $this->db->where('id', $id);
        return $this->db->update('baixa', array(
            'valorTotal' => $valorTotal
        ));
    }

    public function buscar($limite = null, $offset = null)
    {
        if(is_null($limite) && is_null($offset)):
            $this->db->order_by("data", "desc");
            return $this->db->get_where('baixa', array(
                'tipo' => 'v', // retirar isso caso eles nao se importem da quabra aparecer A quebra aparecer reflete no valor do produto que teve a quebra  + a porcentagem de lucro por isso nao mostro
                'ativo' => 's'
            ))->result('BaixaModel');

        else:
            $this->db->order_by("data", "desc");
            return $this->db->get_where('baixa', array(
                'tipo' => 'v', // retirar isso caso eles nao se importem da quabra aparecer  A quebra aparecer reflete no valor do produto que teve a quebra  + a porcentagem de lucro por isso nao mostro
                'ativo' => 's'
            ), $limite, $offset)->result('BaixaModel');
        endif;
    }

    public function exibir()
    {
        $this->db->order_by("id", "desc");
        $this->db->limit(7);
        return $this->db->get_where('baixa', array(
            'ativo' => 's'
        ))->result('BaixaModel');
    }

    public function excluir($id)
    {
        $this->db->where('id', $id);
        $result = $this->db->update('baixa', array(
            'ativo' => 'n'
        ));

        if($result):
            return $id;
        else:
            return false;
        endif;
    }

    public function pegaId($id)
    {
        return $this->db->get_where('produtobaixaestoque', array(
            'baixaId' => $id
        ))->result();

    }

    public function ativaEstoque($id)
    {
        $this->db->where('id', $id);
        return $this->db->update('estoque', array(
            'ativo' => 's'
        ));
    }

    public function excluirBaixaEstoque($id)
    {
        $this->db->where('baixaId', $id);
        return $this->db->delete('produtobaixaestoque');
    }

    public function totalBaixas($dt)
    {
        $sql = "SELECT *
                    FROM (SELECT
                            p.nome,
                            p.codigo       AS produto,
                            b.data         AS dataVenda,
                            pbe.valor      AS valorCobrado,
                            pbe.quantidade,
                            e.valor        AS valorEstoque,
                            TRUNCATE(((pbe.valor - e.valor) * pbe.quantidade),2) AS lucro
                          FROM baixa b
                            LEFT JOIN produtobaixaestoque pbe
                              ON pbe.baixaId = b.id
                            LEFT JOIN estoque e
                              ON e.id = pbe.estoqueId
                            LEFT JOIN produto p
                              ON e.produtoId = p.id
                          WHERE b.tipo = 'v'
                              AND b.ativo = 's'
                          ORDER BY p.nome DESC) relatorio
                    WHERE dataVenda BETWEEN '".$dt['dtInicio']."' AND '".$dt['dtFim']."'
                    ORDER BY nome, dataVenda ASC;";

        $result = $this->db->query($sql)->result();

        return $result;
    }

    public function totalQuebra($dt)
    {
        $sql = "SELECT *
                    FROM (SELECT
                        p.nome,
                        p.codigo       AS produto,
                        b.data         AS dataVenda,
                        pbe.quantidade,
                        e.valor        AS valorEstoque,
                        b.comentario
                      FROM baixa b
                        LEFT JOIN produtobaixaestoque pbe
                          ON pbe.baixaId = b.id
                        LEFT JOIN estoque e
                          ON e.id = pbe.estoqueId
                        LEFT JOIN produto p
                          ON e.produtoId = p.id
                      WHERE b.tipo = 'q'
                          AND b.ativo = 's') relatorio
                    WHERE dataVenda BETWEEN '".$dt['dtInicio']."' AND '".$dt['dtFim']."'
                    ORDER BY nome, dataVenda ASC;";

        $result = $this->db->query($sql)->result();

        return $result;
    }

    public function totalAgregado()
    {
        $sql = "SELECT
                  produtoId,
                  nome,
                  TRUNCATE(valorComLucro,2) AS valorComLucro,
                  SUM(qtdDisponivel) as quantidade,
                  TRUNCATE(SUM(valorComLucro * qtdDisponivel),2) AS valorTotal
                FROM (SELECT
                        e.id,
                        e.produtoId,
                        p.nome,
                        e.quantidade,
                        TRUNCATE((SELECT MAX(valor * (1 + lucro / 100 ) ) FROM estoque WHERE estoque.ativo = 's' AND produtoId = e.produtoId),2) AS valorComLucro,
                        (e.quantidade - (SELECT COALESCE(SUM(quantidade),0) FROM produtobaixaestoque WHERE estoqueId = e.id)) AS qtdDisponivel
                      FROM estoque e
                        LEFT JOIN produto p
                          ON p.id = e.produtoId
                      WHERE e.ativo = 's'
                      ORDER BY produtoId) AS prds
                GROUP BY produtoId;";

        $result = $this->db->query($sql)->result();

        return $result;
    }

}
