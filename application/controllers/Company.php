<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Company extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Company';

		$this->load->library('upload'); // Load upload library
		$this->load->model('model_company');
		$this->load->model('model_users'); // Keep if needed for currency or other logic
	}

    /*
    * It redirects to the company page and displays the company information
    * It also updates the company information into the database if found data
    */
	public function index()
	{
		if(!in_array('updateCompany', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->form_validation->set_rules('company_name', 'Company name', 'trim|required');
        $this->form_validation->set_rules('service_charge_value', 'Charge Amount (%)', 'trim|integer');
        $this->form_validation->set_rules('vat_charge_value', 'Vat Charge (%)', 'trim|integer');
        $this->form_validation->set_rules('address', 'Address', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
        $this->form_validation->set_rules('country', 'Country', 'trim|required');
        $this->form_validation->set_rules('message', 'Message', 'trim|required');
        $this->form_validation->set_rules('currency', 'Currency', 'trim|required');

		// Configuration for logo upload
		$config['upload_path'] = './assets/images/company_logo/'; // Make sure this folder exists and is writable
		$config['allowed_types'] = 'gif|jpg|png|jpeg|ico|webp'; // Allowed image types (added webp)
		$config['max_size'] = '2048'; // Max size in KB (2MB) - Increased size
		$config['max_width'] = '2048'; // Max width - Increased size
		$config['max_height'] = '2048'; // Max height - Increased size
		$config['encrypt_name'] = TRUE; // Encrypt filename for security

		$this->upload->initialize($config);

		$logo_filename = null; // Initialize logo filename
		$upload_error = null; // Initialize upload error

		// Check if a logo file is selected for upload
		if (isset($_FILES['logo']) && !empty($_FILES['logo']['name'])) {
			if (!$this->upload->do_upload('logo')) {
				$upload_error = $this->upload->display_errors('', ''); // Get plain error message
				// Set flashdata error for logo upload failure
				$this->session->set_flashdata('error', 'Logo upload failed: ' . $upload_error);
				// Don't proceed with DB update if logo upload fails but was attempted
                // Redirect back to the form to show the error
                // Fetch existing data again to display in the form
                $this->data['currency_symbols'] = $this->currency();
	        	$this->data['company_data'] = $this->model_company->getCompanyData(1);
				$this->render_template('company/index', $this->data);
                return; // Stop execution here
			} else {
				$upload_data = $this->upload->data();
				$logo_filename = $upload_data['file_name'];
			}
		}

        // Proceed only if form validation passes AND there was no logo upload error (or no logo was uploaded)
        if ($this->form_validation->run() == TRUE && $upload_error === null) {
            // true case
        	$data = array(
        		'company_name' => $this->input->post('company_name'),
        		// 'logo' will be handled separately by the model if $logo_filename is set
        		'charge_amount' => $this->input->post('service_charge_value'), // Corrected field name
        		'vat_charge' => $this->input->post('vat_charge_value'),       // Corrected field name
        		'address' => $this->input->post('address'),
        		'phone' => $this->input->post('phone'),
        		'country' => $this->input->post('country'),
        		'message' => $this->input->post('message'),
        		'currency' => $this->input->post('currency')
        	);

			// Pass the logo filename (or null) to the model's update method
        	$update = $this->model_company->update($data, 1, $logo_filename);
        	if($update == true) {
        		$this->session->set_flashdata('success', 'Successfully updated');
        	}
        	else {
        		$this->session->set_flashdata('error', 'Error occurred!!');
        	}
        	redirect('company/', 'refresh');
        }
        else {
            // false case (validation failed OR upload error occurred before validation check)
            // If it's not an upload error case (which redirects above), it's a validation error.
            if ($upload_error === null) {
                // Validation errors will be shown automatically by CodeIgniter
            }
			$this->data['currency_symbols'] = $this->currency();
        	$this->data['company_data'] = $this->model_company->getCompanyData(1);
			$this->render_template('company/index', $this->data);
        }

	}

	// Function to get currency symbols (assuming it exists or you add it)
	public function currency() {
        // Example: Replace with your actual currency fetching logic if needed
        // This might come from a config file, database, or helper
        return array(
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'TZS' => 'TSh', // Example for Tanzanian Shilling
            // Add other currencies as needed
        );
    }

}
