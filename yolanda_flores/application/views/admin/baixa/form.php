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

                <div class="panel-heading">


                        <h3 class="panel-title"><i class="glyphicon glyphicon-plus"></i> Cadastro</h3>



                </div>

                <?php $this->load->view('admin/mensagem'); ?>

                <!--Colocar o conteúdo da página aqui-->

                <form role="form" method="POST" action="/admin/baixa/inserir">

                    <div class="box-body">

                        <input type="hidden" value="<?php echo $usuario->getIdUsuario(); ?>" name="usuarioId">

                        <label>Tipo de Baixa</label>
                        <div class="form-group">
                            <label class="radio-inline">
                                <input type="radio" checked name="tipo" value="v">Venda
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="tipo" value="q">Quebra
                            </label>
                        </div>

                        <div class="form-group">
                            <label>Comentário</label>
                            <textarea class="form-control" rows="5" name="comentario"></textarea>
                        </div>

                        <!--Começa a repetir para adicionar-->


                        <button type="button" id="btn-add" class="btn btn-primary btn-sm btn-add-subservice"><i class="glyphicon glyphicon-plus"></i> Adicionar Produto</button>
                        <div id="produtos">
                            <div class="produto-item form-group well col-lg-12" id="ProdutoModel">
                                <input type="hidden" name="produto[:index:][acao]" class="removeAction" value="A">

                                <div class="col-lg-3">
                                    <label>Produto</label>
                                    <label class="input-inline">
                                        <select class="input-inline form-control produto-estoque" name="produto[:index:][produtoId]"  data-produto-index=":index:">
                                            <?php foreach($produtos as $key => $produto): ?>
                                            <option value="<?= $produto->getProduto()->getId(); ?>" data-valor="<?= $produto->getValorAjustado(); ?>" data-qtd="<?= $produto->getQtdDisponivel() ?>"><?= $produto->getProduto()->getNome(); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                </div>
                                <div class="col-lg-3">

                                    <label>Valor</label>
                                    <label class="input-inline" >
                                        <input type="text" value="" class="form-control valor" name="produto[:index:][valor]"  required onKeyPress="return(MascaraMoeda(this,'.',',',event))">
                                    </label>
                                </div>
                                <div class="col-lg-2">
                                    <label>Quantidade</label>
                                    <label class="input-inline">
                                        <select class="form-control quantidade" name="produto[:index:][quantidade]">
                                            <option value="Quantidade"> </option>
                                            <?php for($i = 0; $i <= 0; $i++): ?>
                                                <option value="<?= $i  ?>"><?php echo $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </label>
                                </div>
                                <div class="col-lg-2">
                                    <label>Total parcial: </label> <span class="valorParcial"></span>

                                </div>
                                <div class="col-lg-2">
                                <a href="javascript:void(0)" class="btn btn-sm btn-danger btn-remove">Remover Item</a>
                                </div>
                            </div>

                            <div class="col-lg-12 text-right ">
                                <label>Valor Total: R$ </label> <span class="valorFinal">0</span>
                            </div>

                        </div>

                    <!--Aqui termina a repetição para adicionar-->


                        <div class="box-footer">
                            <div class="col-sm-1">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <span class="glyphicon glyphicon-floppy-disk"></span> Cadastrar
                                </button>
                            </div>

                            <div class="col-sm-1">
                                <a href="/admin/baixa" class="btn btn-sm btn-primary">
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