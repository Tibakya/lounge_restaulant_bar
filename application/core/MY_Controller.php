<?php

class MY_Controller extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
}

class Admin_Controller extends MY_Controller
{
	var $permission = array();

	public function __construct()
	{
		parent::__construct();

		$group_data = array();
		if(empty($this->session->userdata('logged_in'))) {
			$session_data = array('logged_in' => FALSE);
			$this->session->set_userdata($session_data);
		}
		else {
			$user_id = $this->session->userdata('id');
			$this->load->model('model_groups');
			$group_data = $this->model_groups->getUserGroupByUserId($user_id);

			$this->data['user_permission'] = unserialize($group_data['permission']);
			$this->permission = unserialize($group_data['permission']);
		}
	}

	public function logged_in()
	{
		$session_data = $this->session->userdata();
		if($session_data['logged_in'] == TRUE) {
			redirect('dashboard', 'refresh');
		}
	}

	public function not_logged_in()
	{
		$session_data = $this->session->userdata();
		if($session_data['logged_in'] == FALSE) {
			redirect('auth/login', 'refresh');
		}
	}

	public function render_template($page = null, $data = array())
	{
		// Load company model if not already loaded (optional, good practice)
		if (!isset($this->model_company)) {
			$this->load->model('model_company');
		}
		// Fetch company info for logo and name
		$company_info = $this->model_company->getCompanyData(1); // Assuming company ID is 1
		// Construct full logo URL only if logo exists and file exists
        $logo_path = ($company_info && $company_info['logo']) ? './assets/images/company_logo/' . $company_info['logo'] : null;
        $data['company_logo'] = ($logo_path && file_exists($logo_path)) ? base_url('assets/images/company_logo/' . $company_info['logo']) : null;
		$data['company_brand_name'] = ($company_info && $company_info['company_name']) ? $company_info['company_name'] : 'RestaurantSystem'; // Use company name or default

		$this->load->view('templates/header',$data);
		$this->load->view('templates/header_menu',$data);
		// $this->load->view('templates/side_menubar',$data); // Sidebar is commented out
		$this->load->view($page, $data);
		$this->load->view('templates/footer',$data);
	}

	/**
	 * Fetches the company currency symbol.
	 * Loads the company model if needed.
	 * @return string Currency symbol (e.g., '$') or empty string.
	 */
	public function company_currency()
	{
		if (!isset($this->model_company)) {
			$this->load->model('model_company');
		}
		$company_data = $this->model_company->getCompanyData(1); // Assuming company ID is 1
		// You might need a mapping from currency code (e.g., 'USD') to symbol ('$')
		// For now, let's assume the 'currency' field stores the symbol directly or we return the code.
		return ($company_data && isset($company_data['currency'])) ? $company_data['currency'] : ''; // Adjust if 'currency' stores code not symbol
	}

}
