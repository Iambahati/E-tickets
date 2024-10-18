<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Your Company Name</title>
    <style>
        * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    color: #333;
}

.container {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

header {
    border-bottom: 1px solid #e5e5e5;
    padding: 1rem;
}

.logo {
    background-color: #1a73e8;
    color: white;
    font-weight: bold;
    font-size: 1.2rem;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    display: inline-block;
}

main {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
}

h1 {
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
    text-align: center;
}

form {
    width: 100%;
    max-width: 400px;
}

.form-group {
    margin-bottom: 1rem;
}

label {
    display: block;
    margin-bottom: 0.5rem;
}

input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.btn {
    display: inline-block;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    text-align: center;
    text-decoration: none;
}

.btn-primary {
    background-color: #1a73e8;
    color: white;
}

.btn-primary:hover {
    background-color: #1557b0;
}

.text-center {
    text-align: center;
}

.mt-20 {
    margin-top: 1.25rem;
}

a {
    color: #1a73e8;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

.forgot-password {
    display: inline-block;
    margin-top: 1rem;
}

footer {
    border-top: 1px solid #e5e5e5;
    padding: 1rem;
    text-align: center;
    font-size: 0.875rem;
    color: #666;
}
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">E-Ticketing</div>
        </header>
        <main>
            <h1>Log in to your account</h1>
            <form id="loginForm">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>
                <button type="submit" class="btn btn-primary">Log In</button>
            </form>
            <div class="text-center">
                <a href="#" class="forgot-password">Forgot your password?</a>
            </div>
            <div class="text-center mt-20">
                Don't have an account? <a href="register.html">Sign up</a>
            </div>
        </main>
        <footer>
        <p>&copy; 2023 E-Ticketing. All rights reserved.</p>
        </footer>
    </div>
    <script>

document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('registerForm');

    registerForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (password !== confirmPassword) {
            alert("Passwords don't match!");
            return;
        }

        // Here you would typically send the registration data to your server
        console.log('Registration attempt:', { name, email, password });

        // For demonstration purposes, we'll just log a success message
        alert('Registration successful!');
    });
});
    </script>
</body>
</html>