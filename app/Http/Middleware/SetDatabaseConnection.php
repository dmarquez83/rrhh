<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class SetDatabaseConnection {

	public function handle($request, Closure $next)
	{
    $defaultCompany = Session::get('currentCompany');

    if ($defaultCompany!= null) {

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
      }
      return redirect('/dashboard');

    } else {
      return redirect('/dashboard');
    }


		return $next($request);
	}

}
