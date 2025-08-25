<?php
header('Content-Type: application/javascript');
$service = getenv('NESHAN_SERVICE_API_KEY') ?: '';
$map = getenv('NESHAN_MAP_API_KEY') ?: '';
$search = getenv('NESHAN_SEARCH_API_KEY') ?: $service;
echo 'window.NESHAN_SERVICE_API_KEY = ' . json_encode($service) . ";\n";
echo 'window.NESHAN_MAP_API_KEY = ' . json_encode($map) . ";\n";
echo 'window.NESHAN_SEARCH_API_KEY = ' . json_encode($search) . ";\n";
?>
