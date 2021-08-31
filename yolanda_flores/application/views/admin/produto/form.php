<?php $this->load->view('admin/cabecalho'); ?>

    <!-- container -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Produtos
                <small>Sistema de administração</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">

                <div class="panel panel-default">

                    <div class="panel-heading">

                        <?php if( empty($produto->getId())): ?>
                            <h3 class="panel-title"><i class="glyphicon glyphicon-plus"></i> Cadastro</h3>
                        <?php else: ?>
                            <h3 class="panel-title"><i class="glyphicon glyphicon-plus"></i> Edição</h3>
                        <?php endif; ?>

                    </div>

                    <?php $this->load->view('admin/mensagem'); ?>

                    <!--Colocar o conteúdo da página aqui-->

                    <form role="form" method="POST" action="/admin/produto/inserir">

                        <div class="box-body">

                            <input type="hidden" value="<?=@$produto->getId()?>" name="id">

                            <div class="form-group">
                                <label>Código de identificação</label>
                                <input type="text" value="<?=@$produto->getCodigo()?>" class="form-control" name="codigo" placeholder="Insira o código do produto" required>
                            </div>

                            <div class="form-group">
                                <label>Nome do produto</label>
                                <input type="text" value="<?=@$produto->getNome()?>" class="form-control" name="nome" placeholder="Escolha o nome do produto" required>
                            </div>

                            <div class="box-footer">
                                <div class="col-sm-1">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <span class="glyphicon glyphicon-floppy-disk"></span> Cadastrar
                                    </button>
                                </div>

                                <div class="col-sm-1">
                                    <a href="/admin/produto" class="btn btn-sm btn-primary">
                                        <span class="glyphicon glyphicon-log-out"></span> Voltar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>

        </section>
        <!-- /.content -->
    </div>
    <!-- Fim container -->

<?php $this->load->view('admin/rodape'); ?>