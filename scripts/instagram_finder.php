<?php

    $api = new Instaphp\Instaphp([
        'client_id' => 'your client id',
        'client_secret' => 'your client secret',
        'redirect_uri' => 'http://somehost.foo/callback.php',
        'scope' => 'comments+likes'
    ]);

    $popular = $api->Media->Popular(['count' => 10]);

    if (empty($popular->error)) {
        foreach ($popular->data as $item) {
            printf('<img src="%s">', $item['images']['low_resolution']['url']);
        }
    }
?>