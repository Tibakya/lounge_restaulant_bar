<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Orders';

		$this->load->model('model_orders');
		$this->load->model('model_tables');
		$this->load->model('model_products');
		$this->load->model('model_company');
		$this->load->model('model_category'); // Added for create/edit page
		$this->load->model('model_inventory'); // Added for stock adjustment
		$this->load->model('model_users'); // For fetching waiters
	}

	/*
	* It only redirects to the manage order page
	*/
	public function index()
	{
		if(!in_array('viewOrder', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$this->data['page_title'] = 'Manage Orders';
		// Load data needed for the modal on the index page
		$this->data['tables_data_for_modal'] = $this->model_tables->getTableData(); // Inapaswa kurudisha meza zote active
		$this->data['waiters_data_for_modal'] = $this->model_users->getUsersByGroupId(5); // Badilisha 5 kama Group ID ya wahudumu ni tofauti


		$this->render_template('orders/index', $this->data);
	}

	/*
	* Fetches the orders data from the orders table
	* this function is called from the datatable ajax function
	*/
	public function fetchOrdersData()
{
	$result = array('data' => array());
	$data = $this->model_orders->getOrdersData();

	foreach ($data as $key => $value) {
		// Prepare waiter name. Assumes 'waiter_id', 'waiter_firstname', 'waiter_lastname' are in $value from model
		$waiter_name = (!empty($value['waiter_id']) && !empty($value['waiter_firstname'])) ? html_escape($value['waiter_firstname'] . ' ' . $value['waiter_lastname']) : 'N/A';

		$date_time = date('d-m-Y h:i a', $value['date_time']);
		$count_total_item = $this->model_orders->countOrderItem($value['id']);
		$buttons = ''; // Initialize buttons
		$paid_status = ($value['paid_status'] == 1) ? '<span class="label label-success">Paid</span>' : '<span class="label label-danger">Unpaid</span>';
		
		$result['data'][$key] = array(
			$value['bill_no'],    // Index 0
			$waiter_name,         // Index 1 - Added Waiter Name
			$date_time,           // Index 2
			$count_total_item,    // Index 3
			$value['net_amount'], // Index 4
			$paid_status,         // Index 5
			''                    // Index 6 - Buttons will be added here
		);

		if (in_array('viewOrder', $this->permission)) {
			$result['data'][$key][6] .= '<a href="'.base_url('orders/printDiv/'.$value['id']).'" class="btn btn-default btn-sm"><i class="fa fa-print"></i></a>';
		}
		if (in_array('updateOrder', $this->permission)) {
			$result['data'][$key][6] .= ' <button type="button" class="btn btn-info btn-sm" onclick="openEditOrderModal('.$value['id'].')"><i class="fa fa-pencil"></i></button>';
		}
		if (in_array('deleteOrder', $this->permission)) {
			$result['data'][$key][6] .= ' <button type="button" class="btn btn-danger btn-sm" onclick="removeFunc('.$value['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
		}
	}
	echo json_encode($result);
}

	/*
	* If the validation is not valid, then it redirects to the create page.
	* If the validation for each input field is valid then it inserts the data into the database
	* and it sends the operation message into the session flashdata and display on the manage group page
	*/
	public function create()
	{
		if(!in_array('createOrder', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$this->data['page_title'] = 'Add Order';

		// Fetch data for the view
		$this->data['products'] = $this->model_products->getActiveProductData();
		$this->data['categories'] = $this->model_category->getActiveCategory();
		$this->data['tables'] = $this->model_tables->getTableData(); // Use getTableData to get all active tables
		$company = $this->model_company->getCompanyData(1); // Assuming company ID 1
		$this->data['company_data'] = $company;
		// Use the correct keys from your company table for charge rates
		$this->data['service_charge_rate'] = isset($company['charge_amount']) ? $company['charge_amount'] : 0;
		$this->data['currency_symbol'] = $this->company_currency(); // Added currency symbol
		// Fetch waiters (e.g., users from group ID 5 - Staff)
		$this->data['waiters'] = $this->model_users->getUsersByGroupId(5); // Assuming group ID 5 is for Staff/Waiters
		$this->data['recent_orders'] = $this->model_orders->getOrdersData(null, 5); // Fetch 5 recent orders

		$this->data['vat_charge_rate'] = isset($company['vat_charge']) ? $company['vat_charge'] : 0;
		$this->data['body_class'] = 'order-page'; // Add this class for order create page


		$this->render_template('orders/create', $this->data);
	}

	public function handle_ajax_create() {
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['success' => false, 'message' => 'Invalid request.']));
			return;
		}

		$json_data = json_decode(file_get_contents('php://input'), true);

		if (empty($json_data['table_id']) || empty($json_data['items']) || empty($json_data['waiter_id'])) {
			$this->output->set_output(json_encode(['success' => false, 'message' => 'Invalid order data received.']));
			return;
		}

		$order_data_for_model = array(
			'waiter_id' => (int) $json_data['waiter_id'],
			'table_id' => $json_data['table_id'],
			'items' => $json_data['items'], // Pass the items object directly
			'user_id' => $this->session->userdata('id'),
			// Add other necessary fields like discount, amounts (recalculate server-side)
			'paid_status' => 2 // Set default to Unpaid since it's not sent from JS anymore
		);

		// Call a potentially new or adapted model function for AJAX creation
		$order_id = $this->model_orders->create_ajax($order_data_for_model);

		if ($order_id) {
			$this->output->set_output(json_encode(['success' => true, 'message' => 'Order created successfully!', 'order_id' => $order_id]));
		} else {
			$this->output->set_output(json_encode(['success' => false, 'message' => 'Error creating order in the database.']));
		}
	}


	/*
	* If the validation is not valid, then it redirects to the edit orders page
	* If the validation is successfully then it updates the data into the database
	* and it sends the operation message into the session flashdata and display on the manage group page
	*/
	public function update($id)
	{
		if(!in_array('updateOrder', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		if(!$id) {
			redirect('dashboard', 'refresh');
		}

		$this->data['page_title'] = 'Update Order';

		// $this->form_validation->set_rules('product[]', 'Product name', 'trim|required');
		// $this->form_validation->set_rules('qty[]', 'Quantity', 'trim|integer');
		// $this->form_validation->set_rules('rate_value[]', 'Rate', 'trim|numeric');
		// $this->form_validation->set_rules('amount_value[]', 'Amount', 'trim|numeric');
		$this->form_validation->set_rules('product[]', 'Product name', 'trim'); // Made optional for initial load, will be populated by JS
		$this->form_validation->set_rules('qty[]', 'Quantity', 'trim|integer');
		$this->form_validation->set_rules('rate_value[]', 'Rate', 'trim|numeric');
		$this->form_validation->set_rules('amount_value[]', 'Amount', 'trim|numeric');
		$this->form_validation->set_rules('waiter_id', 'Waiter', 'trim|required|integer');
		$this->form_validation->set_rules('paid_status', 'Paid status', 'trim|required');
		$this->form_validation->set_rules('gross_amount_value', 'Gross Amount', 'trim|numeric|required');
		// Optional: Add validation for service_charge_rate, vat_charge_rate if they are user-editable
		// $this->form_validation->set_rules('service_charge_rate', 'Service Charge Rate', 'trim|numeric');
		// $this->form_validation->set_rules('vat_charge_rate', 'VAT Charge Rate', 'trim|numeric');
		$this->form_validation->set_rules('service_charge_value', 'Service Charge', 'trim|numeric');
		$this->form_validation->set_rules('vat_charge_value', 'VAT Charge', 'trim|numeric');
		$this->form_validation->set_rules('net_amount_value', 'Net Amount', 'trim|numeric|required');
		// $this->form_validation->set_rules('discount', 'Discount', 'trim|numeric'); // Discount is now part of hidden fields


        if ($this->form_validation->run() == TRUE) {
        	// true case
        	$update = $this->model_orders->update($id);
        	if($update == true) {
        		$this->session->set_flashdata('success', 'Successfully updated');
        		redirect('orders/update/'.$id, 'refresh');
        	}
        	else {
        		$this->session->set_flashdata('errors', 'Error occurred!!');
        		redirect('orders/update/'.$id, 'refresh');
        	}
        }
        else {
            // false case
			$order_data = $this->model_orders->getOrdersData($id);
			if(empty($order_data)){
				$this->session->set_flashdata('errors', 'The requested order does not exist or has been deleted.');
				redirect('orders', 'refresh');
				return;
			}
			$result['order_data'] = $order_data;
			$orders_item_raw = $this->model_orders->getOrdersItemData($id); // Raw items from DB

			// Convert order items to the format expected by JavaScript's orderList
			// JS orderList: { productId: {id, name, price, image, qty} }
			// getOrdersItemData returns: [{product_id, qty, rate, amount, product_name}]
			// We need product image and ensure price is from order_items.rate
			$js_order_items = [];
			foreach ($orders_item_raw as $item) {
				$product_details = $this->model_products->getProductData($item['product_id']); // Fetch full product details for image etc.
				if ($product_details) {
					$js_order_items[$item['product_id']] = [ // Use product_id as key for the orderList object
						'id'    => $item['product_id'],
						'name'  => $product_details['name'], // Name from products table for consistency
						'price' => (float)$item['rate'],     // Price (rate) from order_items table
						'image' => base_url() . $product_details['image'],
						'qty'   => (int)$item['qty']
					];
				}
			}
			$this->data['js_order_items_json'] = json_encode($js_order_items);

			$this->data['order_data'] = $order_data;
			$this->data['order_items_data_raw'] = $orders_item_raw; // Keep raw data if needed for other parts

			// Data for the right panel (products, categories)
			$this->data['products'] = $this->model_products->getActiveProductData(); // Fetch all active products
			$this->data['categories'] = $this->model_category->getActiveCategory(); // Fetch active categories

			// Data for the left panel (tables)
			// Fetch waiters (e.g., users from group ID 5 - Staff)
			$this->data['waiters'] = $this->model_users->getUsersByGroupId(5); // Assuming group ID 5 is for Staff/Waiters

			// Company data for charges (VAT, Service Charge)
			$company = $this->model_company->getCompanyData(1); // Assuming company ID 1
			$this->data['company_data'] = $company;
			// Use the correct keys from your company table for charge rates
			$this->data['service_charge_rate'] = isset($company['charge_amount']) ? $company['charge_amount'] : 0;
			$this->data['currency_symbol'] = $this->company_currency(); // Added currency symbol
			$this->data['vat_charge_rate'] = isset($company['vat_charge']) ? $company['vat_charge'] : 0;
			$this->data['body_class'] = 'order-page'; // Add this class for order edit page

			$this->render_template('orders/edit', $this->data);

        }
	}

	/*
	* It removes the data from the database
	* and it returns the response into the json format
	*/
	public function remove()
	{
		if(!in_array('deleteOrder', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$order_id = $this->input->post('order_id');

        $response = array();
        if($order_id) {
            $delete = $this->model_orders->remove($order_id);
            if($delete == true) {
                $response['success'] = true;
                $response['messages'] = "Successfully removed";
            }
            else {
                $response['success'] = false;
                $response['messages'] = "Error in the database while removing the product information";
            }
        }
        else {
            $response['success'] = false;
            $response['messages'] = "Refersh the page again!!";
        }

        echo json_encode($response);
	}

	/*
	* It gets the product id passed from the ajax method.
	* It checks retrieves the product data to send data related to the product id
	* back to the ajax method.
	*/
	public function getProductValueById()
	{
		$product_id = $this->input->post('product_id');
		if($product_id) {
			$product_data = $this->model_products->getProductData($product_id);
			echo json_encode($product_data);
		}
	}

	/*
	* It gets the all the active product inforamtion from the product table
	* This function is used in the order page, for the product selection in the table
	* The response is return in the json format.
	*/
	public function getTableProductRow()
	{
		$products = $this->model_products->getActiveProductData();
		echo json_encode($products);
	}

	/*
	* This function is invoked from another function to print the result into pdf format
	* It takes arguments as order_id and store_id and intialize the pdf
	*/
	public function printDiv($id)
	{
		if(!in_array('viewOrder', $this->permission)) {
          	redirect('dashboard', 'refresh');
  		}

		if($id) {
			$order_data = $this->model_orders->getOrdersData($id);
			$orders_items = $this->model_orders->getOrdersItemData($id);
			$company_info = $this->model_company->getCompanyData(1); // Assuming company ID 1

			$order_date = date('d/m/Y', $order_data['date_time']);
			$paid_status = ($order_data['paid_status'] == 1) ? "Paid" : "Unpaid";

			$html = '<!-- Main content -->
			<!DOCTYPE html>
			<html>
			<head>
			  <meta charset="utf-8">
			  <meta http-equiv="X-UA-Compatible" content="IE=edge">
			  <title>Invoice</title>
			  <!-- Tell the browser to be responsive to screen width -->
			  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
			  <!-- Bootstrap 3.3.7 -->
			  <link rel="stylesheet" href="'.base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css').'">
			  <!-- Font Awesome -->
			  <link rel="stylesheet" href="'.base_url('assets/bower_components/font-awesome/css/font-awesome.min.css').'">
			  <link rel="stylesheet" href="'.base_url('assets/dist/css/AdminLTE.min.css').'">
			</head>
			<body onload="window.print();">

			<div class="wrapper">
			  <section class="invoice">
			    <!-- title row -->
			    <div class="row">
			      <div class="col-xs-12">
			        <h2 class="page-header">
			          '.$company_info['company_name'].'
			          <small class="pull-right">Date: '.$order_date.'</small>
			        </h2>
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- info row -->
			    <div class="row invoice-info">

			      <div class="col-sm-4 invoice-col">

			        <b>Bill ID:</b> '.$order_data['bill_no'].'<br>
			        <b>Name:</b> '.(isset($order_data['customer_name']) ? html_escape($order_data['customer_name']) : 'N/A').'<br>
			        <b>Address:</b> '.(isset($order_data['customer_address']) ? html_escape($order_data['customer_address']) : 'N/A').' <br />
			        <b>Phone:</b> '.(isset($order_data['customer_phone']) ? html_escape($order_data['customer_phone']) : 'N/A').'
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- /.row -->

			    <!-- Table row -->
			    <div class="row">
			      <div class="col-xs-12 table-responsive">
			        <table class="table table-striped">
			          <thead>
			          <tr>
			            <th>Product name</th>
			            <th>Price</th>
			            <th>Qty</th>
			            <th>Amount</th>
			          </tr>
			          </thead>
			          <tbody>';

			          foreach ($orders_items as $k => $v) {

			          	$product_data = $this->model_products->getProductData($v['product_id']);

			          	$html .= '<tr>
				            <td>'.$product_data['name'].'</td>
				            <td>'.$v['rate'].'</td>
				            <td>'.$v['qty'].'</td>
				            <td>'.$v['amount'].'</td>
			          	</tr>';
			          }

			          $html .= '</tbody>
			        </table>
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- /.row -->

			    <div class="row">

			      <div class="col-xs-6 pull pull-right">

			        <div class="table-responsive">
			          <table class="table">
			            <tr>
			              <th style="width:50%">Gross Amount:</th>
			              <td>'.$order_data['gross_amount'].'</td>
			            </tr>';

			            if($order_data['service_charge_rate'] > 0) {
			            	$html .= '<tr>
				              <th>Service Charge ('.$order_data['service_charge_rate'].'%)</th>
				              <td>'.$order_data['service_charge_amount'].'</td>
				            </tr>';
			            }

			            if($order_data['vat_charge_rate'] > 0) {
			            	$html .= '<tr>
				              <th>Vat Charge ('.$order_data['vat_charge_rate'].'%)</th>
				              <td>'.$order_data['vat_charge_amount'].'</td>
				            </tr>';
			            }


			            $html .=' <tr>
			              <th>Discount:</th>
			              <td>'.$order_data['discount'].'</td>
			            </tr>
			            <tr>
			              <th>Net Amount:</th>
			              <td>'.$order_data['net_amount'].'</td>
			            </tr>
			            <tr>
			              <th>Paid Status:</th>
			              <td>'.$paid_status.'</td>
			            </tr>
			          </table>
			        </div>
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- /.row -->
			  </section>
			  <!-- /.content -->
			</div>
		</body>
	</html>';

			  echo $html;
		}
	}

	public function get_order_details_json($order_id = null)
	{
		if (!$this->input->is_ajax_request() || !$order_id) {
			$this->output->set_status_header(400)->set_output(json_encode(['success' => false, 'message' => 'Invalid Request.']));
			return;
		}

		$order_data = $this->model_orders->getOrdersData($order_id);
		$order_items_raw = $this->model_orders->getOrdersItemData($order_id); // This gets items with product_id, qty, rate, amount

		if (!$order_data) {
			$this->output->set_output(json_encode(['success' => false, 'message' => 'Order not found.']));
			return;
		}

		$detailed_order_items = [];
		if ($order_items_raw) {
			foreach ($order_items_raw as $item_raw) {
				$product_details = $this->model_products->getProductData($item_raw['product_id']); // Fetch full product details
				if ($product_details) {
					$detailed_order_items[] = [
						'product_id'     => $item_raw['product_id'],
						'product_name'   => $product_details['name'], // Name from products table
						'product_image'  => $product_details['image'], // Image from products table
						'qty'            => $item_raw['qty'],
						'rate'           => $item_raw['rate'],    // Price (rate) from order_items table
						'amount'         => $item_raw['amount']
					];
				}
			}
		}

		$response_data = [
			'success' => true,
			'order_data' => $order_data, // Contains bill_no, table_id, waiter_id, paid_status, etc.
			'order_items' => $detailed_order_items
		];

		$this->output->set_content_type('application/json')->set_output(json_encode($response_data));
	}


}
