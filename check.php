<?php
error_reporting(1);
session_start();
//Load Configuration
include 'includes/config.php';
include 'includes/functions.php';
// Load modules then setup ones used by system

//Load Smarty
include 'includes/modules/smarty/Smarty.class.php';
$smarty = new Smarty;



//Load Database
include 'includes/modules/db/interface.php';
include 'includes/modules/db/database.php';
$db = new Database($_DB_SERVER,$_DB_NAME,$_DB_USER,$_DB_PASS,false,false,$_DB_ENGINE);
//Load htmlMimeMail
include_once('includes/classes/htmlMimeMail.php');		
include_once('includes/classes/jsonRPCClient.php');

/* $freicoin 	=	new jsonRPCClient('http://poker:killer@localhost:9999/');
$result=$freicoin->getaccountaddress('ledger@sicanet.net'); */

$novacoin = new jsonRPCClient('http://novacoinrpc:Ramesh@127.0.0.1:9355/');
$result   = $novacoin->listreceivedbyaddress(1,false); // main functionality
print_r($result);
?>