<?php
// echo '<pre>'.print_r($optin, true).'</pre>';

$fixedFields = array(3);



?>

<style>
.browser-bar {
    background: url("<?php echo plugins_url( '../images/browser.png', __FILE__); ?>") no-repeat scroll 15px 15px rgba(0, 0, 0, 0);
    min-height: 40px;
}
.live-preview {
    background: url("<?php echo plugins_url( '../images/transparent.gif', __FILE__); ?>") repeat scroll 0 0 rgba(0, 0, 0, 0);
    padding: 0;
}
.modal-dialog {
    margin: 90px auto;
}
input.autoresize, 
.text-muted input {
    background-color: transparent;
    border: 0;
}
.p-field input {
    border: 1px solid #ccc;
    border-radius: 5px;
}
.p-field label {
    width: 100%;
}
.fixed-field {
    cursor: not-allowed;
}
.p-select {
    height: 40px !important;
}
#fields li > .handle {
    background: url("<?php echo plugins_url( '../images/handle.png', __FILE__); ?>") no-repeat scroll;
    display: inline-block;
    width: 4px;
    height: 12px;
}
#fields li > .handle:hover {
    cursor: move;
}
</style>


<div class="wrap">

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page">
                        <form class="save" data-form="save" action="<?php echo $_SERVER['REQUEST_URI']?>" method="post">
                            <input type="hidden" name="validated" id="form-validated" value="0">
                            <input type="hidden" name="save" value="1">
                            <input type="hidden" name="data[name]" value="<?php echo $perfitConfig['optinName']?>">
                            <input type="hidden" name="id" value="<?php echo $id?>">
                            <div class="toolbar row">
                                <h1><?=($id)? $optin->data->name : 'Nuevo optin' ?> </h1>
                                <button type="submit" class="btn btn-primary pull-right">
                                    Guardar
                                </button>
                                <?php  if ($id): ?>
                                <a href="/wp-admin/options-general.php?page=perfit_optin&delete=<?php echo $id?>" class="btn btn-danger pull-right" data-confirm="¿Está seguro de que desea eliminar el optin?" data-class="btn-danger" data-action="delete" type="button" style="margin-left: 10px; margin-right: 10px;">
                                    <i class="fa fa-trash"></i>Eliminar
                                </a>
                                <?php  endif; ?>
                                <a href="/wp-admin/options-general.php?page=perfit_optin" class="btn btn-default pull-right" style="margin-right: 10px;" type="button"> Cancelar </a>
                            </div>

                            <div class="alert alert-danger" id="gral-error" role="alert" style="display: none;">
                                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                <span class="sr-only">Error:</span>
                                <span class="gral-error-msg">Enter a valid email address</span>
                            </div>

                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a data-toggle="tab" data-target="#data">
                                        General
                                    </a>
                                </li>
                                <li>
                                    <a data-toggle="tab" data-target="#design">
                                        Diseño de formulario
                                    </a>
                                </li>
                                <li>
                                    <a data-toggle="tab" data-target="#confirmation">
                                        Email de confirmación
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active row" id="data">
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label>
                                                Nombre
                                            </label>
                                            <!-- <?php echo $perfitConfig['optinName']?> -->
                                            <input class="form-control" name="data[name]" id="input-name" value="<?php echo $optin->data->name?>" type="text" placeholder="Nombre">
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                Descripción
                                            </label>
                                            <input class="form-control" name="data[description]" id="input-descripcion" value="<?php echo $optin->data->description?>" type="text" placeholder="Descripción">
                                        </div>
                                        <div class="form-group">
                                            <p>Seleccioná las listas a las cuales querés suscribir los nuevos contactos:</p>
                                            <label id="lists-label">
                                                Listas
                                            </label>

                                            <div class="instant-search has-feedback">
                                                <input class="form-control finder" type="text" data-filter="#lists-list" placeholder="Buscar">
                                                <span class="form-control-feedback dashicons dashicons-search" style="color: #d6d6d6;"></span>
                                            </div>

                                            <?php  if ($lists->data): ?>

                                            <div id="lists-list" style="margin-top: 20px;">
                                                <ul class="list-selector list-checker">
                                                <?php  foreach ($lists->data as $list): ?>

                                                    <li class="item" data-role="list">
                                                        <div class="checkbox" style="padding-bottom: 0;">
                                                            <label>
                                                                <input name="data[lists][]" value="<?php echo $list->id?>" type="checkbox" <?php echo is_array($optin->data->lists) && in_array($list->id, $optin->data->lists)? 'checked="checked"' : ''?> >
                                                                <div class="list-item" style="margin-left: 0;">
                                                                    <span class="list-name"><?php echo $list->name?></span>
                                                                    <span class="list-count">
                                                                            <?=$list->totalContacts?> contactos
                                                                    </span>
                                                                    <span class="list-tags">
                                                                        <?php if (!empty($list->tags)): ?>
                                                                            <?php foreach ($list->tags as $tag): ?>
                                                                        <span class="label label-primary"><?=$tag?></span>
                                                                            <?php endforeach; ?>
                                                                        <?php endif; ?>
                                                                    </span>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </li>
                                                <?php  endforeach; ?>
                                                </ul>
                                            </div>
                                            <?php  else: ?>
                                            <div class="checkbox">No hay listas creadas</div>
                                            <?php  endif; ?>
                                        </div>
                                    </div>
                                    <?php if ($id): ?>
                                    <div class="stats col-md-5">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <span class="number"><?=number_format($optin->data->subscriptions->total, 0, '', '.')?></span>
                                                <span class="reference">suscriptos totales</span>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="number"><?=number_format($optin->data->subscriptions->lastMonth, 0, '', '.')?></span>
                                                <span class="reference">&uacute;ltimo mes</span>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="number"><?=number_format($optin->data->subscriptions->lastWeek, 0, '', '.')?></span>
                                                <span class="reference">&uacute;ltima semana</span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="tab-pane" id="design">
                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">Vista previa</div>
                                                <div class="panel-body live-preview">
                                                    <div data-preview="form">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            <ul class="nav nav-pills nav-justified">
                                                <li class="active">
                                                    <a data-toggle="tab" data-target="#details">
                                                        Detalles
                                                    </a>
                                                </li>
                                                <li>
                                                    <a data-toggle="tab" data-target="#fields">
                                                        Campos
                                                    </a>
                                                </li>
