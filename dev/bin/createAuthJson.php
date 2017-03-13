<?php

$json['http-basic']['repo.magento.com'] = array(
    'username' => getenv('MAGENTO_AUTH_USERNAME'),
    'password' => getenv('MAGENTO_AUTH_PASSWORD'),
);

file_put_contents(getenv('AUTH_DIR'), json_encode($json));
