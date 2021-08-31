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
        $this->load->view('/admin/baixa/index');
    }

    public function inserir()
    {

        $this->load->model('estoqueModel');

        $produtos = $this->estoqueModel->produtosDisponiveis();

        $dados = array("produtos" => $produtos);

        $this->load->view('/admin/baixa/form', $dados);
    }




}

