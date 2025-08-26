<?php
header('Content-Type: application/javascript');
$service = getenv('NESHAN_SERVICE_API_KEY') ?: 'service.6f92c26cc02a47aca745b15b9b87ee55';
$map = getenv('NESHAN_MAP_API_KEY') ?: 'web.3149369ad73f47aca745b15b9b87ee55';
$search = getenv('NESHAN_SEARCH_API_KEY') ?: $service;
echo 'window.NESHAN_SERVICE_API_KEY = ' . json_encode($service) . ";\n";
echo 'window.NESHAN_MAP_API_KEY = ' . json_encode($map) . ";\n";
echo 'window.NESHAN_SEARCH_API_KEY = ' . json_encode($search) . ";\n";
?>
