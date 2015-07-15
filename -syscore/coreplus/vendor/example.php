<?php

include 'Whois.php';

$w = new Whois;
echo '<pre>';
//print_r($w->query('przeslij.pl'));
//print_r($w->query('de77.com'));
//print_r($w->query('meinkaetzchen.de'));
//print_r($w->query('shop.fr'));
//print_r($w->query('wikipedia.org'));
//print_r($w->query('shop.net'));
//print_r($w->query('google.net'));
print_r($w->query('google.com'));