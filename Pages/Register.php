<?php

namespace Pages;

?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja | SmartHaven</title>
    <link rel="stylesheet" href="/styles/main.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</head>
<body class="d-flex flex-md-row p-1 p-md-3 gap-3 w-100 vh-100 overflow-hidden">
<main class="card bg-dark-subtle flex-grow-1 pb-4 px-4 overflow-y-auto" style="max-height: 100vh">
    <div class='w-100 h-100 d-flex align-items-center justify-content-center'>
        <div class='card p-3' style='max-width: 500px'>
            <h2 class='mb-4'><strong>Rejestracja</strong></h2>
            <div class='alert alert-danger d-none' id='alert'></div>
            <form method='POST' id='loginForm'>
                <div class='mb-3'>
                    <label class='form-label' for='username'>Nazwa użytkownika:</label>
                    <input class='form-control' type='text' id='username' name='username' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label' for='email'>Email:</label>
                    <input class='form-control' type='email' id='email' name='email' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label' for='password'>Password:</label>
                    <input class='form-control' type='password' id='password' name='password' required>
                </div>
                <button class='btn btn-primary w-100' type='submit'>Zarejestruj się</button>
            </form>
            <p class='text-center'>Masz już konto? <a class='link-primary' href='/login'>Zaloguj</a></p>
        </div>
    </div>
    <script>
        const alert = document.getElementById('alert');
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('/api/register', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '/login';
                    } else {
                        alert.classList.remove('d-none');
                        alert.innerText = data.message;
                    }
                });
        });
    </script>
</main>
</body>
</html>