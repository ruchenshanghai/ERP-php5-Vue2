<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Loader extends CI_Loader {

	public function __construct(){
		parent::__construct();
	}
	public function setpath(){
        if(defined('BROWSER')&&BROWSER=='mobile') {
            $this->_ci_library_paths = array(APPPATH, BASEPATH);
            $this->_ci_helper_paths = array(APPPATH, BASEPATH);
            $this->_ci_model_paths = array(APPPATH);
            $this->_ci_view_paths = array('../'.BROWSER.'/views/'	=> TRUE);
        }
        else
        {
            parent::setpath();
        }
	}
}
