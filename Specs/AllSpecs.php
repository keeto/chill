<?php
require 'PHPSpec.php';
require_once('../Chill/Chill.php');

$options = new stdClass();
$options->recursive = true;
$options->specdoc = true;
$options->reporter = 'html';

PHPSpec_Runner::run($options);