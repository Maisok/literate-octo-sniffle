<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Подтверждение почты</title>
</head>
<body>
    <h2>Подтверждение почты</h2>
    <p>Пожалуйста, подтвердите ваш адрес электронной почты, перейдя по ссылке, отправленной на вашу почту.</p>
    <form action="{{ route('verification.send') }}" method="POST">
        @csrf
        <button type="submit">Отправить ссылку повторно</button>
    </form>
</body>
</html>