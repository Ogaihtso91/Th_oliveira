<?php $this->load->view('admin/cabecalho'); ?>

    <!-- container -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Relatório
                <small>Sistema de administração</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="panel panel-default">

                <div class="panel-heading">

                    <h3 class="panel-title"><i class="glyphicon glyphicon-plus"></i> Por período</h3>

                </div>

                <?php $this->load->view('admin/mensagem'); ?>

                <!--Colocar o conteúdo da página aqui-->

                <form role="form" method="POST" action="/admin/relatorio/periodo">

                    <div class="box-body">

                        <div class="form-group">
                            <label>Data Inicio</label>
                            <input type="text" value="" class="form-control" id="data" name="dtInicio" placeholder="00/00/0000" required>
                        </div>

                        <div class="form-group">
                            <label>Data Fim</label>
                            <input type="text" value="" class="form-control" id="dataa" name="dtFim" placeholder="00/00/0000" required>
                        </div>


                        <div class="box-footer">
                            <div class="col-sm-1">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <span class="glyphicon glyphicon-search"></span> Buscar
                                </button>
                            </div>

                            <div class="col-sm-1">
                                <a href="/admin/inicio" class="btn btn-sm btn-primary">
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