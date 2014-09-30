<?php

namespace Complement\Support;

class Arr
{

	protected $_app;
	protected static $macros = array(
		'add'		 => 'array_add',
		'divide'	 => 'array_divide',
		'dot'		 => 'array_dot',
		'except'	 => 'array_except',
		'fetch'		 => 'array_fetch',
		'first'		 => 'array_first',
		'last'		 => 'array_last',
		'flatten'	 => 'array_flatten',
		'forget'	 => 'array_forget',
		'get'		 => 'array_get',
		'only'		 => 'array_only',
		'pluck'		 => 'array_pluck',
		'pull'		 => 'array_pull',
		'set'		 => 'array_set',
		'sort'		 => 'array_sort',
		'where'		 => 'array_where',
	);

	public function __construct( $app )
	{
		$this->_app = $app;
	}

	public static function isAssoc( array $array )
	{
		$keys = array_keys( $array );

		return array_keys( $keys ) !== $keys;
	}

	public static function merge( $array1, $array2 )
	{
		if ( static::isAssoc( $array2 ) )
		{
			foreach ( $array2 AS $key => $value )
			{
				if ( is_array( $value ) && isset( $array1[ $key ] ) && is_array( $array1[ $key ] ) )
				{
					$array1[ $key ] = static::merge( $array1[ $key ], $value );
				}
				else
				{
					$array1[ $key ] = $value;
				}
			}
		}
		else
		{
			foreach ( $array2 AS $value )
			{
				if ( !in_array( $value, $array1, true ) )
				{
					$array1[] = $value;
				}
			}
		}

		if ( func_num_args() > 2 )
		{
			foreach ( array_slice( func_get_args(), 2 ) AS $array2 )
			{
				if ( static::is_assoc( $array2 ) )
				{
					foreach ( $array2 AS $key => $value )
					{
						if ( is_array( $value ) && isset( $array1[ $key ] ) && is_array( $array1[ $key ] ) )
						{
							$array1[ $key ] = static::merge( $array1[ $key ], $value );
						}
						else
						{
							$array1[ $key ] = $value;
						}
					}
				}
				else
				{
					foreach ( $array2 AS $value )
					{
						if ( !in_array( $value, $array1, true ) )
						{
							$array1[] = $value;
						}
					}
				}
			}
		}

		return $array1;
	}

	public static function jsonEncode( $value )
	{
		if ( is_array( $value ) )
		{
			$value = json_encode( $value );

			if ( $value )
			{
				return $value;
			}
		}

		return '[]';
	}

	public static function jsonDecode( $value )
	{
		$value = json_decode( $value, true );

		if ( $value )
		{
			if ( is_array( $value ) )
			{
				return $value;
			}
		}

		return array();
	}

	public function encrypt( $value )
	{
		return $this->_app->str->encrypt( static::jsonEncode( $value ) );
	}

	public function decrypt( $value )
	{
		return static::jsonDecode( $this->_app->str->decrypt( $value ) );
	}

	public static function macro( $name, $macro )
	{
		static::$macros[ $name ] = $macro;
	}

	public static function __callStatic( $method, $parameters )
	{
		if ( isset( static::$macros[ $method ] ) )
		{
			return call_user_func_array( static::$macros[ $method ], $parameters );
		}

		throw new \BadMethodCallException( 'Method ' . $method . ' does not exist.' );
	}

	public function __call( $method, $parameters )
	{
		return static::__callStatic( $method, $parameters );
	}

}
