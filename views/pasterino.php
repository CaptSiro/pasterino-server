<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <script>
        const PROJECT_PATH = "<?= $GLOBALS["project_path"] ?>";
    </script>
    <script src="<?= $GLOBALS["project_path"] ?>/public/js/pasterino.js" type="module" defer></script>
    <script src="<?= $GLOBALS["project_path"] ?>/public/js/session-cookie.js" type="module" defer></script>

    <title>Pasterino</title>
</head>
<body>
    <button data-server-login>Login</button>
</body>
</html>