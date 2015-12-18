<?php

use App\Models\GeneralParameter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralParameterSeeder extends Seeder{

  public function run(){
    DB::collection('GeneralParameters')->delete();


    GeneralParameter::create(array(
      'code' => 'IVA',
      'description' => 'Código de tipo de impuesto IVA',
      'alfanumericValue' => '55a43d985129a48f050041b6'
    ));
    GeneralParameter::create(array(
      'code' => 'ICE',
      'description' => 'Código de tipo de impuestos de ICE',
      'alfanumericValue' => '55a43e105129a48f050041b8'
    ));
    GeneralParameter::create(array(
      'code' => 'IRBPNR',
      'description' => 'Código de tipo de impuestos de IRBPNR',
      'alfanumericValue' => '55a43e2b5129a48f050041b9'
    ));
    GeneralParameter::create(array(
      'code' => 'VMMCF',
      'description' => 'Valor Monto Máximo Consumidor Final',
      'numericValue' => 20
    ));
    GeneralParameter::create(array(
      'code' => 'FEFactura',
      'description' => 'Código comprobante FACTURA electrónica XML',
      'alfanumericValue' => '01'
    ));
    GeneralParameter::create(array(
      'code' => 'FENotaCredito',
      'description' => 'Código comprobante NOTA DE CREDITO electrónica XML',
      'alfanumericValue' => '04'
    ));
    GeneralParameter::create(array(
      'code' => 'FENotaDebito',
      'description' => 'Código comprobante NOTA DE DEBITO electrónica XML',
      'alfanumericValue' => '05'
    ));
    GeneralParameter::create(array(
      'code' => 'FEGuiaRemision',
      'description' => 'Código comprobante GUIA DE REMISION electrónica XML',
      'alfanumericValue' => '06'
    ));
    GeneralParameter::create(array(
      'code' => 'FEComprobanteRetencion',
      'description' => 'Código comprobante COMPROBANTE DE RETENCION electrónico XML',
      'alfanumericValue' => '07'
    ));
    GeneralParameter::create(array(
      'code' => 'FEAmbiente',
      'description' => 'Ambiente de pruebas 1 y ambiente de producción 2',
      'alfanumericValue' => '1'
    ));
    GeneralParameter::create(array(
      'code' => 'FEEmisionNormal',
      'description' => 'Código para Facturación Electrónica Normal',
      'alfanumericValue' => '1'
    ));
    GeneralParameter::create(array(
      'code' => 'FEVentaRuc',
      'description' => 'Código para venta con ruc',
      'alfanumericValue' => '04'
    ));
    GeneralParameter::create(array(
      'code' => 'FEVentaCedula',
      'description' => 'Código para venta con cédula',
      'alfanumericValue' => '05'
    ));
    GeneralParameter::create(array(
      'code' => 'FEVentaPasaporte',
      'description' => 'Código para venta con pasaporte',
      'alfanumericValue' => '06'
    ));
    GeneralParameter::create(array(
      'code' => 'FEVentaConsumidorFinal',
      'description' => 'Código para venta consumidor final',
      'alfanumericValue' => '07'
    ));
    GeneralParameter::create(array(
      'code' => 'FECodigoIva',
      'description' => 'Código para venta con IVA',
      'alfanumericValue' => '2'
    ));
    GeneralParameter::create(array(
      'code' => 'FECodigoIce',
      'description' => 'Código para venta con ICE',
      'alfanumericValue' => '3'
    ));
    GeneralParameter::create(array(
      'code' => 'FECodigoPorcentajeIva0',
      'description' => 'Código para indicar porcentaje de IVA 0%',
      'alfanumericValue' => '0'
    ));
    GeneralParameter::create(array(
      'code' => 'FECodigoPorcentajeIva12',
      'description' => 'Código para indicar porcentaje de IVA 12%',
      'alfanumericValue' => '2'
    ));
    GeneralParameter::create(array(
      'code' => 'FEDescripcionProducto',
      'description' => 'Indica el campo que se va a usar en la factura electrónica en la descripción',
      'alfanumericValue' => 'description'
    ));
    GeneralParameter::create(array(
      'code' => 'SaeBasic',
      'description' => '1 Indica que se encuentra activo SAE BASIC, implica menos campos en clientes , proveedores y productos',
      'alfanumericValue' => '0'
    ));
    GeneralParameter::create(array(
      'code' => 'SaeAccounting',
      'description' => '0 Indica que no se encuentra activa la contabilidad',
      'alfanumericValue' => '1'
    ));
    GeneralParameter::create(array(
      'code' => 'SaeInventory',
      'description' => '0 Indica que no se encuentra activo el kardex ni controla stock',
      'alfanumericValue' => '1'
    ));
  }
}
