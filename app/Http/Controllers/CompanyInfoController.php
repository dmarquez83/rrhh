<?php namespace App\Http\Controllers;

use App\Models\CompanyInfo;
use App\Models\Warehouse;
use App\Models\DocumentConfiguration;
use Illuminate\Support\Facades\Input;
use App\Helpers\SystemConfiguration;
use App\Helpers\ResultMsgMaker;

/*
|--------------------------------------------------------------------------
| DONT USER WAREHOUSE for CompanyInfo Model
|--------------------------------------------------------------------------
*/

class CompanyInfoController extends Controller {

  public function index()
  {
    $companyInfo = CompanyInfo::first();
    return $companyInfo;
  }

  public function store()
  {
    $companyInfo = Input::all();
    if (isset($companyInfo['logo'])){
      $this->saveCompanyLogo($companyInfo['logo']['src'], $companyInfo['businessName'], $companyInfo['identification']);
    }
    if (isset($companyInfo['signature'])) {
      $this->saveSignature($companyInfo['signature'], $companyInfo['businessName'], $companyInfo['identification']);
      unset($companyInfo['signature']);
    }
    if (isset($companyInfo['documentSecuencial'])) {
      $this->updateDocumentsSecuential($companyInfo['documentSecuencial']);
      unset($companyInfo['documentSecuencial']);
    }
    if (CompanyInfo::create($companyInfo)) {
      $this->updateWarehouseSecuencial($companyInfo);
      return ResultMsgMaker::saveSuccess();
    }else{
      return ResultMsgMaker::error();
    }
  }

  private function saveCompanyLogo($logo)
  {
    $filePath = SystemConfiguration::getImagesCompanyPath().'companyLogo.png';
    $logoFile = str_replace('data:image/png;base64,','', $logo);
    $logoFile = str_replace('data:image/jpeg;base64,','', $logo);
    $logoFile = base64_decode($logoFile);
    \Storage::put($filePath, $logoFile);
    return $filePath;
  }

  private function saveSignature($signature)
  {
    $filePath = SystemConfiguration::getSignatureCompanyPath().'signature.p12';
    $signatureFile = base64_decode(str_replace('data:application/x-pkcs12;base64,','', $signature));
    \Storage::put($filePath, $signatureFile);
    return $filePath;
  }

  private function updateDocumentsSecuential($documentSecuentials)
  {
    if (isset($documentSecuentials['invoice'])) {
      $document = DocumentConfiguration::warehouse()->where('code', '001')->first();
      $document->secuencial = $documentSecuentials['invoice']['secuencial'] - 1;
      $document->save();
    }
    if (isset($documentSecuentials['creditNote'])) {
      $document = DocumentConfiguration::warehouse()->where('code', '019')->first();
      $document->secuencial = $documentSecuentials['creditNote']['secuencial'] - 1;
      $document->save();
    }
    if (isset($documentSecuentials['debitNote'])) {
      $document = DocumentConfiguration::warehouse()->where('code', '024')->first();
      $document->secuencial = $documentSecuentials['debitNote']['secuencial'] - 1;
      $document->save();
    }
    if (isset($documentSecuentials['retention'])) {
      $document = DocumentConfiguration::warehouse()->where('code', '015')->first();
      $document->secuencial = $documentSecuentials['retention']['secuencial'] - 1;
      $document->save();
    }
    if (isset($documentSecuentials['remisionGuide'])) {
      $document = DocumentConfiguration::warehouse()->where('code', '005')->first();
      $document->secuencial = $documentSecuentials['remisionGuide']['secuencial'] - 1;
      $document->save();
    }
  }

  private function updateWarehouseSecuencial($companyInfo)
  {
    $warehouse = Warehouse::first();
    $warehouse->code = $companyInfo['companyCode'];
    //$warehouse->series = $companyInfo['emisionPoint'];
    $warehouse->save();
  }

  public function update($id)
  {
    $savedCompanyInfo = CompanyInfo::find($id)->first();
    $companyInfo = Input::all();

    if (isset($companyInfo['logo'])) {
      $this->saveCompanyLogo($companyInfo['logo']['src']);
    }

    if (isset($companyInfo['signature'])) {
      $this->saveSignature($companyInfo['signature']);
      unset($companyInfo['signature']);
    }

    if ($savedCompanyInfo->update($companyInfo)) {
      return ResultMsgMaker::saveSuccess();
    } else {
      return ResultMsgMaker::error();
    }
  }

  public function validateDigitalSignature()
  {
    $companyInfo = Input::all();
    SystemConfiguration::createCompanyPaths($companyInfo['companyName'], $companyInfo['companyIdentification']);
    if (isset($companyInfo['signature'])){
      $path = $this->saveSignature($companyInfo['signature']);
      $almacén_cert = file_get_contents(base_path()."/".$path);
      $info_cert = null;
      if (openssl_pkcs12_read($almacén_cert, $info_cert, $companyInfo['password'])) {
        return ResultMsgMaker::successCustom("El certificado es correcto");
      } else {
        return ResultMsgMaker::errorCustom("Error en abrir el certificado");
      }
    } else {
      return ResultMsgMaker::saveSuccess();
    }
  }

  private function cleanString($texto)
  {
    $textoLimpio = preg_replace('([^A-Za-z0-9])', '', $texto);
    return $textoLimpio;
  }

}
