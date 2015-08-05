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

$id = $_GET['id'];

// if no id is set, look for default optin name
/*
if (!$id) {

    $_optin = $perfit->optins->params(['q' => $perfitConfig['optinName']])->get();

    if ($_optin->data[0]->id) {
        $id = $_optin->data[0]->id;
    }
}
*/

// Retrieve optin 
$optin = array();
if ($id) {
    $optin = $perfit->optins->id($id)->get();
}
else 
    $optin = $perfit->optins->method('get')->default();

// Retrieve lists
// $lists = json_decode(file_get_contents($config['baseUrl']."/lists?token=".$config['token']."&order=name+asc&limit=1000&offset=0"));
$lists = $perfit->lists->limit(1000)->get();

// Retrieve fields
// $fields = json_decode(file_get_contents($config['baseUrl']."/fields?token=".$config['token']."&order=name+asc&limit=1000&offset=0"));
$fields = $perfit->fields->limit(1000)->get();
// echo '<pre>'.print_r($fields, true).'</pre>';

// Retrieve interests
// $interests = json_decode(file_get_contents($config['baseUrl']."/interests?token=".$config['token']."&order=name+asc&limit=1000&offset=0"));
$interests = $perfit->interests->limit(1000)->get();

// Obtain selected interests
$selectedInterests = array();
if ($optin && isset($optin->data->form) && isset($optin->data->form->interests)) {
    foreach ($optin->data->form->interests as $interest) {
        $selectedInterests[$interest->id] = $interest;
    }
}

// Obtain selected fields
$selectedFields = array();
if ($optin && isset($optin->data->form) && isset($optin->data->form->fields)) {
    foreach ($optin->data->form->fields as $field) {
        $selectedFields[$field->id] = $field;
    }
}

// Move selected interests to top of interests list
$topInterest = array();
if ($interests->data) {
    foreach ($interests->data as $k => $v) {
        if (isset($selectedInterests[$v->id])) {
            array_unshift($topInterests, $v);
            unset($interests->data[$k]);
        }
    }
}
if ($topInterests) {
    foreach ($topInterests as $k => $v)
        array_unshift($interests->data, $v);
}

// Move selected fields to top of fields list
$topFields = array();
if ($fields->data) {
    foreach ($fields->data as $k => $v) {
        if (isset($selectedFields[$v->id])) {
            array_unshift($topFields, $v);
            unset($fields->data[$k]);
        }
    }
}
if ($topFields) {
    foreach ($topFields as $k => $v)
        array_unshift($fields->data, $v);
}

// Move selected lists to top of lists list
$topLists = array();
if ($optin) {
    foreach ($lists->data as $k => $v) {
        if (is_array($optin->data->lists) && in_array($v->id, $optin->data->lists)) {
            array_unshift($topLists, $v);
            unset($lists->data[$k]);
        }
    }
    foreach ($topLists as $k => $v)
        array_unshift($lists->data, $v);
}

// echo '<pre>';
// print_r($optin->data);
// print_r($lists);
// print_r($fields);
// print_r($interests);
// die('a');
// print_r($selectedInterests);
// print_r($selectedFields);
// die('a');

include(dirname(__FILE__).'/tpl/optin.php');


?>
