<?php

class Model_products extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		// Load model_users if needed elsewhere, but not directly needed for getProductData anymore
		// $this->load->model('model_users');
	}

	/* get the product data */
	public function getProductData($id = null)
	{
		// Select columns from products table and group categories
		$this->db->select('products.*, GROUP_CONCAT(DISTINCT category.name SEPARATOR ", ") as category_names, GROUP_CONCAT(DISTINCT product_categories.category_id) as category_ids');
		$this->db->from('products');
		// Join with product_categories and category tables
		$this->db->join('product_categories', 'product_categories.product_id = products.id', 'left');
		$this->db->join('category', 'category.id = product_categories.category_id', 'left');

		if($id) {
			$this->db->where('products.id', $id);
		}

		// Group by product id to aggregate categories
		$this->db->group_by('products.id');
		$this->db->order_by('products.name', 'ASC'); // Order by name instead of ID

		$query = $this->db->get();

		// If getting a single product, return a single row, otherwise return all results
		return ($id) ? $query->row_array() : $query->result_array();
	}

	/* get the product data - Adjusted for non-admin users if necessary, but currently shows all active */
	public function getActiveProductData()
	{
		// Select columns from products table and group categories
		$this->db->select('products.*, GROUP_CONCAT(DISTINCT category.name SEPARATOR ", ") as category_names, GROUP_CONCAT(DISTINCT product_categories.category_id) as category_ids');
		$this->db->from('products');
		// Join with product_categories and category tables
		$this->db->join('product_categories', 'product_categories.product_id = products.id', 'left');
		$this->db->join('category', 'category.id = product_categories.category_id', 'left');
		$this->db->where('products.active', 1);
		// Group by product id to aggregate categories
		$this->db->group_by('products.id');
		$this->db->order_by('products.name', 'ASC');
		$query = $this->db->get();
		return $query->result_array();

		// If you need store-specific logic for non-admins later, re-introduce user/store checks here
	}

	/* get the product data by category */
	public function getProductDataByCat($cat_id)
	{
		// Use JOIN to get products based on category ID
		$this->db->select('products.*');
		$this->db->from('products');
		$this->db->join('product_categories', 'product_categories.product_id = products.id');
		$this->db->where('product_categories.category_id', $cat_id);
		$this->db->where('products.active', 1);
		$this->db->order_by('products.name', 'ASC');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function countTotalProducts()
	{
		$sql = "SELECT * FROM products";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	public function create($data)
	{
		// Separate category IDs from the main product data
		$categories = [];
		if (isset($data['category_ids'])) {
			$categories = $data['category_ids'];
			unset($data['category_ids']); // Remove it from main data array
		}

		// Insert product data first
		$insert = $this->db->insert('products', $data);
		$product_id = $this->db->insert_id();

		// If product insert was successful and there are categories, insert them
		if ($product_id && !empty($categories)) {
			$category_data = [];
			foreach ($categories as $category_id) {
				// Ensure category_id is valid before adding
				if ((int)$category_id > 0) {
					$category_data[] = [
						'product_id' => $product_id,
						'category_id' => (int) $category_id
					];
				}
			}
			// Only insert if there's valid data
			if (!empty($category_data)) {
				$this->db->insert_batch('product_categories', $category_data);
			}
		}

		return ($product_id) ? $product_id : false; // Return the new product ID or false
	}

	public function update($data, $id)
	{
		if($data && $id) {
			$this->db->where('id', $id);

			// Separate category IDs if they exist
			$categories = null;
			if (isset($data['category_ids'])) {
				$categories = $data['category_ids'];
				unset($data['category_ids']); // Remove from main data array
			}

			// Update product data (only if there's data left after removing category_ids)
			if (!empty($data)) {
				$update = $this->db->update('products', $data);
			} else {
				$update = true; // Assume success if only categories were updated
			}


			// If categories were provided (even an empty array means "remove all"), update the product_categories table
			if ($categories !== null) {
				// 1. Delete existing categories for this product
				$this->db->where('product_id', $id);
				$this->db->delete('product_categories');

				// 2. Insert new categories if any were selected
				if (!empty($categories)) {
					$category_data = [];
					foreach ($categories as $category_id) {
						// Ensure category_id is valid before adding
						if ((int)$category_id > 0) {
							$category_data[] = [
								'product_id' => $id,
								'category_id' => (int) $category_id
							];
						}
					}
					// Only insert if there's valid data
					if (!empty($category_data)) {
						$this->db->insert_batch('product_categories', $category_data);
					}
				}
			}

			return true; // Assume success if update query runs or only categories were managed
		}
		return false; // Return false if no data or ID provided
	}

	public function remove($id)
	{
		if($id) {
			// Deleting from 'products' table will cascade delete related 'product_categories' and 'inventory' due to FOREIGN KEY constraints (ON DELETE CASCADE)
			$this->db->where('id', $id);
			$delete = $this->db->delete('products');
			return ($delete == true) ? true : false;
		}
		return false;
	}

}
