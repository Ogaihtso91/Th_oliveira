<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include "Base.php";

class Estoque extends Base{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $offset = ($_SERVER['QUERY_STRING'] == '') ? 0 : preg_replace("/[^0-9]/", "", $_SERVER['QUERY_STRING']);

        $limite = 8;

        $this->load->model('EstoqueModel');

        $estoques = $this->EstoqueModel->buscar($limite, $offset);

        $config['base_url'] = '/admin/estoque/index';
        $config['total_rows'] = count($this->EstoqueModel->buscar());
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

        $dados = array("estoques" => $estoques);

        $this->load->view('/admin/estoque/index', $dados);

    }

    public function inserir()
    {
        if($this->input->post()):

            if($this->input->post('id') == NULL): // Cadastro

                $this->load->model('EstoqueModel');

                $dados = $this->EstoqueModel->popula($this->input->post());

                if($dados):

                    $resultado = $this->EstoqueModel->inserir();

                    if($resultado):
                        $this->session->set_flashdata("sucesso", "Produto inserido no estoque!");
                        redirect('/admin/estoque/index');
                    else:
                        $this->session->set_flashdata("erro", "Erro! Produto NÃO inserido no estoque.");
                        redirect('/admin/estoque/inserir');
                    endif;
                else:
                    $this->session->set_flashdata("erro", "Erro! Produto NÃO inserido no estoque.");
                    redirect('/admin/estoque/inserir');
                endif;

            else: // Edição

                $this->load->model('EstoqueModel');

                $dados = $this->EstoqueModel->popula($this->input->post());

                if($dados):

                    $resultado = $this->EstoqueModel->editar($dados);

                    if($resultado):
                        $this->session->set_flashdata("sucesso", "Produto do estoque editado com sucesso!");
                        redirect('/admin/estoque/index');
                    else:
                        $this->session->set_flashdata("erro", "Erro ao editar produto do estoque!");
                        redirect('/admin/estoque/index');
                    endif;

                else:
                    $this->session->set_flashdata("erro", "Erro ao editar produt do estoque!");
                    redirect('/admin/estoque/index');
                endif;

            endif;

        endif;

        $this->load->model('ProdutoModel');

        $produtos = $this->ProdutoModel->buscar();

        $this->load->model('EstoqueModel');

        $estoque = new EstoqueModel();

        $dados = array("estoque" => $estoque, "produtos" => $produtos);

        $this->load->view('/admin/estoque/form', $dados);
    }


    public function editar()
    {
        $id = $this->input->get('id');

        $this->load->model('ProdutoModel');

        $produtos = $this->ProdutoModel->buscar();

        $this->load->model('EstoqueModel');

        $estoque = $this->EstoqueModel->buscarPorId($id);

        $dados = array("estoque" => $estoque, "produtos" => $produtos);

        $this->load->view('/admin/estoque/form', $dados);

    }

    public function excluir()
    {
        $id = $this->input->get('id');

        $this->load->model('EstoqueModel');

        $resultado = $this->EstoqueModel->excluir($id);

        if($resultado):
            $this->session->set_flashdata("sucesso", "Produto excluído do estoque com sucesso!");
            redirect('/admin/estoque/index'); die;
        endif;

        $this->session->set_flashdata("danger", "Erro ao excluir produto do estoque!");
        redirect('/admin/estoque/index'); die;
    }


}

