<?php

class Model_orders extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_products');
		$this->load->model('model_inventory'); // Load inventory model
		$this->load->model('model_company'); // Load company model for create_ajax
	}

	/*
	*	This function fetches the orders data from the table orders
	*/
	public function getOrdersData($id = null, $limit = null) // Added limit parameter
	{
		$this->db->select('orders.*, 
						   u.firstname as waiter_firstname, u.lastname as waiter_lastname,
						   t.table_name'); // Added table_name
		$this->db->from('orders');
		$this->db->join('users u', 'orders.waiter_id = u.id', 'left');
		$this->db->join('tables t', 'orders.table_id = t.id', 'left'); // Join with tables

		if($id) {
			$this->db->where('orders.id', $id);
			$query = $this->db->get();
			return $query->row_array();
		}

		$this->db->order_by('orders.id', 'DESC');
		if ($limit) { // Apply limit if provided
			$this->db->limit($limit);
		}
		$query = $this->db->get();
		return $query->result_array();
	}
	public function countTotalPaidOrders()
	{
		$sql = "SELECT COUNT(*) as total_paid FROM orders WHERE paid_status = 1";
		$query = $this->db->query($sql);
		$result = $query->row_array();
		return (int) $result['total_paid']; // Return the count as an integer
	}
	public function countTotalUnPaidOrders()
	{
		$sql = "SELECT COUNT(*) as total_unpaid FROM orders WHERE paid_status = 2";
		$query = $this->db->query($sql);
		$result = $query->row_array();
		return (int) $result['total_unpaid'];
	}
	public function countTotalOrders()
	{
		$sql = "SELECT COUNT(*) as total_orders FROM orders";
		$query = $this->db->query($sql);
		$result = $query->row_array();
		return (int) $result['total_orders'];
	}


	/*
	* Insert the orders data into the database
	* It inserts the data into the orders and order_items table
	* and returns the id of the order table.
	*/
	// Original create function for traditional form submission
	public function create()
	{
		$user_id = $this->session->userdata('id');
		$bill_no = 'BIL'.strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));
    	$data = array(
			'bill_no' => $bill_no, // Added bill_no
    		'date_time' => strtotime(date('Y-m-d h:i:s a')),
    		'user_id' => $user_id,
			// Get amounts from hidden inputs
			'gross_amount' => $this->input->post('gross_amount_value'),
			'service_charge_rate' => $this->input->post('service_charge_rate'), // Assuming hidden input exists
			'service_charge_amount' => $this->input->post('service_charge_value'), // Corrected column name
			'vat_charge_rate' => $this->input->post('vat_charge_rate'), // Assuming hidden input exists
			'vat_charge_amount' => $this->input->post('vat_charge_value'), // Corrected column name
			'net_amount' => $this->input->post('net_amount_value'),
			'discount' => $this->input->post('discount') ? $this->input->post('discount') : 0,
    		'paid_status' => $this->input->post('paid_status'),
			'table_id' => $this->input->post('table_name'),
			// 'store_id' => $this->input->post('store_id') // Assuming store_id comes from somewhere
    	);
		// Add store_id if it exists in the input
		if ($this->input->post('store_id')) {
			$data['store_id'] = $this->input->post('store_id');
		} else {
			// Maybe set a default store_id if required and not provided
			// $data['store_id'] = 1;
		}


		$insert = $this->db->insert('orders', $data);
		$order_id = $this->db->insert_id();

		$count_product = count($this->input->post('product'));
    	for($x = 0; $x < $count_product; $x++) {
    		$items = array(
    			'order_id' => $order_id,
    			'product_id' => $this->input->post('product')[$x], // Ensure 'product' is the correct input name
    			'qty' => $this->input->post('qty')[$x],
    			'rate' => $this->input->post('rate_value')[$x],
    			'amount' => $this->input->post('amount_value')[$x],
    		);
    		$this->db->insert('order_items', $items); // Corrected table name

				// Update inventory stock
				$this->model_inventory->adjustStock($this->input->post('product')[$x], 1, 'subtract', $this->input->post('qty')[$x], null, 'Sale'); // Adjust stock - Assuming location 1 for now
    	}

    	// Update table status
    	$table_id = $this->input->post('table_name');
    	if ($table_id) {
    		$this->db->where('id', $table_id);
    		$this->db->update('tables', array('available' => 2)); // Mark as unavailable
    	}

		return $order_id;
	}

	/**
	 * Creates a new order from AJAX JSON data.
	 * @param array $data Order data including table_id, items, user_id, etc.
	 * @return int|bool Order ID on success, false on failure.
	 */
	public function create_ajax($data)
	{
		$user_id = $data['user_id'];
		$company = $this->model_company->getCompanyData(1); // Assuming company ID is 1

		// Recalculate amounts server-side for security and accuracy
		$subtotal = 0;
		foreach ($data['items'] as $item) {
			// Fetch product price from DB to ensure accuracy
			$product_data = $this->model_products->getProductData($item['id']);
			if (!$product_data) {
				// Handle error: product not found or inactive
				log_message('error', 'create_ajax: Product ID ' . $item['id'] . ' not found or inactive.');
				return false;
			}
			$subtotal += $product_data['price'] * $item['qty'];
		}

		$service_charge_rate = isset($company['charge_amount']) ? $company['charge_amount'] : 0; // Use correct index
		$vat_charge_rate = isset($company['vat_charge']) ? $company['vat_charge'] : 0; // Use correct index
		$discount = isset($data['discount']) ? $data['discount'] : 0; // Get discount if passed, otherwise 0

		$gross_amount = $subtotal - $discount;
		$service_charge = ($gross_amount / 100) * $service_charge_rate;
		$vat_charge = ($gross_amount / 100) * $vat_charge_rate;
		$net_amount = $gross_amount + $service_charge + $vat_charge;

		$order_to_insert = array(
			'bill_no' => 'BIL-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4)),
			'date_time' => strtotime(date('Y-m-d h:i:s a')),
			'gross_amount' => $gross_amount,
			'service_charge_rate' => $service_charge_rate,
			'service_charge_amount' => $service_charge, // Corrected column name
			'waiter_id' => isset($data['waiter_id']) ? (int) $data['waiter_id'] : null,
			'vat_charge_rate' => $vat_charge_rate,
			'vat_charge_amount' => $vat_charge, // Corrected column name
			'net_amount' => $net_amount,
			'discount' => $discount,
			'paid_status' => $data['paid_status'], // Use the status passed from controller
			'user_id' => $user_id,
			'table_id' => $data['table_id']
			// 'store_id' => ? // Determine store_id if needed, maybe from user or table?
		);
		// $order_to_insert['store_id'] = 1; // Removed store_id for now as it's missing in INSERT query shown in error
		$insert = $this->db->insert('orders', $order_to_insert);
		$order_id = $this->db->insert_id();

		if (!$order_id) {
			log_message('error', 'create_ajax: Failed to insert order header.');
			return false;
		}

		// Insert order items and update inventory
		foreach ($data['items'] as $item) {
			$product_data = $this->model_products->getProductData($item['id']); // Fetch again for consistency
			if (!$product_data) continue; // Skip if product vanished somehow

			$items_to_insert = array(
				'order_id' => $order_id,
				'product_id' => $item['id'],
				'qty' => $item['qty'],
				'rate' => $product_data['price'], // Use DB price
				'amount' => ($item['qty'] * $product_data['price'])
			);
			$this->db->insert('order_items', $items_to_insert); // Corrected table name

			// Update inventory stock - Assuming location ID 1 for now, adjust as needed
			$this->model_inventory->adjustStock($item['id'], 1, 'subtract', $item['qty'], null, 'Sale');
		}

		// Update table status (moved outside the loop)
		$table_id = $data['table_id'];
		if ($table_id) {
			$this->db->where('id', $table_id);
			$this->db->update('tables', array('available' => 2)); // Mark as unavailable
		}


		return $order_id;
	}

	public function countOrderItem($order_id)
	{
		if($order_id) {
			$sql = "SELECT * FROM order_items WHERE order_id = ?"; // Corrected table name
			$query = $this->db->query($sql, array($order_id));
			return $query->num_rows();
		}
	}

	public function getOrdersItemData($order_id)
	{
		if($order_id) {
			$sql = "SELECT o.*, p.name as product_name FROM order_items o INNER JOIN products p ON o.product_id = p.id WHERE o.order_id = ?"; // Corrected table name
			$query = $this->db->query($sql, array($order_id));
			return $query->result_array();
		}
	}

	public function remove($id)
	{
		if ($id) {
			// Optional: Fetch order items to potentially revert stock before deleting
			// $items = $this->getOrdersItemData($id);
			// foreach ($items as $item) {
			//     $this->model_inventory->adjustStock($item['product_id'], 1, 'add', $item['qty'], null, 'Order Deleted');
			// }

			$this->db->where('id', $id);
			$delete = $this->db->delete('orders'); // Deleting order should cascade delete items if FK is set correctly

			// Optional: Update table status back to available if needed
			// $order_data = $this->getOrdersData($id); // Need to fetch before delete
			// if ($order_data && $order_data['table_id']) {
			//     $this->db->where('id', $order_data['table_id']);
			//     $this->db->update('tables', ['available' => 1]);
			// }

			return ($delete == true) ? true : false;
		}
		return false;
	}


	public function update($id)
	{
		if($id) {
			// Get Original Order Items before deleting them
			$original_items_query = $this->db->get_where('order_items', array('order_id' => $id)); // Corrected table name
			$original_items = $original_items_query->result_array();

			// Update order header data (e.g., paid_status, maybe amounts if recalculated)
			$order_header_data = array(
				'paid_status' => $this->input->post('paid_status'),
				// Recalculate amounts based on new items
				'gross_amount' => $this->input->post('gross_amount_value'),
				'service_charge_rate' => $this->input->post('service_charge_rate'),
				'service_charge_amount' => $this->input->post('service_charge_value'), // Corrected column name
				'waiter_id' => $this->input->post('waiter_id'),
				'vat_charge_rate' => $this->input->post('vat_charge_rate'),
				'vat_charge_amount' => $this->input->post('vat_charge_value'), // Corrected column name
				'net_amount' => $this->input->post('net_amount_value'),
				'discount' => $this->input->post('discount') ? $this->input->post('discount') : 0,
				'table_id' => $this->input->post('table_name') // Update table if changed
	    	);
			// Add store_id if it exists in the input
			if ($this->input->post('store_id')) {
				$order_header_data['store_id'] = $this->input->post('store_id');
			}

	    	$this->db->where('id', $id);
	   		$update_header = $this->db->update('orders', $order_header_data);

	   		// Remove the old order item data
	   		$this->db->where('order_id', $id);
	   		$this->db->delete('order_items'); // Corrected table name

			// --- Stock Adjustment Logic ---
			// 1. Revert stock for original items
			foreach ($original_items as $item) {
				$this->model_inventory->adjustStock($item['product_id'], 1, 'add', $item['qty'], null, 'Order Update - Revert');
			}

			// 2. Insert new items and decrease stock
	   		$count_product = count($this->input->post('product'));
	    	for($x = 0; $x < $count_product; $x++) {
	    		$items = array(
	    			'order_id' => $id,
	    			'product_id' => $this->input->post('product')[$x],
	    			'qty' => $this->input->post('qty')[$x],
	    			'rate' => $this->input->post('rate_value')[$x], // Use rate_value hidden input
	    			'amount' => $this->input->post('amount_value')[$x], // Use amount_value hidden input
	    		);
	    		$this->db->insert('order_items', $items); // Corrected table name

				// Decrease stock for the new/updated items
				$this->model_inventory->adjustStock($items['product_id'], 1, 'subtract', $items['qty'], null, 'Order Update - Sale');
	    	}

			// Update table status if changed (more complex logic might be needed if table was freed)
			$new_table_id = $this->input->post('table_name');
			// Fetch original order data to get old table id
			// $original_order = $this->getOrdersData($id); // Fetch before update if needed
			// if ($original_order['table_id'] != $new_table_id) {
				// Update old table to available
				// $this->db->where('id', $original_order['table_id']);
				// $this->db->update('tables', ['available' => 1]);
				// Update new table to unavailable
				// $this->db->where('id', $new_table_id);
				// $this->db->update('tables', ['available' => 2]);
			// } else { // If table didn't change, ensure it's marked unavailable
				$this->db->where('id', $new_table_id);
				$this->db->update('tables', ['available' => 2]);
			// }


	    	return ($update_header == true) ? true : false; // Check if header update was successful
		}
		return false;
	}
}
