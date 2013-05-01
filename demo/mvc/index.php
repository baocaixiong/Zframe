<?php

    $config = include dirname(__FILE__) . '/Protected/Config/main.php';

    include dirname(__FILE__) . '/../../Z.php';

    \Z\Z::createWebApplication($config)->run()  ;
?>