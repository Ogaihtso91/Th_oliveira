<?php

 if ($this->session->flashdata('sucesso')): ?>

    <div data-notificacao class="alert alert-success">
        <span class="glyphicon glyphicon-ok-sign"></span>
        <?= $this->session->flashdata('sucesso'); ?>
    </div>

<?php elseif ($this->session->flashdata('alerta')): ?>

    <div data-notificacao class="alert alert-warning">
        <span class="glyphicon glyphicon-exclamation-sign"></span>
        <?= $this->session->flashdata('alerta'); ?>
    </div>

<?php elseif ($this->session->flashdata('erro')): ?>

    <div data-notificacao class="alert alert-danger">
        <span class="glyphicon glyphicon-remove-sign"></span>
        <?= $this->session->flashdata('erro'); ?>
    </div>

<?php endif; ?>

<?php $this->session->set_flashdata(array('sucesso' => null, 'alerta' => null, 'erro' => null)); ?>