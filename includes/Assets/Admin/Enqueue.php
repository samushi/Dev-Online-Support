<?php

/**
 *	Enqueue the scripts & styles class
 *
 * @package   	Admin\Enqueue
 * @author    	Sami Maxhuni <samimaxhuni510@gmail.com>
 * @license   	GPL-2.0+
 * @link      	http://devsolution.info
 * @copyright 	2017 Sami Maxhuni
 **/

namespace DevSupport\Assets\Admin;

class Enqueue {

	/**
	 * Styles Directory path
	 *
	 * @var    string
	 * @since  1.0.0
	 */

	protected $styles_directory  = 'assets/css/';

	/**
	 * Scripts Directory path
	 *
	 * @var    string
	 * @since  1.0.0
	 */

	protected $scripts_directory = 'assets/js/';

	/**
	 * List of styles
	 *
	 * @var    array
	 * @since  1.0.0
	 */

	public $styles = array(
								'dev_admin_core' => array('filename' => 'admin', 'version' => '1.0.0', 'deps' => false, 'media' => 'all')
							 );

	/**
	 * List of Scripts
	 *
	 * @var    array
	 * @since  1.0.0
	 */

	public $scripts = array(
								'dev_admin_core' => array('filename' => 'admin', 'version' => '1.0.0', 'deps' => array('jquery'), 'footer' => true)
							  );

	/**
	 * Initialize the class
	 *
	 * @since  1.0.0
	 */

	public function __construct(){


		add_action( 'admin_enqueue_scripts', array($this, 'register_styles') );
		add_action( 'admin_enqueue_scripts', array($this, 'register_scripts') );
	}

	/**
	 * Register all styles
	 *
	 * @since  1.0.0
	 */

	public function register_styles($hook){

		$this->styles = apply_filters('dev_admin_styles', $this->styles);

		if(!empty($this->styles && is_array($this->styles))){
			foreach($this->styles as $key => $file){
				$file = (object) $file;
				wp_register_style( $key, $this->generate_url($file->filename, 'style'), $file->deps, $file->version, $file->media );
				wp_enqueue_style($key);
			}
		}
	}

	/**
	 * Register all scripts
	 *
	 * @since  1.0.0
	 */

	public function register_scripts($hook){

		$this->scripts = apply_filters('dev_admin_styles', $this->scripts);

		if(!empty($this->scripts && is_array($this->scripts))){
			foreach($this->scripts as $key => $file){
				$file = (object) $file;
				wp_register_script( $key, $this->generate_url($file->filename, 'script'), $file->deps, $file->version, $file->footer);
				wp_enqueue_script( $key );
			}
		}
	}

	/**
	 * Generate Link for assets
	 *
	 * @return url
	 * @since  1.0.0
	 */

	public function generate_url($file, $type = 'style'){
		if(!empty($file)){
			$file_name = trim($file);
			$file_path = '';

			$file_path = $this->check_file($file, $type);

			return $file_path;
		}else{
			return false;
		}
	}

	/**
	 * Check if exist file
	 *
	 * @return url
	 * @since  1.0.0
	 */

	private function check_file($file, $type = 'style'){
		if($type == 'style'){
			$file_path = DEV_PATH . $this->styles_directory . '/'.$file . '.css';
		}elseif($type == 'script'){
			$file_path = DEV_PATH . $this->scripts_directory . '/'.$file . '.js';
		}

		if(file_exists($file_path)){
			if($type == 'style'){
				$file_path = DEV_URL . $this->styles_directory . '/'.$file . '.css';
			}

			if($type == 'script'){
				$file_path = DEV_URL . $this->scripts_directory . '/'.$file . '.js';
			}
		}else{
			$file_path = '/'.$file;
		}

		return $file_path;
	}


}