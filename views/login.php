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
    <script src="<?= $GLOBALS["project_path"] ?>/public/js/login.js" type="module" defer></script>

    <title>Login - Pasterino</title>
</head>
<body>
    <button data-server-login-with-twitch>Login with Twitch!</button>
</body>
</html>