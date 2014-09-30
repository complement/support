<?php

namespace Complement\Support;

use Illuminate\Support\ServiceProvider;

class SupportServiceProvider extends ServiceProvider
{

	protected $defer = false;

	public function register()
	{
		$this->app->bindShared( 'str', function( $app )
		{
			return new Str( $app );
		} );

		$this->app->bindShared( 'arr', function( $app )
		{
			return new Arr( $app );
		} );
	}

	public function provides()
	{
		return array();
	}

}
