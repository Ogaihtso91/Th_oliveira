<?php $this->load->view('admin/cabecalho'); ?>

    <!-- container -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Baixa
                <small>Sistema de administração</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="panel panel-default">

                <!--Colocar o conteúdo da página aqui-->
                <div class="panel panel-default">

                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> Visualização de produtos da baixas</h3>
                    </div>

                    <table class="table table-striped">

                        <?php $this->load->view('admin/mensagem'); ?>
                        <thead>
                        <tr>
                            <th>Código</th>
                            <th>Produto</th>
                            <th>Valor</th>
                            <th>Quantidade</th>
                            <th>Total por unidade</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php foreach($baixas as $baixa): ?>
                            <tr>
                                <td><?= html_escape($baixa->getProduto()->getCodigo()) ?></td>
                                <td><?= html_escape($baixa->getProduto()->getNome()) ?></td>
                                <td>R$ <?= html_escape($baixa->getValor()) ?></td>
                                <td><?= html_escape($baixa->getQuantidade()) ?></td>
                                <td>R$ <?= html_escape($baixa->getValorTotalPorUnidade()) ?></td>

                            </tr>
                        <?php  endforeach; ?>
                        </tbody>

                        <tfoot>
                        <tr>
                            <td colspan="1"><ul class="pagination">Total de Produtos: <?php echo count($this->ProdutoBaixaModel->buscarProdutos($baixa->getBaixaId())); ?></ul></td>
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