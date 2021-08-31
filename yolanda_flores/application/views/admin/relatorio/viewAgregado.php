<?php $this->load->view('admin/cabecalho'); ?>

    <!-- container -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Visualização de Relatório Agregado
                <small>Sistema de administração</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="panel panel-default">

                <div class="panel-heading">

                    <h3 class="panel-title"><i class="glyphicon glyphicon-plus"></i> Relatório emitido em <?php echo date('d/m/Y'); ?> </h3>

                </div>

                <!--Colocar o conteúdo da página aqui-->
                <?php $totalGeral = 0; ?>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Valor Sugerido</th>
                        <th>Quantidade</th>
                        <th>Estimativa Total</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php foreach($total as $total): ?>
                    <tr>
                        <td><?= html_escape($total->nome) ?></td>
                        <td>R$ <?= html_escape(number_format($total->valorComLucro, 2, ',', '.')) ?></td>
                        <td><?= html_escape($total->quantidade) ?></td>
                        <td>R$ <?= html_escape(number_format($total->valorTotal, 2, ',', '.')) ?></td>
                    </tr>
                        <?php    $totalGeral = $totalGeral + $total->valorTotal;
                    endforeach; ?>
                    </tbody>
                    <tfooter>
                        <tr>
                            <td colspan="6" class="danger"><?php echo "Estimativa do lucro total  R$". number_format($totalGeral, 2, ',', '.'); ?></td>
                        </tr>
                    </tfooter>
                </table>

            </div>

        </section>
        <!-- /.content -->
    </div>
    <!-- Fim container -->

<?php $this->load->view('admin/rodape'); ?>