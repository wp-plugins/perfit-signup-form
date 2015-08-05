<?php

include("../../../wp-load.php");

if ( !($optins = unserialize(get_option("optin_list")) ))
    die('No se encontraron optins');

$optinModes = array (
    'inline:' => 'Widget',
    'button:' => 'Botón',
    'popup:once' => 'Pop-Up (mostrar una vez)',
    'popup:always' => 'Pop-Up (mostrar hasta lograr subscripción)',
);


?>
<style>
.radio, .checkbox {
    display: block;
    margin-bottom: 10px;
    position: relative;
}
.radio label, .checkbox label {
    cursor: pointer;
    font-weight: 400;
    margin-bottom: 0;
    padding-left: 0;
}
label {
    display: inline-block;
    font-weight: 700;
    margin-bottom: 5px;
    max-width: 100%;
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
}
.btn-primary {
    background-color: #337ab7;
    border-color: #2e6da4;
    color: #fff;
}
.btn {
    -moz-user-select: none;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
    cursor: pointer;
    display: inline-block;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.42857;
    margin-bottom: 0;
    padding: 6px 12px;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
}
.select {
    background: #fff none repeat scroll 0 0;
    border: 1px solid #ddd;
    border-radius: 3px;
    font-size: 100%;
    padding: 0.5em;
    width: 100%;
    margin-bottom: 20px;
    margin-top: -10px;
}
h4 {
    color: #333;
    display: inline-block;
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
    font-size: 16px;
    font-weight: 700;
    line-height: 1.42857;
    margin-bottom: 15px;
    max-width: 100%;
}
</style>
<h4>Por favor seleccione un optin</h4>
<select name="optin" id="tinymce_optin" class="select">
    <?php foreach ($optins->data as $optin): ?>
        <option value="<?php echo $optins->request->account; ?>:<?php echo $optin->pubId; ?>"><?php echo $optin->name; ?></option>
    <?php endforeach; ?>
</select>

<?php  $i = 0;
        foreach ($optinModes as $k => $v): 
?>
    <div class="radio" id="tinymce_optin_mode">
        <label><input type="radio" name="mode" <?=($i++ == 0)? 'checked="checked"' : ''?> value="<?php echo $k?>"><?php echo $v?></label>
    </div>
<?php  endforeach; ?>


<input class="btn btn-primary" type="submit" id="tinymce_optin_add" value="Agregar" style="float: right;">

<script>

document.getElementById('tinymce_optin_add').onclick=function(){
    var ed = top.tinymce.activeEditor;
    var number = document.getElementById('tinymce_optin').value;

    var modes = document.getElementsByName('mode');
    var mode_value;
    for(var i = 0; i < modes.length; i++){
        if(modes[i].checked){
            mode_value = modes[i].value;
        }
    }
    console.log(number);
    shortcode = '[perfit_optin ' + number + '-' + mode_value + ']';
    ed.execCommand('mceInsertContent', 0, shortcode);
    top.tinymce.activeEditor.windowManager.close();
}

</script>
