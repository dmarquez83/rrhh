<?php namespace App\Exceptions;

use App\Helpers\ResultMsgMaker;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Lang;

class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		'Symfony\Component\HttpKernel\Exception\HttpException'
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		return parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e)
	{
		if ($this->isHttpException($e))
		{
			return $this->renderHttpException($e);
		}
    elseif ($e instanceof \MongoWriteConcernException) {

      $errorMessage = $e->getMessage();
      $indexQuotes = strpos($errorMessage, "\"") + 1;
      $lastIndexQuotes = strrpos($errorMessage, "\"") - $indexQuotes;
      $value = substr($errorMessage, $indexQuotes, $lastIndexQuotes);
			$comaPositionInValue = strpos($value, ",");
			if($comaPositionInValue != -1){
				$lastIndexQuotes = strpos($value, "\"");
				$value = substr($value, 0, $lastIndexQuotes);
			}

      $indexQuotesField = strpos($errorMessage, "$") + 1;
      $lengthField = strpos($errorMessage, "_", $indexQuotesField) - $indexQuotesField;
      $field = substr($errorMessage, $indexQuotesField, $lengthField);

      $field = Lang::get('collectionFields.'.$field);

      return response()->json(ResultMsgMaker::warningDuplicateField(' ', $field, $value));
			

    }
		else
		{
			return parent::render($request, $e);
		}
	}

}
