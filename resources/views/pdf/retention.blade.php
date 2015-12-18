<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Example 2</title>
    <link rel="stylesheet" type="text/css" href="styles/ride.css">
  </head>
  <body>

    <table width="100%">
      <tr>
        <td class="colum-middle">

          <div id="comanyInfo">
            <div>
              <div class="logo">
                <img src="{{$companyInfo['logo']['src']}}" width="100%"/>
              </div>
              <div  class="box company-info">
                <span class="center">{{$companyInfo['businessName']}}</span><br/>
                <span><strong>DIR MATRIZ:</strong> {{$companyInfo['address']}}</span><br/>
                <span><strong>DIR SUCURSAL:</strong> </span><br/>
                <span><strong>CONTRIBUYENTE ESPECIAL NRO.:</strong> </span><br/>
                <span><strong>OBLIGADO A LLEVAR CONTABILIDAD:</strong>
                  @if ($companyInfo['accountingForced'] == 1)
                    SI
                  @elseif ($companyInfo['accountingForced'] == 0)
                    NO
                  @endif
                </span><br/>
              </div>
            </div>
          </div>

        </td>

        <td>

          <div class="box document-info">
            <div class="max-line">
              <span><strong>R.U.C : </strong>{{$companyInfo['identification']}}</span><br/>
              <span><strong>COMPROBANTE DE RETENCIÓN</strong></span><br/>
              <span><strong>N: </strong>{{$document['number']}}</span><br/>
              <span><strong>NUMERO DE AUTORIZACIÓN </strong>{{ $numeroAutorizacion }}</span><br/>
              <span><strong>AMBIENTE: </strong>{{ $ambiente }}</span><br/>
              <span><strong>EMISIÓN: </strong>{{ $emision }}</span><br/>
              <span><strong>CLAVE DE ACCESO: </strong></span><br/>
            </div>
            <!-- imagen del codigo de barras -->
            <img src="data:image/png;base64,{{base64_encode($generator->getBarcode($claveAcceso, $generator::TYPE_CODE_128, 1.1, 40))}}">
            <span>{{$claveAcceso}}</span><br/>
          </div>

        </td>

      </tr>
      <tr>
        <td colspan="2">

          <div class="box customer-info">
            <span><strong>RAZÓN SOCIAL / NOMBRE Y APELLIDOS : </strong>{{$companyName}}</span><br/><br/>
            <span><strong>IDENTIFICACIÓN : </strong>{{$supplier['identification']}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span><strong>FECHA DE EMISIÓN : </strong>{{$document['creationDate']}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span><strong>GUIA DE REMISIÓN: </strong></span><br/>
          </div>

        </td>
      </tr>
      <tr>
        <td colspan="2">
            {{ setlocale(LC_ALL, 'es-EC') }}
          <table width="100%" class="table-details">
              <tr class="table-header">
                <td class="td-auto">Comprobante</td>
                <td class="td-auto">Número</td>
                <td class="td-auto">Fecha de Emisión</td>
                <td class="td-auto">Base imponible</td>
                <td class="td-auto">Tipo</td>
                <td class="td-auto">Porcentaje de Retención</td>
                <td class="td-auto">Valor Retenido</td>
              </tr>
              @foreach ($taxes as $tax)
                <tr class="table-row-info">
                  <td>FACTURA</td>
                  <td>{{$supplierInvoice['number']}}</td>
                  <td>{{ date('m-d-y', strtotime($supplierInvoice['creationDate'])) }}</td>
                  <td align="right">{{ money_format('%(#10n', $tax['taxable']) }}</td>
                    @if ($tax['type'] == 'rent')
                        <td align="center">RENTA</td>
                    @elseif ($tax['type'] == 'iva')
                        <td align="center">IVA</td>
                    @endif
                  <td align="center">{{ round($tax['percentaje'] * 100, 2) }} %</td>
                  <td align="right">{{ money_format('%(#10n', $tax['total']) }}</td>
                </tr>
              @endforeach


          </table>
        </td>
      </tr>
    </table>









  </body>
</html>
