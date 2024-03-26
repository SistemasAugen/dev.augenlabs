<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Email Template</title>
<style>
  /* Inline your CSS here for email clients */
  .email-container {
    max-width: 600px;
    margin: auto;
    padding: 20px;
    font-family: Arial, sans-serif;
  }
  .header {
    background-color: #f4f4f4;
    padding: 10px;
    text-align: center;
  }
  .content {
    background-color: #ffffff;
    padding: 20px;
    text-align: center;
  }
  .footer {
    padding: 10px;
    text-align: center;
    background-color: #f4f4f4;
  }
</style>
</head>
<body>
  <div class="email-container">
    <!-- Header -->
    <div class="header">
      <img src="{{ $logoUrl }}" alt="Company Logo">
    </div>
    
    <!-- Content -->
    <div class="content">
      <h1>Tu código para iniciar sesión es:</h1>
      <p><strong>{{ $loginCode }}</strong></p>
      <p>Por favor regresa al log in e inserta el código de arriba para verificar tu identidad.</p>
      <p>Si no solicitaste este código, puedes ignorar este mensaje.</p>
      <br/>
      <p>Este código expira en 5 minutos.</p>
    </div>
    
    <!-- Footer -->
    <div class="footer">
      Saludos,<br>
      Equipo Augen
    </div>
  </div>
</body>
</html>
