<?php

include(dirname(__FILE__).'/includes/loader.php'); 

if (!$perfit->token()) {
    include(dirname(__FILE__).'/tpl/login.php');
    die();
}

$acl = array(
    "fields:list",
    "lists:list",
    "optins:create",
    "optins:update",
    "optins:list",
);
foreach ($acl as $needle) {
    if (!in_array($needle, $_SESSION['acl']))
        die('Permisos insuficientes: '.$needle);
}

// Obtengo los optins
$optins = $perfit->optins->params(array('fields' => 'subscriptions'))->limit(1000)->get();

if ($optins->error->type == 'UNAUTHORIZED') {
    unset($_SESSION['token']);
    header("Location: options-general.php?page=perfit_optin");
    die();
}

// Save optin list for WYSIWYG plugin
if (!$optins->error) {
    delete_option("optin_list");
    add_option("optin_list", serialize($optins));
}

// echo '<pre>'.print_r($optins, true).'</pre>';
// die('a');
include(dirname(__FILE__).'/tpl/list.php');

// echo '<pre>';
// print_r($optin->data);
// print_r($lists);
// print_r($fields);
// print_r($interests);
// die('a');

?>
