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
              <span><strong>FACTURA</strong></span><br/>
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
            <span><strong>IDENTIFICACIÓN : </strong>{{$customer['identification']}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span><strong>FECHA DE EMISIÓN : </strong>{{$document['creationDate']}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span><strong>GUIA DE REMISIÓN: </strong></span><br/>
          </div>

        </td>
      </tr>
      <tr>
        <td colspan="2">
          <table width="100%" class="table-details">
              <tr class="table-header">
                <td class="td-auto">Cod. Principal</td>
                <td class="td-auto">Cod. Auxiliar</td>
                <td class="td-auto">Cantidad</td>
                <td>Descripción</td>
                <td class="td-auto">Precio Unitario</td>
                <td class="td-auto">Descuento</td>
                <td class="td-auto">Precio Total</td>
              </tr>
              @foreach ($products as $product)
                <tr class="table-row-info">
                  <td>{{$product['code']}}</td>
                  <td>{{$product['code']}}</td>
                  <td>{{$product['quantity']}}</td>
                  <td>{{$product['name']}}</td>
                  <td align="right">{{ money_format('%(#10n', $product['price']) }}</td>
                  <td align="right">{{ money_format('%(#10n', $product['totalDiscount']) }}</td>
                  <td align="right">{{ money_format('%(#10n', $product['total']) }}</td>
                </tr>
              @endforeach


          </table>
        </td>
      </tr>
      <tr>
        <td></td>
        <td>
          {{ setlocale(LC_ALL, 'es-EC') }}
          <table width="100%" class="table-totals">
              <tr>
                <td> <strong>SUBTOTAL 12% </strong></td>
                <td align="right"> {{ money_format('%(#10n', $document['totals']['subtotalIva']) }} </td>
              </tr>
              <tr>
                <td><strong>SUBTOTAL 0%</strong></td>
                <td align="right">{{ money_format('%(#10n',$document['totals']['subtotalIvaZero']) }}</td>
              </tr>
              <tr>
                <td><strong>SUBTOTAL NO OBJETO DE IVA</strong></td>
                <td align="right">{{ money_format('%(#10n',$document['totals']['subtotalWithOutIva']) }}</td>
              </tr>
              <tr>
                <td><strong>SUBTOTAL SIN IMPUESTOS</strong></td>
                <td align="right">{{ money_format('%(#10n',$document['totals']['subtotal']) }}</td>
               </tr>
              <tr>
                <td><strong>SUBTOTAL EXENTO DE IVA</strong></td>
                <td align="right">{{ money_format('%(#10n',$document['totals']['subtotalWithOutIva']) }}</td>
              </tr>
              <tr>
                <td><strong>DESCUENTO</strong></td>
                <td align="right">{{ money_format('%(#10n',$document['totals']['totalDiscount']) }}</td>
              </tr>
              <tr>
                <td><strong>ICE</strong></td>
                <td align="right">{{ money_format('%(#10n',$document['totals']['totalICE']) }}</td>
              </tr>
              <tr>
                <td><strong>IVA 12%</strong></td>
                <td align="right">{{ money_format('%(#10n',$document['totals']['totalIVA']) }}</td>
              </tr>
              <tr>
                <td><strong>IRBPNR</strong></td>
                <td align="right">{{ money_format('%(#10n',$document['totals']['totalIRBPNR']) }}</td>
              </tr>
              <tr>
                <td><strong>PROPINA</strong></td>
                <td align="right">{{ money_format('%(#10n',$document['totals']['tip']) }}</td>
               </tr>
              <tr>
                <td><strong>VALOR TOTAL</strong></td>
                <td align="right">{{ money_format('%(#10n',$document['totals']['total']) }}</td>
               </tr>
          </table>
        </td>
      </tr>
    </table>









  </body>
</html>
