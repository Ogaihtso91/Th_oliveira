<?php $this->load->view('admin/cabecalho'); ?>

    <!-- container -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="pull-right">

                <a href="/admin/produto/inserir" class="btn btn-sm btn-primary" role="button">
                    <span class="glyphicon glyphicon-plus-sign"></span> Adicionar
                </a>
            </div>
            <h1>
                Produtos
                <small>Sistema de administração</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">

                <div class="panel panel-default">

                    <!--Colocar o conteúdo da página aqui-->
                    <div class="panel panel-default">

                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> Listagem de produtos</h3>
                        </div>

                        <table class="table table-hover">

                            <?php $this->load->view('admin/mensagem'); ?>
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nome</th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php foreach($produtos as $produto): ?>
                                    <tr>

                                        <td><?= html_escape($produto->getCodigo()) ?></td>
                                        <td><?= html_escape($produto->getNome()) ?></td>
                                        <td class="text-right">
                                            <div class="btn-group">
                                                <button class="btn btn-link btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                    <span class="glyphicon glyphicon-menu-hamburger"></span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                    <li>
                                                        <a href="/admin/produto/editar?id=<?=$produto->getId()?>">
                                                            <span class="glyphicon glyphicon-zoom-in"></span> Detalhes
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a href='/admin/produto/excluir?id=<?=$produto->getId()?>' onclick=' return confirm("Deseja realmente excluir esse Produto?")'>
                                                            <span class='glyphicon glyphicon-trash'></span> Excluir
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td colspan="1"><ul class="pagination">Total de Produtos: <?php echo count($this->ProdutoModel->buscar()); ?></ul></td>
                                    <td colspan="4" rowspan="2"><?php echo $this->pagination->create_links();?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>   
                </div>

        </section>
        <!-- /.content -->
    </div>
    <!-- Fim container -->

<?php $this->load->view('admin/rodape'); ?>