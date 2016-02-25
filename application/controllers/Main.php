<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */

	public function __construct() {
		parent::__construct();
		$this->load->library('cTFUtil');
//		header("HTTP/1.1 200 OK");
		$this->load->library('encrypt');
		$this->load->library('session');
	}

	public function index()
	{
		$this->load->view('Main/index.php');
//		$this->load->view('Main/test.php');
	}

	public function test()
	{
		$this->load->view('Main/index.php');
	}
	

	public function auth()
	{
		$this->load->view('Auth/check.php');
	}

}
