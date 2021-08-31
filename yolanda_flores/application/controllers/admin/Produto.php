<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include "Base.php";

class Produto extends Base{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $offset = ($_SERVER['QUERY_STRING'] == '') ? 0 : preg_replace("/[^0-9]/", "", $_SERVER['QUERY_STRING']);

        $limite = 8;

        $this->load->model('ProdutoModel');

        $produtos = $this->ProdutoModel->buscar($limite, $offset);

        $config['base_url'] = '/admin/produto/index';
        $config['total_rows'] = count($this->ProdutoModel->buscar());
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

        $dados = array("produtos" => $produtos);

        $this->load->view('/admin/produto/index', $dados);

    }

    public function inserir()
    {
        if($this->input->post()):

            if($this->input->post('id') == NULL): // Cadastro

       		    $this->load->model('ProdutoModel');

                $dados = $this->ProdutoModel->popula($this->input->post());

                if($dados):
                    $existe = $this->ProdutoModel->verificaSeExiste($dados->getCodigo());

                    if($existe):
                        $this->session->set_flashdata("erro", "Código já cadastrado!");
                        redirect('/admin/produto/inserir');
                    else:
                        $resultado = $this->ProdutoModel->inserir();

                        if($resultado):
                            $this->session->set_flashdata("sucesso", "Produto cadastrado com sucesso!");
                            redirect('/admin/produto/index');
                        else:
                            $this->session->set_flashdata("erro", "Erro ao cadatrar o produto!");
                            redirect('/admin/produto/inserir');
                        endif;
                    endif;
                else:
                    $this->session->set_flashdata("erro", "Erro ao cadatrar o produto!");
                    redirect('/admin/produto/inserir');
                endif;

            else: // Edição

                $this->load->model('ProdutoModel');

                $dados = $this->ProdutoModel->popula($this->input->post());

                if($dados):

                    $resultado = $this->ProdutoModel->editar($dados);

                    if($resultado):
                        $this->session->set_flashdata("sucesso", "Produto editado com sucesso!");
                        redirect('/admin/produto/index');
                    else:
                        $this->session->set_flashdata("erro", "Erro ao editar o produto!");
                        redirect('/admin/produto/index');
                    endif;

                else:
                    $this->session->set_flashdata("erro", "Erro ao editar o produto!");
                    redirect('/admin/produto/index');
                endif;

            endif;
        endif;

        $this->load->model('ProdutoModel');

        $produto = new ProdutoModel();

        $dados = array("produto" => $produto);

        $this->load->view('/admin/produto/form', $dados);
    }

    public function editar()
    {
        $id = $this->input->get('id');

        $this->load->model('ProdutoModel');

        $produto = $this->ProdutoModel->buscarPorId($id);

        $dados = array("produto" => $produto);

        $this->load->view('/admin/produto/form', $dados);

    }

    public function excluir()
    {
        $id = $this->input->get('id');

        $this->load->model('ProdutoModel');

        $resultado = $this->ProdutoModel->excluir($id);

        if($resultado):
            $this->session->set_flashdata("sucesso", "Produto excluído com sucesso!");
            redirect('/admin/produto/index'); die;
        endif;

        $this->session->set_flashdata("danger", "Erro ao excluir produto!");
        redirect('/admin/produto/index'); die;
    }



}

