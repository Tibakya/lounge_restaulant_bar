<?php

class Model_inventory extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Fetches inventory data, optionally filtered by ID.
	 * Joins with products and locations tables using the correct column names.
	 */
	public function getInventoryData($inventoryId = null)
	{
		// Corrected column names based on user's table structure
		$this->db->select('
            inv.id as inventory_id,
            inv.current_quantity as quantity,
            inv.low_stock_threshold,
            inv.last_updated,
            p.name as product_name,
                      loc.name as location_name
        ');
		$this->db->from('inventory AS inv');
		$this->db->join('products AS p', 'p.id = inv.product_id', 'inner');
		// Corrected join condition for inventory_locations
		$this->db->join('inventory_locations AS loc', 'loc.id = inv.location_id', 'left');

		if ($inventoryId) {
			// Corrected where clause column name
			$this->db->where('inv.id', $inventoryId);
			$query = $this->db->get();
			return $query->row_array(); // Return single row
		} else {
			$this->db->order_by('p.name', 'ASC'); // Order by product name
			$this->db->order_by('loc.name', 'ASC'); // Then by location name
			$query = $this->db->get();
			return $query->result_array(); // Return multiple rows
		}
	}

    /**
     * Adjusts the stock quantity for a given product and location.
     * Creates a new inventory record if it doesn't exist.
     *
     * @param int $product_id ID of the product.
     * @param int $location_id ID of the location.
     * @param string $adjustment_type 'set', 'add', or 'subtract'.
     * @param float $quantity The quantity to set, add, or subtract.
     * @param float|null $unit_cost Optional unit cost.
     * @param string|null $reason Optional reason for adjustment (for logging later).
     * @return bool True on success, false on failure.
     */
    public function adjustStock($product_id, $location_id, $adjustment_type, $quantity, $unit_cost = null, $reason = null)
    {
        if (!$product_id || !$location_id || !$adjustment_type || !is_numeric($quantity)) {
            log_message('error', 'adjustStock: Invalid parameters provided.');
            return false;
        }

        // Start transaction
        $this->db->trans_start();

        // Check if inventory record exists for this product and location
        $this->db->where('product_id', $product_id);
        $this->db->where('location_id', $location_id);
        $query = $this->db->get('inventory');
        $existing_record = $query->row_array();

        $new_quantity = 0;
        $data = [];

        if ($existing_record) {
            // Record exists, calculate new quantity based on adjustment type
            $current_quantity = (float)$existing_record['current_quantity'];

            switch ($adjustment_type) {
                case 'set':
                    $new_quantity = $quantity;
                    break;
                case 'add':
                    $new_quantity = $current_quantity + $quantity;
                    break;
                case 'subtract':
                    $new_quantity = $current_quantity - $quantity;
                    break;
                default:
                    log_message('error', 'adjustStock: Invalid adjustment type.');
                    $this->db->trans_rollback(); // Rollback transaction
                    return false;
            }

            // Prepare data for update
            $data['current_quantity'] = $new_quantity;
            // Optionally update unit cost if provided
            if ($unit_cost !== null) {
                // You might want logic here: update only if new cost is different, or always update?
                // For now, let's always update if provided.
                // $data['unit_cost'] = $unit_cost; // Uncomment if you add unit_cost column
            }
            // last_updated is handled by DB timestamp

            // Update the existing record
            $this->db->where('id', $existing_record['id']);
            $this->db->update('inventory', $data);

        } else {
            // Record does not exist, create a new one
            // For 'subtract', you can't subtract from non-existent stock (unless you allow negative)
            if ($adjustment_type == 'subtract') {
                 log_message('error', 'adjustStock: Cannot subtract from non-existent stock record.');
                 $this->db->trans_rollback();
                 return false; // Or allow negative stock if needed
            }

            // For 'set' or 'add', the new quantity is simply the provided quantity
            $new_quantity = $quantity;

            // Prepare data for insert
            $data['product_id'] = $product_id;
            $data['location_id'] = $location_id;
            $data['current_quantity'] = $new_quantity;
            // Optionally set unit cost if provided
            // if ($unit_cost !== null) {
            //     $data['unit_cost'] = $unit_cost; // Uncomment if you add unit_cost column
            // }
             // Optionally set low stock threshold if provided or default
            // $data['low_stock_threshold'] = 0.00; // Example default

            // Insert the new record
            $this->db->insert('inventory', $data);
        }

        // TODO: Add logging of the stock adjustment transaction to a separate table (e.g., inventory_log)
        // This is important for tracking changes. Include user_id, product_id, location_id, old_qty, change_qty, new_qty, reason, timestamp.
        // Example:
        // $log_data = [
        //     'user_id' => $this->session->userdata('id'),
        //     'product_id' => $product_id,
        //     'location_id' => $location_id,
        //     'adjustment_type' => $adjustment_type,
        //     'quantity_change' => ($adjustment_type == 'set') ? $new_quantity - ($existing_record ? $existing_record['current_quantity'] : 0) : (($adjustment_type == 'add') ? $quantity : -$quantity),
        //     'new_quantity' => $new_quantity,
        //     'unit_cost' => $unit_cost,
        //     'reason' => $reason,
        //     'log_timestamp' => date('Y-m-d H:i:s')
        // ];
        // $this->db->insert('inventory_log', $log_data);


        // Complete transaction
        $this->db->trans_complete();

        // Check transaction status
        if ($this->db->trans_status() === FALSE) {
            log_message('error', 'adjustStock: Database transaction failed.');
            return false;
        }

        return true;
    }

}
