<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in(); // Ensure user is logged in

		$this->data['page_title'] = 'Inventory Management';
		// Load necessary models
		$this->load->model('model_inventory');
		$this->load->model('model_products'); // Needed for product list
		$this->load->model('model_inventory_locations'); // Needed for location list
        // Load users model if needed for permissions (it's usually loaded in Admin_Controller)
        // $this->load->model('model_users');
	}

	/*
	* Redirects to the manage inventory page
	*/
	public function index()
	{
		// Check if user has permission to view inventory
		if(!in_array('viewInventory', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		// Fetch inventory data using the model function
        $this->data['inventory_data'] = $this->model_inventory->getInventoryData();
        // Pass user permissions to the view (already available via $this->permission or $this->data['user_permission'])
        // $this->data['user_permission'] = $this->permission; // Already available in render_template

		// Render the page
		$this->render_template('inventory/index', $this->data);
	}

    /**
     * Handles displaying the stock adjustment form and processing the submission.
     */
    public function adjust()
    {
        // Check permission for creating/updating inventory
        if(!in_array('createInventory', $this->permission) && !in_array('updateInventory', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        $this->data['page_title'] = 'Adjust Stock';

        // Set validation rules
        $this->form_validation->set_rules('product', 'Product', 'trim|required|integer');
        $this->form_validation->set_rules('location', 'Location', 'trim|required|integer');
        $this->form_validation->set_rules('adjustment_type', 'Adjustment Type', 'trim|required|in_list[set,add,subtract]');
        $this->form_validation->set_rules('quantity', 'Quantity', 'trim|required|numeric');
        $this->form_validation->set_rules('unit_cost', 'Unit Cost', 'trim|numeric'); // Optional
        $this->form_validation->set_rules('reason', 'Reason/Note', 'trim'); // Optional

        if ($this->form_validation->run() == TRUE) {
            // Process the form data
            $product_id = (int)$this->input->post('product');
            $location_id = (int)$this->input->post('location');
            $adjustment_type = $this->input->post('adjustment_type');
            $quantity = (float)$this->input->post('quantity');
            $unit_cost = $this->input->post('unit_cost') ? (float)$this->input->post('unit_cost') : null;
            $reason = $this->input->post('reason'); // Optional reason

            // Call the model function to adjust stock
            $adjust = $this->model_inventory->adjustStock($product_id, $location_id, $adjustment_type, $quantity, $unit_cost, $reason);

            if($adjust) {
                $this->session->set_flashdata('success', 'Stock adjusted successfully.');
                redirect('inventory', 'refresh'); // Redirect back to inventory list
            } else {
                $this->session->set_flashdata('error', 'Error occurred while adjusting stock.');
                redirect('inventory/adjust', 'refresh'); // Redirect back to adjustment form
            }

        } else {
            // Display the form - Load necessary data for dropdowns
            $this->data['products'] = $this->model_products->getActiveProductData(); // Get active products
            $this->data['locations'] = $this->model_inventory_locations->getActiveInventoryLocations(); // Get active locations

            // Render the adjustment form view
            $this->render_template('inventory/adjust', $this->data);
        }
    }


    // Functions for editing, deleting stock will be added later
    // public function edit($id) { ... }
    // public function remove() { ... }
    // public function locations() { ... } // For managing locations if needed

}
