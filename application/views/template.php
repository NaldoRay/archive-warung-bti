<?php
/**
 * User: RN
 * Date: 6/20/2016
 * Time: 15:22
 */
?>
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title><?=$title?></title>
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/foundation.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/app.css') ?>">
    </head>
    <body>
        <script type="text/javascript" src="<?= base_url('assets/js/vendor/jquery.js') ?>"></script>
        
        <?=$content?>

        <script type="text/javascript" src="<?= base_url('assets/js/vendor/jquery.numeric.min.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/vendor/what-input.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/vendor/foundation.min.js') ?>"></script>
        <script>
            $(document).foundation();
        </script>
    </body>
</html>