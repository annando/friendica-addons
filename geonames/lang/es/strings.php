<?php

if(! function_exists("string_plural_select_es")) {
function string_plural_select_es($n){
	$n = intval($n);
	return intval($n != 1);
}}
;
$a->strings["Geonames settings updated."] = "Ajustes de geonombres actualizados.";
$a->strings["Geonames Settings"] = "Ajustes de Geonombres";
$a->strings["Enable Geonames Addon"] = "Habilitar Addon de Geonombres";
$a->strings["Submit"] = "Enviar";
