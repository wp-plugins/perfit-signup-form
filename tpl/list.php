<?php

/*
    [0] => stdClass Object
        (
            [id] => 2
            [pubId] => febyUMyP
            [name] => Facebook Optin
            [description] => Formulario de suscripción en Facebook
            [created] => 2015-07-03T03:03:16.000+0000
            [lastModified] => 2015-07-10T20:29:40.000+0000
            [lists] => Array
                (
                    [0] => 7
                )

        )

        [subscriptions] => stdClass Object
            (
                [total] => 0
                [lastMonth] => 0
                [lastWeek] => 0
            )
*/
?>

<div class="wrap">
        <div class="container-fluid" id="optin-list">
            <div class="row">
                <div class="col-md-12">
                    <div class="page">
                        <div class="list">
                            <div class="toolbar row">
                                <a href="options-general.php?page=perfit_optin&action=new" class="btn btn-primary">
                                    Nuevo
                                </a>
                                <div class="actions hide">
                                    <button class="btn btn-default" data-action="mass" data-method="destroy">
                                        Eliminar
                                    </button>
                                </div>
                            </div>

                            <?php if (isset($_GET['success']) && ($_GET['success'] == 1)): ?>
                            <div id="success-message" class="alert alert-success" role="alert">
                                <strong>Guardado!</strong>
                                Su formulario ha sido guardado exitosamente.
                            </div>
                            <?php endif; ?>

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>
                                            <!-- <input data-action="check-all" name="mass" type="checkbox"> -->
                                        </th>
                                        <th>
                                            Nombre
                                        </th>
                                        <th>
                                            Descripción
                                        </th>
                                        <th>
                                            Shortcode
                                        </th>
                                        <th>
                                            Creado
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php  if (!empty($optins->data)): ?>
                                        <?php  foreach ($optins->data as $optin): ?>
                                    <tr>

                                        <td>
                                            <!-- <input value="<?php echo $optin->id?>" data-action="check" type="checkbox"> -->
                                        </td>
                                        <td class="main">
                                            <a href="<?php echo $_SERVER['REQUEST_URI']?>&id=<?php echo $optin->id?>">
                                                <span class="name"><?php echo $optin->name?></span>
                                            </a>
                                            <p class="description"><?php echo $optin->description?></p>
                                            <p class="created">Creado el <?php echo strftime("%e de %B", strtotime($optin->created))?></p>
                                        </td>
                                        <td class="stats">
                                            <span class="number"><?=number_format($optin->subscriptions->total, 0, '', '.')?></span>
                                            <span class="reference">suscriptos totales</span>
                                        </td>
                                        <td class="stats">
                                            <span class="number"><?=number_format($optin->subscriptions->lastMonth, 0, '', '.')?></span>
                                            <span class="reference">&uacute;ltimo mes</span>
                                        </td>
                                        <td class="stats">
                                            <span class="number"><?=number_format($optin->subscriptions->lastWeek, 0, '', '.')?></span>
                                            <span class="reference">&uacute;ltima semana</span>
                                        </td>
                                    </tr>
                                        <?php  endforeach; ?>
                                    <?php  else: ?>
                                    <tr>
                                        <td colspan="4" class="empty-state">
                                            <img src="<?php echo plugins_url( '../images/empty.png', __FILE__); ?>">
                                            <div class="message">
                                                <h2>No ten&eacute;s formularios a&uacute;n.</h2>
                                                <h3>Cre&aacute; un formulario para empezar<br/>a sumar suscriptores.</h3>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php  endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</div>

<!--
<script src='//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
<script src='//netdna.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js'></script>
<script src="<?php echo plugins_url( '../js/app.js', __FILE__); ?>"></script>
-->

