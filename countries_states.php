<?php

//$json = file_get_contents('assets/states.json');
//var_dump($json);
//$states = json_decode($json);
//var_dump($states);
//foreach($states as $state) var_dump($state);

$xml = simplexml_load_file('xml/country.xml');
$countries = array();
foreach($xml as $country)
{
	$countries[(string)$country->country_id] = array((string)$country->country_id, (string)$country->name, (string)$country->iso_code);
}

$json = json_encode($countries);
file_put_contents('assets/countries.json', $json);

$xml = simplexml_load_file('xml/state.xml');
$states = array();
foreach($xml as $state)
{
	$states[(string)$state->country_id][] = array((string)$state->name, (string)$state->abbreviation, (string)$state->country_id);
}

//var_dump($states);
$json = json_encode($states);
file_put_contents('assets/states.json', $json);
