<?php

class UsuarioModel extends BaseModel
{
    private $idUsuario;
    private $criadoEm;
    private $criadoPor;
    private $modificadoEm;
    private $modificadoPor;
    private $perfil;
    private $nome;
    private $email;
    private $senha;
    private $tokenSenha;
    private $tokenExpira;
    private $ativo;


    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
    }


    public function getCriadoEm()
    {
        return $this->criadoEm;
    }

    public function setCriadoEm($criadoEm)
    {
        $this->criadoEm = $criadoEm;
    }


    public function getCriadoPor()
    {
        return $this->criadoPor;
    }

    public function setCriadoPor($criadoPor)
    {
        $this->criadoPor = $criadoPor;
    }


    public function getModificadoEm()
    {
        return $this->modificadoEm;
    }

    public function setModificadoEm($modificadoEm)
    {
        $this->modificadoEm = $modificadoEm;
    }


    public function getModificadoPor()
    {
        return $this->modificadoPor;
    }

    public function setModificadoPor($modificadoPor)
    {
        $this->modificadoPor = $modificadoPor;
    }


    public function getPerfil()
    {
        return $this->perfil;
    }

    public function setPerfil($perfil)
    {
        $this->perfil = $perfil;
    }


    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }


    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }


    public function getSenha()
    {
        return $this->senha;
    }

    public function setSenha($senha)
    {
        $this->senha = $senha;
    }


    public function getTokenSenha()
    {
        return $this->tokenSenha;
    }

    public function setTokenSenha($tokenSenha)
    {
        $this->tokenSenha = $tokenSenha;
    }


    public function getTokenExpira()
    {
        return $this->tokenExpira;
    }

    public function setTokenExpira($tokenExpira)
    {
        $this->tokenExpira = $tokenExpira;
    }


    public function getAtivo()
    {
        return $this->ativo;
    }

    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }



    public function buscar($dados)
    {
        if(isset($dados['email']) && isset($dados['senha'])):

            $dados["senha"] = md5($dados["senha"]);

            $usuario = $this->db->get_where('usuario', array(
                'ativo' => "sim",
                'email' => $dados["email"],
                'senha' => $dados["senha"]
            ))->row(0, 'UsuarioModel');

            return $usuario;

        elseif(isset($dados['idUsuario'])):

            $usuario = $this->db->get_where('usuario', array(
                'ativo' => "sim",
                'idUsuario' => $dados["idUsuario"]
            ))->row(0, 'UsuarioModel');

            return $usuario;

        elseif(isset($dados['tokenSenha'])):

            $usuario = $this->db->get_where('usuario', array(
                'ativo' => "sim",
                'tokenSenha' => $dados["tokenSenha"]
            ))->row(0, 'UsuarioModel');

            return $usuario;

        else:

            $usuarios = $this->db->get_where('usuario', array(
                'ativo' => "sim"
            ))->result('UsuarioModel');

            return $usuarios;

        endif;
    }


    public function inserir($dados)
    {
        if($this->db->insert("usuario", $dados)):
            return true;
        else:
            return false;
        endif;

    }

    public function atualizaToken($dados)
    {
        if(isset($dados['tokenSenha']) && isset($dados['tokenExpira']) && isset($dados['idUsuario'])):

            $this->db->where('idUsuario', $dados['idUsuario']);

            $resultado = $this->db->update('usuario', $dados);

            if($resultado):

                $usuarioAtualizado = $this->buscar($dados);

                if($usuarioAtualizado):
                    return $usuarioAtualizado;
                endif;
                    return false;
            endif;

        endif;
    }

}
