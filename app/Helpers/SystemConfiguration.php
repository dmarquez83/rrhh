<?php namespace App\Helpers;

use App\Models\GeneralParameter;
use App\Models\CompanyInfo;
use Illuminate\Support\Facades\Session;

class SystemConfiguration {

  public static function isAccountingEnabled()
  {
    $parameter = GeneralParameter::where("code", '=', "SaeAccounting")->first();
    return $parameter->alfanumericValue == "1";
  }

  public static function isElectronicDocumentsEnabled()
  {
    //$parameter = GeneralParameter::where("code", '=', "FEEnabled")->first();
    return true;
  }

  private static function getCompanyFolderName()
  {
    $companyInfo = Session::get('companyInformation');
    if (!$companyInfo) {
      $companyInfo = self::getCompanyInfo();
    }
    $companyName = $companyInfo['businessName'];
    $companyIdentification = $companyInfo['identification'];
    $companyFolder = strtolower(self::cleanString(trim($companyName))).$companyIdentification;
    return 'companiesFiles/'.$companyFolder;
  }

  private static function getCompanyInfo()
  {
    $companyData = CompanyInfo::first();
    if ($companyData) {
      return $companyData->toArray();
    }
    return null;
  }

  public static function getPublicCompanyPath($fullPath = false)
  {
    $companyFolder = self::getCompanyFolderName();
    if ($fullPath){
      return public_path().'/'.$companyFolder.'/';
    }
    return 'public/'.$companyFolder.'/';
  }

  public static function getPrivateCompanyPath($fullPath = false)
  {
    $companyFolder = self::getCompanyFolderName();
    if ($fullPath){
      return storage_path().'/'.$companyFolder.'/';
    }
    return 'storage/'.$companyFolder.'/';
  }

  public static function getElectronicDocumentsPath($fullPath = false)
  {
    $basepath = self::getPrivateCompanyPath($fullPath);
    return $basepath.'electronicDocuments/';
  }

  public static function getImagesCompanyPath($fullPath = false)
  {
    $basepath = self::getPrivateCompanyPath($fullPath);
    return $basepath.'images/';
  }

  public static function getSignatureCompanyPath($fullPath = false)
  {
    $basepath = self::getPrivateCompanyPath($fullPath);
    return $basepath.'signature/';
  }

  public static function getTributationDocumentsPaths($fullPath = false)
  {
    $basepath = self::getPrivateCompanyPath($fullPath);
    return $basepath.'tributationDocuments/';
  }

  public static function createCompanyPaths($companyName, $companyIdentification)
  {
    $companyFolder = strtolower(self::cleanString(trim($companyName))).$companyIdentification;
    \Storage::makeDirectory(public_path().'/'.$companyFolder);
    \Storage::makeDirectory(public_path().'/'.$companyFolder.'/prints');
    \Storage::makeDirectory(storage_path().'/'.$companyFolder.'/electronicDocuments');
    \Storage::makeDirectory(storage_path().'/'.$companyFolder.'/images');
    \Storage::makeDirectory(storage_path().'/'.$companyFolder.'/signature');
    \Storage::makeDirectory(storage_path().'/'.$companyFolder.'/tributationDocuments');
    \Storage::makeDirectory(storage_path().'/'.$companyFolder.'/prints');
    $companyInfo = ['businessName' => $companyName, 'identification' => $companyIdentification];
    Session::put('companyInformation', $companyInfo);
  }

  private static function cleanString($text)
  {
    $cleanText = preg_replace('([^A-Za-z0-9])', '', $text);
    return $cleanText;
  }

}
