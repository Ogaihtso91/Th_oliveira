<?php $this->load->view('admin/cabecalho'); ?>

    <!-- container -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Visualização de Relatório por período
                <small>Sistema de administração</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="panel panel-default">

                <div class="panel-heading">

                    <h3 class="panel-title"><i class="glyphicon glyphicon-plus"></i> Período Selecionado: <?php echo date('d/m/Y', strtotime($dt['dtInicio'])) ?> à <?php echo date('d/m/Y', strtotime($dt['dtFim'])) ?></h3>

                </div>

                <!--Colocar o conteúdo da página aqui-->
<?php $totalGeral = 0; ?>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Data</th>
                        <th>Produto</th>
                        <th>Valor do Produto</th>
                        <th>Valor Vendido</th>
                        <th>Quantidade</th>
                        <th>Lucro líquido</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($total as $total): ?>
                    <tr>
                        <td><?= html_escape(date('d/m/Y', strtotime($total->dataVenda))) ?></td>
                        <td><?= html_escape($total->produto) ?></td>
                        <td>R$ <?= html_escape(number_format($total->valorEstoque, 2, ',', '.')) ?></td>
                        <td>R$ <?= html_escape(number_format($total->valorCobrado, 2, ',', '.')) ?></td>
                        <td><?= html_escape($total->quantidade) ?></td>
                        <td>R$ <?= html_escape(number_format($total->lucro, 2, ',', '.')) ?></td>
                    </tr>
                    <?php    $totalGeral = $totalGeral + $total->lucro;
                     endforeach; ?>
                    </tbody>
                    <tfooter>
                        <tr>
                            <td colspan="6" class="danger"><?php echo "Lucro Líquido Total  R$". number_format($totalGeral, 2, ',', '.'); ?></td>
                        </tr>
                    </tfooter>

                </table>

            </div>

        </section>
        <!-- /.content -->
    </div>
    <!-- Fim container -->

<?php $this->load->view('admin/rodape'); ?>