<!--
                                                <li>
                                                    <a data-toggle="tab" data-target="#interests">
                                                        Intereses
                                                    </a>
                                                </li>
-->
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="details">
                                                    <div class="form-group">
                                                        <label>
                                                            Título
                                                        </label>
                                                        <input class="form-control" name="data[form][title]" id="input-form-title" data-optin="title" data-update="keyUp" value="<?php echo $optin->data->form->title?>" type="text">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>
                                                            Texto
                                                        </label>
                                                        <input class="form-control" name="data[form][text]" id="input-form-text" data-optin="text" data-update="keyUp" value="<?php echo $optin->data->form->text?>" type="text">
                                                    </div>
<!--
                                                    <div class="form-group">
                                                        <label>
                                                            Texto de intereses
                                                        </label>
                                                        <input class="form-control" name="data[form][interestsText]" value="<?php echo $optin->data->form->interestsText?>" data-update="keyUp" type="text">
                                                    </div>
-->
                                                    <div class="form-group">
                                                        <label>
                                                            Texto del botón
                                                        </label>
                                                        <input class="form-control" name="data[form][buttonText]" id="input-form-buttonText" data-optin="buttonText" data-update="keyUp" value="<?php echo $optin->data->form->buttonText?>" type="buttonText">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>
                                                            Texto del pie
                                                        </label>
                                                        <input class="form-control" name="data[form][footer]" id="input-form-footer" data-optin="footer" data-update="keyUp" value="<?php echo $optin->data->form->footer?>" type="footer">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>
                                                            Mensaje de éxito
                                                        </label>
                                                        <input class="form-control" name="data[form][successMessage]" value="<?php echo $optin->data->form->successMessage?>" type="text">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>
                                                            Pagina de agradecimiento
                                                        </label>

                                                        <div class="">
                                                            <label style="font-weight: normal;">
                                                                <input type="radio" id="redirect-bool-1" name="redirect-bool" value="1">Redireccionar a: 
                                                            </label>
                                                        </div>
                                                        <input class="form-control" name="data[form][redirect]" id="input-form-redirect" value="<?php echo $optin->data->form->redirect?>" data-action="update-previews" type="redirect">
                                                        <div class="" style="margin-top: 15px;">
                                                            <label style="font-weight: normal;">
                                                                <input type="radio" id="redirect-bool-0" name="redirect-bool" value="0">No redireccionar
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="fields">
                                                    <span id="fields-label"></span>
                                                    <?php if (!empty($fields->data)): ?>
                                                    <p>Seleccioná, reordená y editá los campos que desees:</p>
                                                    <ul class="sortable">
                                                        <?php foreach ($fields->data as $k => $field): 
                                                                if (!$field->readOnly): 

                                                                    $fixed = in_array($field->id, $fixedFields)? true : false;
                                                        ?>
                                                        <li class="checkbox" data-model="<?=$field->id?>">
                                                            <span class="handle"></span>
                                                            <label>
                                                                <input name="data[form][fields][<?=$k?>][id]" <?=$fixed? ' class="fixed-field" readonly="readonly" ' : '' ?> data-field="<?=$field->name?>" data-update="change" value="<?=$field->id?>" type="checkbox" data-params='{"displayName": "<?=$selectedFields[$field->id]? $selectedFields[$field->id]->displayName : $field->name?>", "required": "<?=$selectedFields[$field->id]->required?>"}' <?=($selectedFields[$field->id])? 'checked="checked"' : ''?> >
                                                                <?=$field->name?>
                                                                <input type="hidden" readonly="readonly" name="data[form][fields][<?=$k?>][displayName]" data-role="display-name" value="<?=($selectedFields[$field->id])? $selectedFields[$field->id]->displayName : $field->name?>" class="autoresize">
                                                                <input type="hidden" readonly="readonly" name="data[form][fields][<?=$k?>][required]" data-role="required" value="<?=$selectedFields[$field->id]->required? 'true' : 'false' ?>">
                                                                <span class="text-muted"><?=($selectedFields[$field->id])? '('.$selectedFields[$field->id]->displayName.')' : '('.$field->name.')'?></span>
                                                            </label>
                                                            <a class="actionable" data-modal=".field-modal" data-target=".field-modal" data-var="fields" data-id="<?=$field->id?>"> · Editar </a>
                                                        </li>
                                                        <?php   endif;
                                                            endforeach; ?>
                                                    </ul>
                                                    <?php else: ?>
                                                    <div class="checkbox">No hay campos creados</div>
                                                    <?php endif ?>
                                                </div>
