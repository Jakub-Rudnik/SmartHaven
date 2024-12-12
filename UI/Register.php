<?php

namespace UI;

use Interfaces\UIElement;

class Register implements UIElement
{
    private string $message;

    public function render(): string
    {
        $html = "
        <div class='w-100 h-100 d-flex align-items-center justify-content-center'>
            <div class='card p-3' style='max-width: 500px'>
                <h2 class='mb-4'><strong>Login</strong></h2>
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
            document.getElementById('loginForm').addEventListener('submit', function(e) {
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
        ";

        return $html;
    }
}