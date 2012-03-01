<?php
/**
 * OpenSQLManager
 *
 * Free Database manager for Open Source Databases
 *
 * @author 		Timothy J. Warren
 * @copyright	Copyright (c) 2012
 * @link 		https://github.com/aviat4ion/OpenSQLManager
 * @license 	http://philsturgeon.co.uk/code/dbad-license 
 */

// --------------------------------------------------------------------------

/**
 * Class to create ssh tunnels to connect to protected database servers
 */
class SSH {

	private $session, $stream;

	/**
	 * Constructor
	 * 
	 * @param string $host
	 * @param int $port
	 */
	public function __construct($host, $port=22)
	{
		$this->session =& ssh2_connect($host, $port);

		if($this->session === FALSE)
		{
			return FALSE;
		}

		return $this;
	}

	/**
	 * Create a tunnel using the current ssh connection
	 *
	 * @param string $host
	 * @param int $port
	 * @param string $auth_type
	 * @param array $auth_params
	 * @return stream
	 */
	public function tunnel($host, $port, $auth_type='password', $auth_params)
	{
		if($auth_type === 'password')
		{
			ssh2_auth_password($this->session, $auth_params['user'], $auth_params['pass']);
		}

		$this->stream =& ssh2_tunnel($this->session, $host, $port);

		return $this->stream;
	}
}