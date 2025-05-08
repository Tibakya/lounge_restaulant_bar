<?php

class Model_inventory_locations extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* get inventory location data */
	public function getInventoryLocationData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM inventory_locations WHERE id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM inventory_locations ORDER BY name ASC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/* get active inventory locations */
	public function getActiveInventoryLocations()
	{
		$sql = "SELECT * FROM inventory_locations WHERE active = ? ORDER BY name ASC";
		$query = $this->db->query($sql, array(1));
		return $query->result_array();
	}

	public function create($data = array())
	{
		if($data) {
			$create = $this->db->insert('inventory_locations', $data);
			return ($create == true) ? true : false;
		}
	}

	public function update($id = null, $data = array())
	{
		if($id && $data) {
			$this->db->where('id', $id);
			$update = $this->db->update('inventory_locations', $data);
			return ($update == true) ? true : false;
		}
	}

	public function remove($id = null)
	{
		if($id) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('inventory_locations');
			return ($delete == true) ? true : false;
		}
	}
}