<!--
                                                <div class="tab-pane" id="interests">
                                                    <?php  if ($interests->data): ?>
                                                    <ul class="sortable">
                                                        <?php  foreach ($interests->data as $k => $interest): ?>
                                                        <li class="checkbox" data-model="<?php echo $interest->id?>">
                                                            <label>
                                                                <input name="data[form][interests][<?php echo $k?>][id]" data-interest="<?php echo $interest->name?>" data-update="change" value="<?php echo $interest->id?>" type="checkbox" <?php echo (isset($selectedInterests[$interest->name]))? 'checked="checked"' : ''?> data-params='{"displayName": "<?php echo $selectedInterests[$interest->id]? $selectedInterests[$interest->id]->displayName : $interest->name ?>", "default": "<?php echo $selectedInterests[$interest->id]->default?>"}' <?php echo ($selectedInterests[$interest->id])? 'checked="checked"' : ''?> >
                                                                <?php echo $interest->name?>
                                                                <span class="text-muted" data-role="display-name"> (<?php echo ($selectedInterests[$interest->id])? $selectedInterests[$interest->id]->displayName : $field->name?>) </span>
                                                            </label>
                                                            <a class="actionable" data-modal=".interest-modal" data-target=".interest-modal" data-var="interests" data-id="<?php echo $interest->id?>"> · Editar </a>
                                                        </li>
                                                        <?php  endforeach; ?>
                                                    </ul>
                                                    <?php  else: ?>
                                                        <div class="checkbox">No hay intereses creados</div>
                                                    <?php  endif; ?>
                                                </div>
