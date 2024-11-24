<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Подтверждение почты</title>
</head>
<body>
    <h2>Добро пожаловать, {{ $user->username }}!</h2>
    <p>Пожалуйста, подтвердите ваш адрес электронной почты, перейдя по ссылке ниже:</p>
    <a href="{{ route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->email)]) }}">Подтвердить почту</a>
    <p>Если вы не регистрировались на нашем сайте, проигнорируйте это письмо.</p>
</body>
</html>