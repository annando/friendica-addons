<?php

if(! function_exists("string_plural_select_de")) {
function string_plural_select_de($n){
	$n = intval($n);
	return intval($n != 1);
}}
;
$a->strings["Post to Dreamwidth"] = "In Dreamwidth veröffentlichen";
$a->strings["Dreamwidth Post Settings"] = "Dreamwidth Veröffentlichungs-Einstellungen";
$a->strings["Enable dreamwidth Post Addon"] = "Dreamwidth Post Addon aktivieren";
$a->strings["dreamwidth username"] = "Dreamwidth Benutzername";
$a->strings["dreamwidth password"] = "Dreamwidth Passwort";
$a->strings["Post to dreamwidth by default"] = "Standardmäßig bei Dreamwidth veröffentlichen";
$a->strings["Submit"] = "Senden";
