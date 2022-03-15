<?php
require ('../../config.php');

$from = required_param('from', PARAM_RAW);
$to = required_param('to', PARAM_RAW);

$SESSION->coursehome = $from;
redirect($to);