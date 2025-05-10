<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends Admin_Controller 
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_auth');
		$this->load->model('model_users'); // Load model_users to get group info
	}

	/* 
		Check if the login form is submitted, and validates the user credential
		If not submitted it redirects to the login page
	*/
	public function login()
	{

		$this->logged_in();

		$this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == TRUE) {
            // true case
           	$email_exists = $this->model_auth->check_email($this->input->post('email'));

           	if($email_exists == TRUE) {
           		$login = $this->model_auth->login($this->input->post('email'), $this->input->post('password'));

           		if($login) {

           			$logged_in_sess = array(
           				'id' => $login['id'],
				        'username'  => $login['username'],
				        'email'     => $login['email'],
				        'logged_in' => TRUE
					);

					$this->session->set_userdata($logged_in_sess);

					// Angalia kundi la mtumiaji
					// Badilisha '4' kuwa ID sahihi ya kundi la "Cashier" kwenye mfumo wako
					// Au unaweza kuangalia kwa jina la kundi: $user_group['group_name'] == 'Cashier'
					$cashier_group_id = 4; // MFANO: Weka ID sahihi ya Cashier hapa
					$user_group = $this->model_users->getUserGroup($login['id']);

					if ($user_group && isset($user_group['id']) && $user_group['id'] == $cashier_group_id) {
						// Kama ni Cashier, mpeleke kwenye ukurasa wa kuunda oda
						redirect('orders/create', 'refresh');
					} else {
						// Kwa watumiaji wengine, wapeleke kwenye dashboard
           				redirect('dashboard', 'refresh');
					}
           		}
           		else {
           			$this->data['errors'] = 'Incorrect username/password combination';
           			$this->load->view('login', $this->data);
           		}
           	}
           	else {
           		$this->data['errors'] = 'Email does not exists';

           		$this->load->view('login', $this->data);
           	}	
        }
        else {
            // false case
            $this->load->view('login');
        }	
	}

	/*
		clears the session and redirects to login page
	*/
	public function logout()
	{
		$this->session->sess_destroy();
		redirect('auth/login', 'refresh');
	}

}
