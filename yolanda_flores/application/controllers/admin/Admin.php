<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include "Base.php";

class Admin extends Base {

   public function index()
   {
       $this->load->model('ProdutoModel');
       $this->load->model('EstoqueModel');
       $this->load->model('BaixaModel');

       $produtos = $this->ProdutoModel->exibir();

       $estoques = $this->EstoqueModel->exibir();

       $baixas = $this->BaixaModel->exibir();

       $dados = array("produtos" => $produtos, "estoques" => $estoques,"baixas" => $baixas);

       $this->load->view('admin/index', $dados);
   }

}
