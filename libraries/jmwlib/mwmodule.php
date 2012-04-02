<?php
/**
 * @version		$Id:$
 * @package		jmwlib
 * @copyright	Copyright (C) 2010 Chris Davenport. All rights reserved.
 * @license		GNU/GPL version 2 or later.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * MediaWiki API module.
 *
 */
class jmwModule extends JObject
{
	/**
	 * Valid parameters.
	 */
	protected $valid_parms = array(
		'format',
		'action',
		'version',
		'maxlag',
		'smaxage',
		'maxage',
		'requestid',
	);

	/**
	 * Valid additional parameters.
	 *
	 * Must be overridden in child classes.
	 */
	protected $valid_extra_parms = array();

	/**
	 * Valid formats.
	 */
	private $valid_formats = array(
		'json', 'jsonfm', 'php', 'phpfm', 'wddx', 'wddxfm', 'xml', 'xmlfm',
		'yaml', 'yamlfm', 'rawfm', 'txt', 'txtfm', 'dbg', 'dbgfm'
	);

	/**
	 * Request arguments.
	 * These will be appended to the query part of the request URI.
	 */
	protected $args = array();

	/**
	 * Returned data.
	 */
	protected $data = array();

	/**
	 * Constructor.
	 *
	 * @param	string	Format.
	 */
	public function __construct( $format = 'php' )
	{
		if (in_array( $format, $this->valid_formats )) {
			$this->args['format'] = $format;
		}
		else {
			throw new jmwlibModuleException( 'Invalid format requested: ' . $format );
		}

	}

	/**
	 * Make the API call to the wiki.
	 *
	 * @param	object	jmwWiki object.
	 * @return	object	This object for method chaining.
	 */
	public function call( jmwWiki $wiki )
	{
		try {
			// Make the call.
			$wiki->call( $this );

			// Decode the response.
			$data = unserialize( $wiki->getData() );

			// Check for error conditions.
			if (isset( $data['error'] )) {
				$error = $data['error'];
				throw new jmwlibModuleException( 'jmwlib error ('.$error['code'].') '.$error['info'] );
			}

			// Check for warning conditions.
			if (isset( $data['warning'] )) {
				$error = $data['warning'];
				throw new jmwlibModuleException( 'jmwlib warning ('.$error['code'].') '.$error['info'] );
			}

			// Check that correct data has been returned.
			if (!isset( $this->args['action'] )) {
				throw new jmwlibModuleException( 'jmwlib data error' );
			}

			// Save the returned data (dereferenced).
//			$this->data = $data[$this->args['action']];
			$this->data = $data;
		}

		catch (jmwlibHttpException $e) {

			if ($e->getCode() == 0) {
				// A zero indicates a connection problem (eg. connection timed out).
				$message = 'Connection problem: '.$e->getMessage();
			}
			else {
				// A non-zero code indicates the HTTP server response code.
				$message = 'Server error response: '.$e->getCode().' '.$e->getMessage();
			}

			throw new jmwlibModuleException( $message, $e->getCode() );
		}

		return $this;
	}

	/**
	 * Returns array of module arguments.
	 *
	 * @return	array	Array of module arguments.
	 */
	public function getArgs()
	{
		return $this->args;
	}

	/**
	 * Returns array of data returned by the API call.
	 *
	 * @return	array	Array of data elements.
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Magic getter.
	 *
	 * @param	string	Name of a property of this object.
	 * @return	mixed	Current value of the property.
	 */
	public function __get( $name )
	{
		if (isset( $this->data[$name] )) {
			return $this->data[$name];
		}

		if (!in_array( $name, $this->valid_extra_parms ) &&
		    !in_array( $name, $this->valid_parms )) {
			throw new jmwlibModuleException( 'Undefined property via __get(): ' . $name );
		}

	   	return $this->args[$name];
	}

	/**
	 * Magic setter.
	 *
	 * @param	string	Name of a property of this object.
	 * @param	mixed	Value to set the property to.
	 * @return	object	This object for method chaining.
	 */
	public function __set( $name, $value )
	{
		// Valid wiki API actions.
		// Each of these actions is implemented as a derived class in the modules directory.
		static $valid_actions = array(
			'sitematrix', 'flagconfig', 'review', 'opensearch', 'stabilize', 'login', 'logout', 'query', 'expandtemplates',
			'parse', 'feedwatchlist', 'help', 'paraminfo', 'purge', 'rollback', 'delete', 'undelete', 'protect', 'block',
			'unblock', 'move', 'edit', 'upload', 'emailuser', 'watch', 'patrol', 'import', 'userrights'
		);

		switch ($name) {
			case 'format':
				if (!in_array( $value, $this->valid_formats )) {
					throw new jmwlibModuleException( 'Invalid format requested: ' . $value );
				}
				$this->parms[$name] = $value;
				break;

			case 'action':
				if (!in_array( $value, $valid_actions )) {
					throw new jmwlibModuleException( 'Invalid action requested: ' . $value );
				}
				$this->args[$name] = $value;
				break;

			default:
				if (!in_array( $name, $this->valid_extra_parms ) &&
				    !in_array( $name, $this->valid_parms )) {
				    throw new jmwlibModuleException( 'Undefined property via __set(): ' . $name );
				}
				$this->args[$name] = $value;
				break;
		}

		return $this;
	}

	/**
	 * Magic method.
	 *
	 * @param	string	Name of a non-existent method.
	 * @param	array	Array of arguments that were passed to the method.
	 * @return	object	This object for method chaining.
	 */
	public function __call( $name, $args )
	{
		// If method name matches a valid object property, then treat it as a setter.
		if (!in_array( $name, $this->valid_extra_parms ) &&
		    !in_array( $name, $this->valid_parms )) {
		    throw new jmwlibModuleException( 'Undefined method via __call(): ' . $name );
		}

		$this->args[$name] = $args[0];
	    return $this;
	}

	/**
	 * Magic to string method.
	 *
	 * @return	string	Request query string.
	 */
	public function __toString()
	{
		$query = array();
		foreach ($this->args as $name => $value) {
			$query[] = $name . '=' . $value;
		}

		return implode( '&', $query );
	}

}

class jmwlibModuleException extends Exception {}
