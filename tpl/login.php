<?php

// print_r($_SESSION['accountList']); 
// print_r($accountList); 
?>

<div class="wrap">

    <div class='app' id="login">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <form method="POST" data-form="login" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                        <input type="hidden" name="login" value="1">
                        <legend class="text-center">
                            <img src="<?php echo plugins_url( '../images/logoperfit.gif', __FILE__); ?>">
                        </legend>
                        <div class="form-group">
                            <label>
                                Email
                            </label>
                            <input class="form-control" name="email" type="text" value="<?=$_SESSION['userEmail']?>" placeholder="Email">
                        </div>
                        <?php if ($_SESSION['accountList']): ?>
                        <div class="form-group">
                            <label>
                                Cuenta
                            </label>
                            <select name="account" class="form-control">
                                <?php foreach ($_SESSION['accountList'] as $k => $v): ?>
                                <option value="<?=$v?>"><?=$v?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label>
                                Contraseña
                            </label>
                            <input class="form-control" name="password" autocomplete="off" type="password" placeholder="Contraseña">
                        </div>

                        <?php echo ($_SESSION['error'])? '<div class="alert alert-danger" role="alert">'.$_SESSION['error'].'</div>' : '' ?>

                        <button class="btn btn-primary btn-lg btn-block" type="submit">
                            Ingresar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php unset($_SESSION['error']); ?>