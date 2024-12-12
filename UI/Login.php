<?php

namespace UI;

use Interfaces\UIElement;
use Lib\DatabaseConnection;
use Services\UsersService;

class Login implements UIElement
{
    public function render(): string
    {


        $html = "
        <div class='w-100 h-100 d-flex align-items-center justify-content-center'>
            <div class='card p-3' style='max-width: 500px'>
                <h2 class='mb-4'><strong>Login</strong></h2>
                <div class='alert alert-danger d-none' id='alert'></div>
                <form method='POST' action='/api/login' id='loginForm'>
                    <div class='mb-3'>
                        <label class='form-label' for='email'>Email:</label>
                        <input class='form-control' type='email' id='email' name='email' required>
                    </div>
                    <div class='mb-3'>
                        <label class='form-label' for='password'>Password:</label>
                        <input class='form-control' type='password' id='password' name='password' required>
                    </div>
                    <button class='btn btn-primary w-100' type='submit'>Zaloguj</button>
                </form>
                <p class='text-center'>Nie masz konta? <a class='link-primary' href='/register'>Zarejestruj się tutaj</a></p>
            </div>
        </div>
        <script>
            const alert = document.getElementById('alert');
            document.getElementById('loginForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('/api/login', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '/app';
                    } else {
                       alert.classList.remove('d-none'); 
                       alert.innerText = data.message; 
                    }
                });
            });
        </script>
        ";

        return $html;
    }
}