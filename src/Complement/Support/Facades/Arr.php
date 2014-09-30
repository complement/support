<?php

namespace Complement\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Arr extends Facade
{

	protected static function getFacadeAccessor()
	{
		return 'arr';
	}

}
