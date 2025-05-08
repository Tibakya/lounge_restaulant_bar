<?php

class Model_company extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* get the company data */
	public function getCompanyData($id = null)
	{
		if($id) {
			// Select all necessary columns including the new logo column
			$sql = "SELECT id, company_name, charge_amount, vat_charge, address, phone, country, message, currency, logo FROM company WHERE id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}
		// Return null or handle error if no ID is provided for single fetch
		return null;
	}

	public function update($data, $id, $logo_filename = null) // Added $logo_filename parameter
	{
		if($data && $id) {
			// Fetch current data to check for old logo
			// Ensure getCompanyData is called correctly
			$current_data = $this->getCompanyData($id);
			$old_logo = ($current_data && isset($current_data['logo'])) ? $current_data['logo'] : null;

			// If a new logo filename is provided, add it to the data array
			if ($logo_filename !== null) {
				$data['logo'] = $logo_filename;
				// Delete the old logo file if it exists and is different from the new one
				if ($old_logo && $old_logo != $logo_filename && file_exists('./assets/images/company_logo/' . $old_logo)) {
					@unlink('./assets/images/company_logo/' . $old_logo);
				}
			}

			$this->db->where('id', $id);
			$update = $this->db->update('company', $data);
			return ($update == true) ? true : false;
		}
		return false; // Return false if no data or ID
	}


}
