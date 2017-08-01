<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Smoothrition | login</title>
    <link rel="stylesheet" type="text/css" href="public/css/style.css" />
    <link rel="stylesheet" type="text/css" href="public/css/bootstrap.min.css" />
</head>
<body>
<div id="login-wrapper">
    <form method="POST" action="index.php?do=login" id="login-form">
        <label for="login-name">Korisničko ime:</label>
        <input id="login-name" name="username" type="text" />
        <label for="login-password">Šifra:</label>
        <input id="login-password" name="password" type="password" />
        <button id="login-btn" class="btn btn-primary" type="submit">Prijavi se</button>
    </form>
</div>

<script src="public/js/jquery-3.1.0.min.js"></script>
<script src="public/js/bootstrap.min.js"></script>
</body>
</html>