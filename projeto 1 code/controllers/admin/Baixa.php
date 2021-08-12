<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include "Base.php";

class Baixa extends Base{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {


        $offset = ($_SERVER['QUERY_STRING'] == '') ? 0 : preg_replace("/[^0-9]/", "", $_SERVER['QUERY_STRING']);

        $limite = 8;

        $this->load->model('BaixaModel');

        $baixas = $this->BaixaModel->buscar($limite, $offset);

        $config['base_url'] = '/admin/baixa/index';
        $config['total_rows'] = count($this->BaixaModel->buscar());
        $config['per_page'] = $limite;
        //$config['use_page_numbers'] = TRUE;
        $config['page_query_string'] = true;
        $config['num_links'] = 1;
        $config['first_link'] = 'Primeiro';
        $config['last_link'] = 'Última';

        $config['full_tag_open'] = "<ul class='pagination pull-right'>";
        $config['full_tag_close'] ="</ul>";
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
        $config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
        $config['next_tag_open'] = "<li>";
        $config['next_tagl_close'] = "</li>";
        $config['prev_tag_open'] = "<li>";
        $config['prev_tagl_close'] = "</li>";
        $config['first_tag_open'] = "<li>";
        $config['first_tagl_close'] = "</li>";
        $config['last_tag_open'] = "<li>";
        $config['last_tagl_close'] = "</li>";

        $this->pagination->initialize($config);

        $dados = array("baixas" => $baixas);

        $this->load->view('/admin/baixa/index', $dados);
    }

