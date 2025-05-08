<?php

class Groups extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Groups';


		$this->load->model('model_groups');
	}

	/*
	* It redirects to the manage group page
	*/
	public function index()
	{
		if(!in_array('viewGroup', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$groups_data = $this->model_groups->getGroupData();
		$this->data['groups_data'] = $groups_data;

		$this->render_template('groups/index', $this->data);
	}

	/*
	* If the validation is not valid, then it redirects to the create page.
	* If the validation for each input field is valid then it inserts the data into the database
	* and it stores the operation message into the session flashdata and display on the manage group page
	*/
	public function create()
	{
		if(!in_array('createGroup', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$this->form_validation->set_rules('group_name', 'Group name', 'required');

        if ($this->form_validation->run() == TRUE) {
            // true case
            $permission = serialize($this->input->post('permission'));

        	$data = array(
        		'group_name' => $this->input->post('group_name'),
        		'permission' => $permission
        	);

        	$create = $this->model_groups->create($data);
        	if($create == true) {
        		$this->session->set_flashdata('success', 'Successfully created');
        		redirect('groups/', 'refresh');
        	}
        	else {
        		$this->session->set_flashdata('errors', 'Error occurred!!');
        		redirect('groups/create', 'refresh');
        	}
        }
        else {
            // false case
            $this->data['permission'] = unserialize($this->config->item('permissions')); // Load from config item

            $this->render_template('groups/create', $this->data);
        }


	}

	/*
	* If the validation is not valid, then it redirects to the edit group page
	* If the validation is successfully then it updates the data into the database
	* and it stores the operation message into the session flashdata and display on the manage group page
	*/
	public function edit($id = null)
	{
		if(!in_array('updateGroup', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		if($id) {

			$this->form_validation->set_rules('group_name', 'Group name', 'required');

			if ($this->form_validation->run() == TRUE) {
	            // true case
	            $permission = serialize($this->input->post('permission'));

	        	$data = array(
	        		'group_name' => $this->input->post('group_name'),
	        		'permission' => $permission
	        	);

	        	$update = $this->model_groups->edit($data, $id);
	        	if($update == true) {
	        		$this->session->set_flashdata('success', 'Successfully updated');
	        		redirect('groups/', 'refresh');
	        	}
	        	else {
	        		$this->session->set_flashdata('errors', 'Error occurred!!');
	        		redirect('groups/edit/'.$id, 'refresh');
	        	}
	        }
	        else {
	            // false case
	            $group_data = $this->model_groups->getGroupData($id);
				$this->data['group_data'] = $group_data;
				$this->data['permission'] = unserialize($this->config->item('permissions')); // Load from config item

				$this->render_template('groups/edit', $this->data);
	        }
		}
	}

	/*
	* It removes the removes the specified group from the database
	* and returns the json format operation messages
	*/
	public function delete($id = null) // Changed to accept ID from URL
	{
		if(!in_array('deleteGroup', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		if(!$id) {
             $response['success'] = false;
             $response['messages'] = 'Group ID is required';
             echo json_encode($response);
             return;
		}

        // Optional: Check if the group exists before attempting to delete
        $group_data = $this->model_groups->getGroupData($id);
        if(empty($group_data)) {
            $response['success'] = false;
            $response['messages'] = "Group not found.";
            echo json_encode($response);
            return;
        }

        // Optional: Prevent deleting group ID 1 (Super Admin?)
        if($id == 1) {
            $response['success'] = false;
            $response['messages'] = "Super admin group cannot be deleted.";
            echo json_encode($response);
            return;
        }

		$delete = $this->model_groups->delete($id);
		if($delete == true) {
            $response['success'] = true;
            $response['messages'] = "Successfully removed";
        }
        else {
            $response['success'] = false;
            $response['messages'] = "Error in the database while removing the group information";
        }

        echo json_encode($response);
	}


}
