<?php
    if (!ini_get('display_errors')) {
        ini_set('display_errors', '1');
    }
    $config = include dirname(__FILE__) . '/Protected/config/main.php';

    include dirname(__FILE__) . '/../../Z.php';

    \Z\Z::createRestfulApplication($config)->run();
?>