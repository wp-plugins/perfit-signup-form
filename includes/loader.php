<?php

include_once(dirname(__FILE__).'/config.inc.php');

include_once(dirname(__FILE__).'/../lib/perfit.php');

@session_start();

setlocale(LC_ALL, "es_ES");

$perfit = new PerfitSDK\Perfit($perfitConfig);

$ttl = 3600;
if ( (($_SESSION['last_action']+$ttl)-time()) < 0)
	unset($_SESSION['token']);
else
	$_SESSION['last_action'] = time();
// print_r($_SESSION);

if ($_SESSION['token'])
	$perfit->token($_SESSION['token']);

// if (($response->status == 401) && ($response->type == 'UNAUTHORIZED') ) {
// 	unset($_SESSION['token']);
// 	header("Location: options-general.php?page=perfit_optin");
// }

if ($_SESSION['account'])
	$perfit->account($_SESSION['account']);