-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="confirmation">
                                    <div class="row">
                                        <div class="col-md-6">

                                            <div class="panel panel-default">
                                                <div class="panel-heading browser-bar"></div>
                                                <div class="panel-body">
                                                    <div data-preview="email">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    Correo del emisor
                                                </label>
                                                <input class="form-control" name="data[confirmation][fromAddress]" id="input-confirmation-fromAddress" data-optin="fromAddress" value="<?php echo $optin->data->confirmation->fromAddress?>" data-update="keyUp" type="text">
                                            </div>
                                            <div class="form-group">
                                                <label>
                                                    Nombre del emisor
                                                </label>
                                                <input class="form-control" name="data[confirmation][fromName]" id="input-confirmation-fromName" data-optin="fromName" value="<?php echo $optin->data->confirmation->fromName?>" data-update="keyUp" type="text">
                                            </div>
                                            <div class="form-group">
                                                <label>
                                                    Asunto
                                                </label>
                                                <input class="form-control" name="data[confirmation][subject]" id="input-confirmation-subject" data-optin="subject" value="<?php echo $optin->data->confirmation->subject?>" data-update="keyUp" type="text">
                                            </div>
                                            <div class="form-group">
                                                <label>
                                                    T&iacute;tulo
                                                </label>
                                                <input class="form-control" name="data[confirmation][title]" id="input-confirmation-title" data-optin="confirmationTitle" value="<?php echo $optin->data->confirmation->title?>" type="text">
                                            </div>
                                            <div class="form-group">
                                                <label>
                                                    Encabezado
                                                </label>
                                                <input class="form-control" name="data[confirmation][header]" id="input-confirmation-header" data-optin="header" value="<?php echo $optin->data->confirmation->header?>" data-update="keyUp" type="text">
                                            </div>
                                            <div class="form-group">
                                                <label>
                                                    Texto del vínculo
                                                </label>
                                                <input class="form-control" name="data[confirmation][linkText]" id="input-confirmation-linkText" data-optin="linkText" value="<?php echo $optin->data->confirmation->linkText?>" data-update="keyUp" type="text">
                                            </div>
                                            <div class="form-group">
                                                <label>
                                                    Pie
                                                </label>
                                                <input class="form-control" name="data[confirmation][footer]" id="input-confirmation-footer" data-optin="mailFooter" value="<?php echo $optin->data->confirmation->footer?>" data-update="keyUp" type="text">
                                            </div>
                                            <div class="form-group">
                                                <label>
                                                    Pagina de agradecimiento
                                                </label>
                                                <input class="form-control" name="data[confirmation][redirect]" id="input-confirmation-redirect" value="<?php echo $optin->data->confirmation->redirect?>" type="text">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

</div>

<div class="optin-template" style="display: none;">
    <div class="p-optin">
        <div class="p-header">{{title}}</div>
        <div class="p-body">
            <p>
                {{text}}
            </p>
            <input name="redirect" value="{{thankyou}}" type="hidden">
            <div class="p-fields">
                {{fields}}
            </div>
            <div class="p-interests">
                {{interests}}
            </div>
            <p>{{footer}}</p>
            <button type="button">{{buttonText}}</button>
        </div>
    </div>
</div>

<div class="modal fade field-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form>
                <input type="hidden" name="id" value="" data-key="">
                <input type="hidden" name="varName" value="fields" data-varName="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Editar campo</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>
                            Nombre a mostrar
                        </label>
                        <input class="form-control" name="displayName" data-param="" data-name="displayName" value="" type="text">
                    </div>
                    <div class="form-group field-required">
                        <div class="checkbox">
                            <label>
                                <input name="required" value="1" data-param="" data-name="required" type="checkbox">
                                Campo requerido
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <input type="submit" class="btn btn-primary" data-submit=".field-modal" value="Guardar">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade interest-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form>
                <input type="hidden" name="id" value="" data-key="">
                <input type="hidden" name="varName" value="interests" data-varName="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Editar campo</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>
                            Nombre a mostrar
                        </label>
                        <input class="form-control" name="displayName" data-param="" data-name="displayName" value="" type="text">
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input name="default" value="1" data-param="" data-name="default" type="checkbox">
                                Chequeado por defecto
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <input type="submit" class="btn btn-primary" data-submit=".interest-modal" value="Guardar">
                </div>
            </form>
        </div>
    </div>
</div>


<!--
<script src='//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
<script src='//code.jquery.com/ui/1.11.2/jquery-ui.min.js'></script>
<script src='//netdna.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js'></script>
<script src="<?php echo plugins_url( '../js/app.js', __FILE__); ?>"></script>
-->
