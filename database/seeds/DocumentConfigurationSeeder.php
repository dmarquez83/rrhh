<?php

use App\Models\DocumentConfiguration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentConfigurationSeeder extends Seeder{

  public function run()
  {
    DB::collection('DocumentConfiguration')->delete();

    $documents = [
      ['code' => '001', 'name' => 'Factura Cliente', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => true, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '002', 'name' => 'Nota de Crédito Proveedor', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => true, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '003', 'name' => 'Nota de Débito Proveedor', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => true, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '004', 'name' => 'Retención en ventas', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => true, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '005', 'name' => 'Guía de Remisión', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => false,'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '006', 'name' => 'Orden de Compra', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => false, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '007', 'name' => 'Pedido', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => false, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '008', 'name' => 'Solicitud de Compra', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => false, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '009', 'name' => 'Pedido', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => false, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '010', 'name' => 'Ingreso de Mercadería', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => false, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '011', 'name' => 'Devolución de Mercadería', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => true, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '012', 'name' => 'Factura Proveedor', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => true, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '013', 'name' => 'Oferta De Venta', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => false, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '014', 'name' => 'Pedido Cliente', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => false, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '015', 'name' => 'Retención en Compras', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => true, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '016', 'name' => 'Ventas Clientes Completa', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => true, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '017', 'name' => 'Cobro Clientes', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => false, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '018', 'name' => 'Pago Proveedor', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => false, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '019', 'name' => 'Nota Crédito Cliente', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => true, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '020', 'name' => 'Factura Stock Temporal', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => false, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '022', 'name' => 'Orden de Importación', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => false, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '023', 'name' => 'Importación', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => false, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')],
      ['code' => '024', 'name' => 'Nota Debito Cliente', 'companySerie' => '001', 'warehouseSerie' => '001', 'secuencial' => 0, 'isContable' => true, 'warehouse_id' => new MongoId('54e1a733b144fd6c0e000029')]
    ];

    foreach ($documents as $key => $document) {
      DocumentConfiguration::create($document);
    }

  }

}
