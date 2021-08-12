<?php

include "Base.php";

class Relatorio extends Base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function periodo()
    {

        if($this->input->post()):

            $dt = elements(['dtInicio', 'dtFim'], $this->input->post());

            $dt['dtInicio'] = str_replace("/", "-", $dt['dtInicio']);
            $dt['dtInicio'] = date('Y-m-d', strtotime($dt['dtInicio']));

            $dt['dtFim'] = str_replace("/", "-", $dt['dtFim']);
            $dt['dtFim'] = date('Y-m-d', strtotime($dt['dtFim']));

            $this->load->model('BaixaModel');

            $total = $this->BaixaModel->totalBaixas($dt);

            $dados = array("total" => $total, "dt" => $dt);

            $this->load->view('/admin/relatorio/viewPeriodo', $dados);

        else:
            $this->load->view('/admin/relatorio/periodo');
        endif;

    }

    public function quebra()
    {

        if($this->input->post()):

            $dt = elements(['dtInicio', 'dtFim'], $this->input->post());

            $dt['dtInicio'] = str_replace("/", "-", $dt['dtInicio']);
            $dt['dtInicio'] = date('Y-m-d', strtotime($dt['dtInicio']));

            $dt['dtFim'] = str_replace("/", "-", $dt['dtFim']);
            $dt['dtFim'] = date('Y-m-d', strtotime($dt['dtFim']));

            $this->load->model('BaixaModel');

            $total = $this->BaixaModel->totalQuebra($dt);

            $dados = array("total" => $total, "dt" => $dt);

            $this->load->view('/admin/relatorio/viewQuebra', $dados);

        else:
            $this->load->view('/admin/relatorio/quebra');
        endif;

    }

    public function agregado()
    {
        $this->load->model('BaixaModel');

        $total = $this->BaixaModel->totalAgregado();

        $dados = array("total" => $total);

        $this->load->view('/admin/relatorio/viewAgregado', $dados);

    }


}