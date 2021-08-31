<?php $this->load->view('admin/cabecalho'); ?>

    <!-- container -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Estoque
                <small>Sistema de administração</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="panel panel-default">

                <div class="panel-heading">

                    <?php if( empty($estoque->getId())): ?>
                        <h3 class="panel-title"><i class="glyphicon glyphicon-plus"></i> Cadastro</h3>
                    <?php else: ?>
                        <h3 class="panel-title"><i class="glyphicon glyphicon-plus"></i> Edição</h3>
                    <?php endif; ?>

                </div>

                <?php $this->load->view('admin/mensagem'); ?>

                <!--Colocar o conteúdo da página aqui-->

                <form role="form" method="POST" action="/admin/estoque/inserir">

                    <div class="box-body">
                        <input type="hidden" value="<?=@$estoque->getId()?>" name="id">
                        <div class="form-group">
                            <label>Nome do produto</label>
                            <select class="form-control select2" name="produtoId">
                                <option value="<?php (!$estoque->getProdutoId())? 'selected': ''; ?>">Selecione o Produto</option>
                                <?php foreach($produtos as $produto): ?>
                                    <option value='<?= $produto->getId() ?>' <?= ($produto->getId() == $estoque->getProdutoId()) ? 'selected' : ''; ?> >
                                        <?=  html_escape($produto->getNome()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Data</label>
                            <input type="text" value="<?=@$estoque->getData()?>" class="form-control" id="data" name="data" placeholder="00/00/0000" required>
                        </div>

                        <div class="form-group">
                            <label for="sel1">Quantidade</label>
                            <select class="form-control" name="quantidade">
                                <option value="<?php (!$estoque->getQuantidade())? 'selected': ''; ?>">Informe a quantidade da compra</option>
                                <?php for($i = 1; $i <= 1000; $i++): ?>
                                    <option value="<?= $i  ?>" <?= ($estoque->getQuantidade() == $i) ? 'selected' : ''; ?> ><?php echo $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Valor da Compra</label>
                            <input type="text" value="<?=@$estoque->getValor()?>" id="valor" class="form-control" name="valor" placeholder="R$ 00,00" required onKeyPress="return(MascaraMoeda(this,'.',',',event))" >
                        </div>

                        <div class="form-group">
                            <label>Lucro (%)</label>
                            <select class="form-control" name="lucro">
                                <option value=""<?php (!$estoque->getLucro())? 'selected': ''; ?>>Informe o lucro sobre o produto</option>
                                <?php for($i = 1; $i <= 1000; $i++): ?>
                                    <option value="<?= $i  ?>" <?= ($estoque->getLucro() == $i) ? 'selected' : ''; ?> ><?php echo $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="box-footer">
                            <div class="col-sm-1">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <span class="glyphicon glyphicon-floppy-disk"></span> Cadastrar
                                </button>
                            </div>

                            <div class="col-sm-1">
                                <a href="/admin/estoque" class="btn btn-sm btn-primary">
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