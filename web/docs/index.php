<?php

require __DIR__ . '/../../vendor/autoload.php';

$openapi = \OpenApi\Generator::scan([__DIR__ . '/../../modules/api/modules/v1/controllers']);
header('Content-Type: application/x-yaml');
echo $openapi->toYaml();