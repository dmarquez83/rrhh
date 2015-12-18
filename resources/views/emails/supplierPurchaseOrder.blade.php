<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Facturación Electronica</title>
  </head>
  <body>
      <img src="{{ $message->embed(App\Helpers\SystemConfiguration::getImagesCompanyPath(true).'companyLogo.png') }}" width="220px">
      <h2>Estimado(a) {{$supplierName}}</h2>
      <p>
        Adjunto el pedido realizado, para la confirmación
      </p>
      <br/>
      ¡Gracias por su atención!
  </body>
</html>