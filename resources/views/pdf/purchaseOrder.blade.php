<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Pedido de Cliente</title>
    <link rel="stylesheet" type="text/css" href="styles/documentPrint.css">
  </head>
  <body>
    <table width="100%">
      <tr>

        <td class="colum-middle" align="center">
          <div id="comanyInfo">
              <div class="logo">
                <img src="{{$companyInfo['logo']['src']}}" height="150"/>
              </div>
          </div>
        </td>

        <td align="left" valign="top">
          <div class="document-info">
            <div class="max-line">
              <span style="font-size: 17px;"><b>PEDIDO DE CLIENTE </b>&nbsp;&nbsp;&nbsp;{{$document['number']}} </strong></span><br/>
              <span style="font-size: 10px;"><b>R.U.C : </b>{{$companyInfo['identification']}}</span><br/>
              <span style="font-size: 10px;"><b>RAZÓN SOCIAL : </b>{{$companyInfo['businessName']}}</span><br/>
              @if ($companyInfo['specialContributor'] == 1)
                <span style="font-size: 13px;"><b>CONTRIBUYENTE ESPECIAL NRO.:</b> </span><br/>
              @endif
              <span style="font-size: 10px;"><b>DIRECCIÓN </b>&nbsp;&nbsp;&nbsp;{{ isset($companyInfo['address']) ? $companyInfo['address'] : ''}} </strong></span><br/>
              <span style="font-size: 10px;"><b>TELÉFONO </b>&nbsp;&nbsp;&nbsp;{{ isset($companyInfo['telephone']) ? $companyInfo['telephone'] : ''}} </strong></span><br/>
              <span style="font-size: 10px;"><b>CORREO </b>&nbsp;&nbsp;&nbsp;{{ isset($companyInfo['email']) ? $companyInfo['email'] : ''}} </strong></span><br/>
            </div>
          </div>
        </td>

      </tr>

      <tr>
        <td colspan="2" align="left" valign="top">
          <div class="customer-info">
            <span>Información Cliente</span><br/><br/>
            <span><strong>RUC/CI : </strong>{{$customer['identification']}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span><strong>Cliente : </strong>{{$customerName}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span><strong>Correo : </strong>{{ isset($customer['emails'][0]) ? $customer['emails'][0] : ''}}</span><br/><br/>
            <span><strong>Telf : </strong>{{ isset($customer['telephones'][0]) ? $customer['telephones'][0] : ''}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span><strong>Vendedor : </strong>{{$seller}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span><strong>Fecha de Emisión : </strong>{{ date('d-m-Y', strtotime($document['creationDate'])) }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span><strong>Fecha de Entrega: </strong>{{ date('d-m-Y', strtotime($document['deliveryDate'])) }}</span><br/>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <table width="100%" class="table-details">
              <thead class="table-header">
                <tr>
                  <th class="td-auto">Cod. Principal</th>
                  <th class="td-auto">Cantidad</th>
                  <th>Descripción</td>
                  <th class="td-auto">Precio Unitario</th>
                  <th class="td-auto">Descuento</th>
                  <th class="td-auto">Precio Total</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($products as $product)
                <tr class="table-row-info">
                  <td>{{$product['code']}}</td>
                  <td>{{$product['quantity']}}</td>
                  <td>{{$product['name']}}</td>
                  <td align="right">$ {{ money_format('%(#10n', $product['price']) }}</td>
                  <td align="right">$ {{ money_format('%(#10n', $product['totalDiscount']) }}</td>
                  <td align="right">$ {{ money_format('%(#10n', $product['total']) }}</td>
                </tr>
              @endforeach
              </tbody>
          </table>
        </td>
      </tr>
      <tr>
        {{ setlocale(LC_ALL, 'es-EC') }}
        <td colspan="2" valign="top" align="left">
          <table width="100%">
            <tr>
              <td width="65%">
              </td>
              <td width="35%" align="right">
                <table class="table-totals">
                  <tr>
                    <td align="right" class="td-gray">Subtotal 12%</td>
                    <td align="right"> $ {{ money_format('%(#10n', $document['totals']['subtotalIva']) }} </td>
                  </tr>
                  <tr>
                    <td align="right" class="td-gray">Subtotal 0%</td>
                    <td align="right"> $ {{ money_format('%(#10n',$document['totals']['subtotalIvaZero']) }}</td>
                  </tr>
                  <tr>
                    <td align="right" class="td-gray">Subtotal No Objeto de IVA</td>
                    <td align="right"> $ {{ money_format('%(#10n',$document['totals']['subtotalWithOutIva']) }}</td>
                  </tr>
                  <tr>
                    <td align="right" class="td-gray">Subtotal sin Impuesto</td>
                    <td align="right"> $ {{ money_format('%(#10n',$document['totals']['subtotal']) }}</td>
                   </tr>
                  <tr>
                    <td align="right" class="td-gray">Subtotal Exento de IVA</td>
                    <td align="right"> $ {{ money_format('%(#10n',$document['totals']['subtotalWithOutIva']) }}</td>
                  </tr>
                  <tr>
                    <td align="right" class="td-gray">Descuento</td>
                    <td align="right"> $ {{ money_format('%(#10n',$document['totals']['totalDiscount']) }}</td>
                  </tr>
                  <tr>
                    <td align="right" class="td-gray">ICE</td>
                    <td align="right"> $ {{ money_format('%(#10n',$document['totals']['totalICE']) }}</td>
                  </tr>
                  <tr>
                    <td align="right" class="td-gray">IVA 12%</td>
                    <td align="right"> $ {{ money_format('%(#10n',$document['totals']['totalIVA']) }}</td>
                  </tr>
                  <tr>
                    <td align="right" class="td-gray">IRBPNR</td>
                    <td align="right"> $ {{ money_format('%(#10n',$document['totals']['totalIRBPNR']) }}</td>
                  </tr>
                  <tr>
                    <td align="right" class="td-gray">Propina</td>
                    <td align="right"> $ {{ money_format('%(#10n',$document['totals']['tip']) }}</td>
                   </tr>
                  <tr>
                    <td align="right" class="td-gray">TOTAL</td>
                    <td align="right"> $ {{ money_format('%(#10n',$document['totals']['total']) }}</td>
                   </tr>
                </table>
              </td>
                
            </tr>
          </table>
        </td>
      </tr>
    </table>
    <table width="100%" class="footer">
      <tr>
        <td align="center" valign="middle">FORMAS DE PAGO : </td>
        <td>
          <table>
            <tr>
              <td><img src="dist/images/system/pichincha.jpg" width="40px"/></td>
              <td>Cta Corriente N:<br/>300621500-4 / ESEMEC S.A</td>
            </tr>
          </table>
        </td>
        <td>
          <table>
            <tr>
              <td><img src="dist/images/system/pacifico.png" width="40px"/></td>
              <td>Cta Corriente N:<br/>7248377 / ESEMEC S.A</td>
            </tr>
          </table>
        </td>
        <td>
          <table>
            <tr>
              <td><img src="dist/images/system/internacional.jpeg" width="40px"/></td>
              <td>Cta Corriente N:<br/>0700614803 / ESEMEC S.A</td>
            </tr>
          </table>
        </td>
      </tr>
    </table>

  </body>
</html>
