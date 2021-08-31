<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Base extends CI_Controller {

    protected $usuarioAut;

    public function __construct()
    {
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct();
        $this->verificaUsuarioLogado();
    }

    public function verificaUsuarioLogado()
    {
        if($this->session->userdata("usuarioLogado")):

            $this->load->model('UsuarioModel');

            $dados = array('tokenSenha' => $this->session->userdata("usuarioLogado"));

            $usuario = $this->UsuarioModel->buscar($dados);

            if($usuario):

                if(strtotime(date('Y-m-d')) <= strtotime($usuario->getTokenExpira())):
                    $this->usuarioAut = $usuario;
                    $this->load->vars(['usuario' => $usuario]);
                else:
                    $this->session->unset_userdata("usuarioLogado");
                    $this->session->set_flashdata("erro", "O usuário atingiu o tempo limite de conexão!");
                    redirect('/admin/');
                endif;

            else:
                $this->session->unset_userdata("usuarioLogado");
                $this->session->set_flashdata("erro", "Usuário não autenticado!");
                redirect('/admin/');
            endif;

        elseif($this->router->fetch_class() != "usuario" && $this->router->fetch_method() != "login"):
            $this->session->set_flashdata("alerta", "Acesso restrito!");
            redirect('/admin/');die;
        endif;
    }



}
