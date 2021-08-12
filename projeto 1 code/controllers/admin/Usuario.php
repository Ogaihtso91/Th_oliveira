<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include "Base.php";

class Usuario extends Base{

    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        if($this->input->post()):

            $usuario = elements(['email','senha'], $this->input->post());

            $dados = array('email' => $usuario["email"], 'senha' => $usuario["senha"]);

            $this->load->model('UsuarioModel');

            $usuario = $this->UsuarioModel->buscar($dados);

            if($usuario):

                $tokenSenha = date("YmdHis").$usuario->getIdUsuario().$usuario->getSenha();

                $tokenExpira = date("Y-m-d");

                $dados = array('tokenSenha' => $tokenSenha, 'tokenExpira' => $tokenExpira, 'idUsuario' => $usuario->getIdUsuario());

                $usuarioAtualizado = $this->UsuarioModel->atualizaToken($dados);

                if($usuarioAtualizado):

                    $this->session->set_userdata("usuarioLogado", $usuarioAtualizado->getTokenSenha());
                    redirect('/admin/inicio');

                endif;
            endif;

            $this->session->set_flashdata("erro", "Usuário ou senha inválido");

        endif;

        $this->load->view('/admin/usuario/login');
    }


    public function logout()
    {
        $this->session->unset_userdata("usuarioLogado");
        $this->session->set_flashdata("sucesso", "Usuário deslogado!");
        redirect('/admin/');
    }


}

