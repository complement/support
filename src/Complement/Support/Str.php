<?php

namespace Complement\Support;

class Str extends \Illuminate\Support\Str
{

	protected $_cipher;
	protected $_mode;
	protected $_key;

	public function __construct( $app )
	{
		$this->_cipher = MCRYPT_DES;
		$this->_mode = MCRYPT_MODE_NOFB;

		$key = $app[ 'config' ][ 'app.key' ];
		$size = mcrypt_get_key_size( $this->_cipher, $this->_mode );

		if ( isset( $key[ $size ] ) )
		{
			$key = base_convert( crc32( $key ), 10, 36 );

			if ( isset( $key[ $size ] ) )
			{
				$key = substr( $key, 0, $size );
			}
		}

		$this->_key = $key;
	}

	public function encrypt( $value )
	{
		switch ( true )
		{
			case defined( 'MCRYPT_DEV_URANDOM' ):
				$randomizer = MCRYPT_DEV_URANDOM;
				break;
			case defined( 'MCRYPT_DEV_RANDOM' ):
				$randomizer = MCRYPT_DEV_RANDOM;
				break;
			default:
				$randomizer = MCRYPT_RAND;
				break;
		}

		$iv = mcrypt_create_iv( mcrypt_get_iv_size( $this->_cipher, $this->_mode ), $randomizer );
		$data = mcrypt_encrypt( $this->_cipher, $this->_key, $value, $this->_mode, $iv );
		$hash = base_convert( crc32( $iv . $data . $this->_key ), 10, 36 );

		return implode( '.', array_map( 'static::base64UrlEncode', array( $hash, $iv, $data ) ) );
	}

	public function decrypt( $value )
	{
		$value = explode( '.', $value );

		if ( $value && isset( $value[ 0 ] ) && isset( $value[ 1 ] ) && isset( $value[ 2 ] ) )
		{
			list( $hash, $iv, $data ) = array_map( 'static::base64UrlDecode', $value );

			if ( $hash == base_convert( crc32( $iv . $data . $this->_key ), 10, 36 ) )
			{
				return rtrim( mcrypt_decrypt( $this->_cipher, $this->_key, $data, $this->_mode, $iv ), "\0" );
			}
		}

		return false;
	}

	protected static function base64UrlDecode( $value )
	{
		return base64_decode( strtr( $value, '-_', '+/' ) );
	}

	protected static function base64UrlEncode( $value )
	{
		return str_replace( '=', '', strtr( base64_encode( $value ), '+/', '-_' ) );
	}

	public function __call( $method, $parameters )
	{
		return static::__callStatic( $method, $parameters );
	}

}
