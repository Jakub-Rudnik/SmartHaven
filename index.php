<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smarthaven</title>
    <link rel="stylesheet" href="/styles/main.css">
</head>
<body class="d-flex flex-md-row p-1 p-md-3 gap-3 w-100 vh-100">

    <?php
        require_once './UI/components/header.php';
        require_once './UI/components/navbar.php';
    ?>


    <main class="card bg-dark-subtle flex-grow-1 p-4" style="min-height: 100%">
        <?php



            $request = explode('/', $_SERVER['REQUEST_URI']);

            switch ($request[1]) {
                case '':
                    createHeader('Cześć Dawid! 😎');
                    break;
                case 'devices':
                    createHeader('Urządzenia');
                    require_once './UI/components/devices.php';
                    break;
                default:
                    break;
            }

        ?>
    </main>

</body>
</html>