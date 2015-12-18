<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Solicitud de Compra</title>
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
              <span style="font-size: 17px;"><b>SOLICITUD DE COMPRA </b><br/>
              <span>{{$document['number']}} </span><br/>
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
            <span><strong>Fecha de Requerida: </strong>{{ date('d-m-Y', strtotime($document['requiredDate'])) }}</span><br/>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <table width="100%" class="table-details">
              <thead class="table-header">
                <tr>
                  <th class="td-auto">Cod. Principal</th>
                  <th>Descripción</td>
                  <th class="td-auto">Cantidad</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($products as $product)
                <tr class="table-row-info">
                  <td>{{$product['code']}}</td>
                  <td>{{$product['name']}}</td>
                  <td>{{$product['quantity']}}</td>
                </tr>
              @endforeach
              </tbody>
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