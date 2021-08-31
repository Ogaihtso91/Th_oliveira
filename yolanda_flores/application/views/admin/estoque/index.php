<?php $this->load->view('admin/cabecalho'); ?>

    <!-- container -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="pull-right">

                <a href="/admin/estoque/inserir" class="btn btn-sm btn-primary" role="button">
                    <span class="glyphicon glyphicon-plus-sign"></span> Adicionar
                </a>
            </div>
            <h1>
                Estoque
                <small>Sistema de administração</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="panel panel-default">

                <!--Colocar o conteúdo da página aqui-->
                <div class="panel panel-default">

                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> Listagem de estoque</h3>
                    </div>

                    <table class="table table-hover">

                        <?php $this->load->view('admin/mensagem'); ?>
                        <thead>
                        <tr>
                            <th>Código</th>
                            <th>Produto</th>
                            <th>Data</th>
                            <th>Quantidade</th>
                            <th>Valor compra</th>
                            <th>Lucro</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php foreach($estoques as $estoque): ?>
                            <tr>
                                <td><?= html_escape($estoque->getProduto()->getCodigo()) ?></td>
                                <td><?= html_escape($estoque->getProduto()->getNome()) ?></td>
                                <td><?= html_escape($estoque->getData()) ?></td>
                                <td><?= html_escape($estoque->getQuantidade()) ?></td>
                                <td>R$ <?= html_escape($estoque->getValor()) ?></td>
                                <td><?= html_escape($estoque->getLucro()) ?>%</td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <button class="btn btn-link btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                            <span class="glyphicon glyphicon-menu-hamburger"></span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            <li>
                                                <a href="/admin/estoque/editar?id=<?=$estoque->getId()?>">
                                                    <span class="glyphicon glyphicon-zoom-in"></span> Detalhes
                                                </a>
                                            </li>

                                            <li>
                                                <a href='/admin/estoque/excluir?id=<?=$estoque->getId()?>' onclick=' return confirm("Deseja realmente excluir esse Produto do estoque?")'>
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
                            <td colspan="4"><ul class="pagination">Total de Produtos: <?php echo count($this->EstoqueModel->buscar()); ?></ul></td>
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