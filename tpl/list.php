
<div class="wrap">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page">
                        <div class="list">
                            <div class="toolbar">
                                <a href="options-general.php?page=perfit_optin&action=new" class="btn btn-primary">
                                    Agregar
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
                                            Creada
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
                                        <td>
                                            <a href="<?php echo $_SERVER['REQUEST_URI']?>&id=<?php echo $optin->id?>">
                                                <?php echo $optin->name?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php echo $optin->description?>
                                        </td>
                                        <td>
                                            [perfit_optin <?php echo $perfit->account()?>:<?php echo $optin->pubId?>]
                                        </td>
                                        <td>
                                            <?php echo strftime("%e de %B", strtotime($optin->created))?>
                                        </td>
                                    </tr>
                                        <?php  endforeach; ?>
                                    <?php  else: ?>
                                    <tr data-id="23">
                                        <td colspan="4">No se encontraron resultados</td>
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

