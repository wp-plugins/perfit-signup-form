
<style>
.alert {
    border: 1px solid transparent;
    border-radius: 4px;
    margin-bottom: 20px;
    padding: 15px;
}
.alert-danger {
    background-color: #f2dede;
    border-color: #ebccd1;
    color: #a94442;
}
</style>

    <div style="width: 100%; text-align: center;">
        <img src="<?php echo plugins_url( '../images/logoperfit.gif', __FILE__); ?>">
    </div>
    <input type="hidden" name="login" value="1">
    <p>
        <label style="width: 100%;">Email</label>
        <input style="width: 100%;" class="form-control" name="email" value="<?=$_SESSION['userEmail']?>" type="text">
    </p>
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

    <p>
        <label style="width: 100%;">Contraseña</label>
        <input class="form-control" style="width: 100%;" name="password" autocomplete="off" type="password">
    </p>

    <?php echo ($_SESSION['error'])? '<div class="alert alert-danger" role="alert" style="">'.$_SESSION['error'].'</div>' : '' ?>

