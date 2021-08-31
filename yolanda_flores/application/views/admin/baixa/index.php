<?php $this->load->view('admin/cabecalho'); ?>

    <!-- container -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="pull-right">

                <a href="/admin/baixa/inserir" class="btn btn-sm btn-primary" role="button">
                    <span class="glyphicon glyphicon-plus-sign"></span> Adicionar
                </a>
            </div>
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
                        <h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> Listagem de baixas</h3>
                    </div>

                    <table class="table table-hover">

                        <?php $this->load->view('admin/mensagem'); ?>
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

                                <td><?= html_escape($baixa->getData()) ?></td>
                                <td>R$ <?= html_escape($baixa->getValorTotal()) ?></td>

                                <td><?php
                                    if($baixa->getTipo() == 'v'):
                                        echo "Venda";
                                    else:
                                        echo "Quebra";
                                    endif;

                                 ?></td>

                                <td><?php

                                    if($baixa->getComentario() != ""):
                                       echo html_escape($baixa->getComentario());
                                    else:
                                        echo "-";
                                    endif;

                                ?></td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <button class="btn btn-link btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                            <span class="glyphicon glyphicon-menu-hamburger"></span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            <li>
                                                <a href="/admin/baixa/editar?id=<?=$baixa->getId()?>">
                                                    <span class="glyphicon glyphicon-zoom-in"></span> Detalhes
                                                </a>
                                            </li>

                                            <li>
                                                <a href='/admin/baixa/excluir?id=<?=$baixa->getId()?>' onclick=' return confirm("Deseja realmente excluir essa Baixa?")'>
                                                    <span class='glyphicon glyphicon-trash'></span> Excluir
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php  endforeach; ?>
                        </tbody>

                        <tfoot>
                        <tr>
                            <td colspan="1"><ul class="pagination">Total de Baixas: <?php echo count($this->BaixaModel->buscar()); ?></ul></td>
                            <td colspan="4" rowspan="2"><?php  echo $this->pagination->create_links();?></td>
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