    public function inserir()
    {
        if($this->input->post()):

            // prepara para salvar a baixa

            $baixa = elements(['usuarioId', 'data', 'valorTotal', 'tipo', 'comentario'], $this->input->post());

            $this->load->model('BaixaModel');
            $this->load->model('EstoqueModel');

            $dados = $this->BaixaModel->popula($baixa);

            if($dados):

                $idBaixa = $this->BaixaModel->inserir();

                if($idBaixa):

                    // apos salvar a baixa prepara para salvar os produtos da baixa

                    $valorTotalBaixa = 0;

                    foreach($this->input->post('produto') as $key => $produto):

                        $this->load->model('ProdutoBaixaModel');

                        $produtoBaixa = elements(['produtoId', 'baixaId', 'valor', 'quantidade'], $produto);
                        $produtoBaixa['baixaId'] = $idBaixa;

                        $dados = $this->ProdutoBaixaModel->popula($produtoBaixa);

                        if(!$dados):
                            $this->session->set_flashdata("erro", "Erro! Baixa NÃO realizada.");
                            redirect('/admin/baixa/inserir');
                        endif;

                        $resultado = $this->ProdutoBaixaModel->inserir();

                        $produtoBaixaId     = $this->db->insert_id();
                        $baixaQtdNecessario = $produto['quantidade'];
                        
                        $estoques = $this->EstoqueModel->db
                            ->query("SELECT
                                    e.produtoId,
                                    e.id,
                                    e.data,
                                    (e.quantidade - COALESCE(SUM(pbe.quantidade), '',0)) AS quantidade,
                                    e.valor,
                                    e.lucro,
                                    e.ativo
                                    FROM estoque e
                                    LEFT JOIN produtobaixaestoque pbe
                                        ON pbe.estoqueId = e.id
                                    WHERE e.produtoId = {$produto['produtoId']}
                                        AND e.ativo = 's'
                                    GROUP BY e.id
                            ORDER BY DATA ASC;")
                            ->result();

                        /*Aqui prepara o que o produto foi vendido para ser salvo no banco*/

                        $valorUnitarioVendido = str_replace(".", "", $produto['valor']);
                        $valorUnitarioVendido = str_replace(",", ".", $produto['valor']);
                        
                        foreach($estoques as $estoque) {
                            if($baixaQtdNecessario > 0) {
                                if($estoque->quantidade == $baixaQtdNecessario)  {
                                    $this->EstoqueModel->db->query("UPDATE estoque SET ativo ='n' WHERE id = {$estoque->id}");
                                    $this->EstoqueModel->db->query("INSERT INTO produtobaixaestoque (baixaId, estoqueId, valor, quantidade ) VALUES ('{$idBaixa}','{$estoque->id}' ,'{$valorUnitarioVendido}','{$baixaQtdNecessario}')");
                                    break;
                                } elseif($estoque->quantidade > $baixaQtdNecessario) {
                                    $this->EstoqueModel->db->query("INSERT INTO produtobaixaestoque (baixaId, estoqueId, valor, quantidade ) VALUES ('{$idBaixa}','{$estoque->id}' ,'{$valorUnitarioVendido}','{$baixaQtdNecessario}')");
                                    break;
                                } else {
                                    $this->EstoqueModel->db->query("INSERT INTO produtobaixaestoque (baixaId, estoqueId, valor, quantidade ) VALUES ('{$idBaixa}','{$estoque->id}' ,'{$valorUnitarioVendido}','{$estoque->quantidade}')");
                                    $this->EstoqueModel->db->query("UPDATE estoque SET ativo ='n' WHERE id = {$estoque->id}");
                                    $baixaQtdNecessario = $baixaQtdNecessario - $estoque->quantidade;
                                }
                            } else {
                                break;
                            }
                        }

                        if(!$resultado):
                            $this->session->set_flashdata("erro", "Erro! Baixa NÃO realizada.");
                            redirect('/admin/baixa/inserir');
                        endif;

                        $produtoBaixa['valor'] = str_replace(",", ".", $produtoBaixa['valor']);
                        $valorTotalBaixa += $produtoBaixa['valor'] * $produtoBaixa['quantidade'];

                    endforeach;

                    // Apos inserir todos os produtos da baixa eu vou salvar o valor total da baixa

                    if($this->BaixaModel->inserirValorTotal($idBaixa,$valorTotalBaixa)):

                        $this->session->set_flashdata("sucesso", "Baixa realizada com sucesso!");
                        redirect('/admin/baixa/index');

                    else:

                        $this->session->set_flashdata("erro", "Erro! Baixa NÃO realizada.");
                        redirect('/admin/baixa/inserir');

                    endif;

                else:
                    $this->session->set_flashdata("erro", "Erro! Baixa NÃO realizada.");
                    redirect('/admin/baixa/inserir');
                endif;
            else:
                $this->session->set_flashdata("erro", "Erro! Baixa NÃO realizada.");
                redirect('/admin/baixa/inserir');
            endif;

        endif;

        $this->load->model('estoqueModel');

        $produtos = $this->estoqueModel->produtosDisponiveis();

        $dados = array("produtos" => $produtos);

        $this->load->view('/admin/baixa/form', $dados);
    }

    public function excluir()
    {
        $id = $this->input->get('id');

        $this->load->model('BaixaModel');

        $idExcluido = $this->BaixaModel->excluir($id);

        if($idExcluido):

            $this->load->model('ProdutoBaixaModel');

            $resultado = $this->ProdutoBaixaModel->excluir($idExcluido);

            if($resultado):

                $ativar = $this->BaixaModel->PegaId($id);

                if($ativar):

                    $idExcluir = $ativar[0]->baixaId;

                /* o for abaixo ativa todos os itens de estoque vinculados a baixa que esta sendo excluida*/

                    for($i = 0; $i < count($ativar); ++$i):

                        $ativarId = $ativar[$i]->estoqueId;

                        $resultado = $this->BaixaModel->ativaEstoque($ativarId);

                    endfor;

                    if($resultado):

                        $resultado = $this->BaixaModel->excluirBaixaEstoque($idExcluir);

                        if($resultado):

                            $this->session->set_flashdata("sucesso", "Baixa excluída com sucesso!");
                            redirect('/admin/baixa/index'); die;
                        else:
                            $this->session->set_flashdata("danger", "Erro ao ativar o estoque");
                            redirect('/admin/baixa/index'); die;
                        endif;

                    else:
                        $this->session->set_flashdata("danger", "Erro ao ativar o estoque");
                        redirect('/admin/baixa/index'); die;
                    endif;

                else:
                    $this->session->set_flashdata("danger", "Erro ao atualizar o estoque");
                    redirect('/admin/baixa/index'); die;
                endif;


            else:
                $this->session->set_flashdata("danger", "Erro ao excluir a baixa!");
                redirect('/admin/baixa/index'); die;
            endif;

        endif;

        $this->session->set_flashdata("danger", "Erro ao excluir a baixa!");
        redirect('/admin/baixa/index'); die;
    }

    public function editar()
    {
        $id = $this->input->get('id');

        $this->load->model('ProdutoBaixaModel');

        $baixas = $this->ProdutoBaixaModel->buscarProdutos($id);

        $dados = array("baixas" => $baixas);

        $this->load->view('/admin/baixa/visualizador', $dados);
    }


}

