<?php $this->load->view('admin/cabecalho'); ?>

    <!-- container -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Início
                <small>Sistema de administração</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">

                <div class="panel panel-default">

                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="glyphicon glyphicon-plus"></i> Últimas informações</h3>
                    </div>

                    <?php $this->load->view('admin/mensagem'); ?>

                    <!--Colocar o conteúdo da página aqui-->

                    <!--Box 1-->
                    <div class="row">
                        <section class="content col-md-4">

                            <div class="panel panel-default">

                                <div class="panel-heading">

                                    <h3 class="panel-title"><a href="/admin/produto"><i class="glyphicon glyphicon-asterisk"></i> Produtos cadastrados</a></h3>

                                </div>

                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Produto</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php foreach($produtos as $produto): ?>

                                    <tr>
                                        <td><a href="/admin/produto/editar?id=<?=$produto->getId()?>"><?= html_escape($produto->getCodigo()) ?></a></td>
                                        <td><a href="/admin/produto/editar?id=<?=$produto->getId()?>"><?= html_escape($produto->getNome()) ?></a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                        <!--Fim box 1-->

                        <!--Box 2-->
                        <section class="content col-md-4">

                            <div class="panel panel-default">

                                <div class="panel-heading">

                                    <h3 class="panel-title"><a href="/admin/estoque"><i class="glyphicon glyphicon-asterisk"></i> Estoques cadastrados</a></h3>

                                </div>

                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Produto</th>
                                        <th>Valor</th>
                                        <th>Quantidade</th>
                                        <th>Lucro</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php foreach($estoques as $estoque): ?>

                                    <tr>
                                        <td><a href="/admin/estoque/editar?id=<?=$estoque->getId()?>"><?= html_escape($estoque->getData()) ?></a></td>
                                        <td><a href="/admin/estoque/editar?id=<?=$estoque->getId()?>"><?= html_escape($estoque->getProduto()->getNome()) ?></a></td>
                                        <td><a href="/admin/estoque/editar?id=<?=$estoque->getId()?>">R$ <?= html_escape($estoque->getValor()) ?></a></td>
                                        <td><a href="/admin/estoque/editar?id=<?=$estoque->getId()?>"><?= html_escape($estoque->getQuantidade()) ?></a></td>
                                        <td><a href="/admin/estoque/editar?id=<?=$estoque->getId()?>"><?= html_escape($estoque->getLucro()) ?>%</a></td>
                                    </tr>

                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                        <!--Fim box 2-->

                        <!--Box 3-->
                        <section class="content col-md-4">

                            <div class="panel panel-default">

                                <div class="panel-heading">

                                    <h3 class="panel-title"><a href="/admin/baixa"><i class="glyphicon glyphicon-asterisk"></i> Baixas realizadas</a></h3>

                                </div>

                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Valor Total</th>
                                        <th>Tipo</th>
                                        <th>Comentário</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php foreach($baixas as $baixa):  ?>

                                    <tr>
                                        <td><a href="/admin/baixa/editar?id=<?=$baixa->getId()?>"><?= html_escape($baixa->getData()) ?></a></td>
                                        <td><a href="/admin/baixa/editar?id=<?=$baixa->getId()?>">R$ <?= html_escape($baixa->getValorTotal()) ?></a></td>
                                        <td><a href="/admin/baixa/editar?id=<?=$baixa->getId()?>"><?php if($baixa->getTipo() == 'v'): echo "Venda"; else: echo "Quebra"; endif; ?></a></td>
                                        <td><a href="/admin/baixa/editar?id=<?=$baixa->getId()?>"><?php if($baixa->getComentario() != ""): echo html_escape($baixa->getComentario()); else: echo "-"; endif; ?></a></td>
                                    </tr>

                                    <?php  endforeach; ?>

                                    </tbody>
                                </table>
                            </div>
                        </section>
                        <!--Fim box 3-->

                    </div>
                </div>
        </section>

        <!-- /.content -->
    </div>
    <!-- Fim container -->

<?php $this->load->view('admin/rodape'); ?>