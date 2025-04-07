<!DOCTYPE html>
<html>

<head>
    <title>New Message Received</title>
</head>

<body>
    <h1>New Message from {{ $data['sender_name'] }}</h1>
    <p><strong>Correo:</strong> {{ $data['sender_email'] }}</p>
    <p><strong>Mensaje:</strong></p>
    <p>{{ $data['message'] }}</p>
    <p>Responde directamente a el cliente con el email de arriba</p>
    <p>Gracias,<br>SMR Heavy Maq</p>
</body>
</html>
