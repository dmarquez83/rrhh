<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class Authenticate {


	protected $auth;

	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	public function handle($request, Closure $next)
	{
    if ($this->auth->guest())
        {
            if ($request->ajax())
            {
                return redirect('/logout');
            }
            else
            {
                return redirect('/logout');
            }
    } else {
      $defaultCompany = Session::get('currentCompany');

      if ($defaultCompany['databaseName'] != 'sae') {

        Config::set('database.connections.' . $defaultCompany['databaseName'], [
          'driver'   => 'mongodb',
          'host'     => 'localhost',
          'port'     => 27017,
          'username' => '',
          'password' => '',
          'database' => $defaultCompany['databaseName']
        ]);

        Config::set('database.default', $defaultCompany['databaseName']);
        return $next($request);
      }
    }

		return $next($request);
	}

}
