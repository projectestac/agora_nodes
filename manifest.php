<?php
$uri = explode('/', $_SERVER['REQUEST_URI']);

$path = '/'.$uri[1].'/';
if ( count($uri) > 3 ) {
    //For non productive environments.
    $path .= $uri[2].'/';
}

$manifest = <<<EOT
{
"name": "Àgora Nodes: $path",
"short_name": "Nodes",
"icons": [{
    "src": "{$path}wp-content/themes/reactor/icon-192x192.png",
    "sizes": "192x192",
    "type": "image/png"
}],
"background_color": "#D5E0EB",
"theme_color": "#D5E0EB",
"display": "standalone",
"start_url": "$path",
"scope": "$path"
}
EOT;

header('Content-Type: application/json');
echo $manifest;
