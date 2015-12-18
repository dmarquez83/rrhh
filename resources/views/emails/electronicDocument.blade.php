<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Facturación Electronica</title>
  </head>
  <body>
      <img src="{{ $message->embed(App\Helpers\SystemConfiguration::getImagesCompanyPath(true).'companyLogo.png') }}" width="220px">
      <h2>Estimado(a) {{$companyName}}</h2>
      <p>
        Adjunto su comprobante electrónico <b>Factura</b> Número {{$documentNumber}}, por los servicios brindados.
      </p>
      <br/>
      ¡Gracias por preferirnos!
  </body>
</html>
