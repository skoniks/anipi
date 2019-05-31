<html>
    <head>
        <link rel="icon" type="image/png" href="/assets/img/icon.png">
        <meta name="viewport" content="width=400, initial-scale=1">
        <link rel="stylesheet" href="/assets/css/style.css?<?php //echo time() ?>">
        <link rel="stylesheet" href="/assets/css/fa.css">
        <title>ANIPI.CC - Anime Pictures</title>
    </head>
    <body>
        <?php echo view('includes/header') ?>
        <?php echo view('includes/category') ?>
        <div class="grid"></div>
        <?php echo view('includes/modals') ?>
        <div class="notify"></div>
        <script src="/assets/js/script.js?<?php //echo time() ?>"></script>
    </body>
</html>
