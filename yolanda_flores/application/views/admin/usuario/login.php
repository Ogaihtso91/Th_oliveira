
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>Sistema de Administração</title>
    <meta name="generator" content="Bootply" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="description" content="Example snippet for a Bootstrap login form modal" />
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">

    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link rel="apple-touch-icon" href="/bootstrap/img/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/bootstrap/img/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/bootstrap/img/apple-touch-icon-114x114.png">

    <!-- CSS code from Bootply.com editor -->

    <style type="text/css">
        .modal-footer {   border-top: 0px; }
    </style>
</head>

<!-- HTML code from Bootply.com editor -->

<body  >

<!--login modal-->
<div id="loginModal" class="modal show" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="text-center">Yolanda Flores</h1>
            </div>
            <div class="modal-body">
                <form class="form col-md-12 center-block" action="/admin/" method="post">

                    <?php $this->load->view('admin/mensagem'); ?>

                    <div class="form-group">
                        <input type="email" class="form-control input-lg" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control input-lg" name="senha" placeholder="Senha" required>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-lg btn-block">Entrar</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>


</body>
</html>


