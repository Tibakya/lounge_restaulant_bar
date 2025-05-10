<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Products';

		$this->load->model('model_products');
		$this->load->model('model_category');
		// $this->load->model('model_stores'); // Store model might not be needed directly here anymore
		// Load inventory location model if needed later for inventory management
		// $this->load->model('model_inventory_locations');
	}

    /*
    * It only redirects to the manage product page
    */
	public function index()
	{
        if(!in_array('viewProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->render_template('products/index', $this->data);
	}

    /*
    * It Fetches the products data from the product table
    * this function is called from the datatable ajax function
    */
	public function fetchProductData()
	{
        if(!in_array('viewProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$result = array('data' => array());

		$data = $this->model_products->getProductData(); // Model now fetches category names

		foreach ($data as $key => $value) {

			// Category names are now fetched directly by the model using GROUP_CONCAT
			$category_names = $value['category_names'] ? $value['category_names'] : 'N/A';

			// button
			$buttons = '';
            if(in_array('updateProduct', $this->permission)) {
    			$buttons .= '<a href="'.base_url('products/update/'.$value['id']).'" class="btn btn-info"><i class="fa fa-pencil"></i></a>';
            }

            if(in_array('deleteProduct', $this->permission)) {
    			$buttons .= ' <button type="button" class="btn btn-danger" onclick="removeFunc('.$value['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
            }

            // Angalia kama kuna njia sahihi ya picha, vinginevyo tumia placeholder
            $image_path = '';
            // Check if image path is not empty and does not contain the error message '<p>'
            if (!empty($value['image']) && strpos($value['image'], '<p>') === false) {
                $image_path = base_url($value['image']);
            } else {
                // Use a placeholder image if the path is invalid or empty
                // Make sure 'placeholder.png' exists in 'assets/images/' or change the path
                $image_path = base_url('assets/images/placeholder.png');
            }
			$img = '<img src="'.$image_path.'" alt="'.$value['name'].'" class="img-thumbnail" width="50" height="50" />';

            $availability = ($value['active'] == 1) ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
			$product_type = ucfirst($value['product_type']); // Capitalize 'food' or 'beverage'

			$result['data'][$key] = array(
				$img,
				$value['name'],
				$product_type, // Display product type
				$category_names, // Display category names
                $value['price'],
				// Store column removed - manage via inventory
                $availability,
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}

    /*
    * view the product based on the store (Now shows all products grouped by category)
    * the admin can view all the product information
    */
    public function viewproduct()
    {
        if(!in_array('viewProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        $company_currency = $this->company_currency();
        // get all the active categories
        $category_data = $this->model_category->getActiveCategory();

        $result = array();

        foreach ($category_data as $k => $v) {
            $products_in_cat = $this->model_products->getProductDataByCat($v['id']);
            // Only include category if it has products
            if (!empty($products_in_cat)) {
                $result[$k]['category'] = $v;
                $result[$k]['products'] = $products_in_cat;
            }
        }

        // based on the category get all the products

        $html = '<!-- Main content -->
                    <!DOCTYPE html>
                    <html>
                    <head>
                      <meta charset="utf-8">
                      <meta http-equiv="X-UA-Compatible" content="IE=edge">
                      <title>Product Menu</title>
                      <!-- Tell the browser to be responsive to screen width -->
                      <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
                      <!-- Bootstrap 3.3.7 -->
                      <link rel="stylesheet" href="'.base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css').'">
                      <!-- Font Awesome -->
                      <link rel="stylesheet" href="'.base_url('assets/bower_components/font-awesome/css/font-awesome.min.css').'">
                      <link rel="stylesheet" href="'.base_url('assets/dist/css/AdminLTE.min.css').'">
                      <style>
                        body { padding: 20px; }
                        .product-info { margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
                        .category-title h1 { font-size: 24px; margin-top: 0; border-bottom: 2px solid #333; padding-bottom: 5px; margin-bottom: 15px; }
                        .product-detail { margin-bottom: 5px; }
                        .product-name h5 { margin: 0; font-size: 16px; font-weight: normal; }
                        .product-price h5 { margin: 0; font-size: 16px; font-weight: bold; }
                        @media print {
                          .no-print { display: none; }
                          body { padding: 0; }
                          .invoice { border: none; box-shadow: none; margin: 0; padding: 0;}
                        }
                      </style>
                    </head>
                    <body>
                    <button class="btn btn-primary no-print" onclick="window.print();" style="margin-bottom: 15px;"><i class="fa fa-print"></i> Print Menu</button>
                    <div class="wrapper">
                      <section class="invoice">
                        <div class="row text-center" style="margin-bottom: 20px;">
                            <h2>Menu</h2>
                        </div>
                        <div class="row">
                        ';
                            if (!empty($result)) {
                                foreach ($result as $k => $v) {
                                    $html .= '<div class="col-xs-12 col-sm-6">
                                        <div class="product-info">
                                            <div class="category-title">
                                                <h1>'.$v['category']['name'].'</h1>
                                            </div>';

                                            if(count($v['products']) > 0) {
                                                foreach ($v['products'] as $p_key => $p_value) {
                                                    $html .= '<div class="product-detail">
                                                                <div class="product-name" style="display:inline-block; width: 70%;">
                                                                    <h5>'.$p_value['name'].'</h5>
                                                                </div>
                                                                <div class="product-price" style="display:inline-block; float:right; width: 25%; text-align: right;">
                                                                    <h5>'.$company_currency . ' ' . number_format($p_value['price'], 2).'</h5>
                                                                </div>
                                                                <div style="clear:both;"></div>
                                                            </div>';
                                                }
                                            }
                                            // No need for 'N/A' because we only include categories with products
                                        $html .='</div>

                                    </div>';
                                }
                            } else {
                                $html .= '<div class="col-xs-12"><p class="text-center">No products found in active categories.</p></div>';
                            }


                        $html .='
                        </div>
                      </section>
                      <!-- /.content -->
                    </div>
                </body>
            </html>';

                      echo $html;
    }

    /*
    * If the validation is not valid, then it redirects to the create page.
    * If the validation for each input field is valid then it inserts the data into the database
    * and it stores the operation message into the session flashdata and display on the manage product page
    */
	public function create()
	{
		if(!in_array('createProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		// Validation rules for new fields
		$this->form_validation->set_rules('product_name', 'Product name', 'trim|required');
		$this->form_validation->set_rules('price', 'Price', 'trim|required|numeric');
		$this->form_validation->set_rules('product_type', 'Product Type', 'trim|required|in_list[food,beverage]');
		$this->form_validation->set_rules('unit_of_measure', 'Unit', 'trim'); // Optional?
		$this->form_validation->set_rules('cost_price', 'Cost Price', 'trim|numeric'); // Optional?
		$this->form_validation->set_rules('active', 'Active', 'trim|required');
		// Category validation - check if it's an array
        $this->form_validation->set_rules('category[]', 'Category', 'required');


        if ($this->form_validation->run() == TRUE) {
            // true case
        	$upload_image_path = $this->upload_image();
            // Check if upload was successful before proceeding
            if ($upload_image_path === false) {
                // Error message is set in upload_image function
                redirect('products/create', 'refresh');
                return; // Stop execution
            }

        	$data = array(
        		'name' => $this->input->post('product_name'),
        		'price' => $this->input->post('price'),
				'product_type' => $this->input->post('product_type'),
				'unit_of_measure' => $this->input->post('unit_of_measure'),
				'cost_price' => $this->input->post('cost_price') ? $this->input->post('cost_price') : 0.00,
        		'image' => $upload_image_path, // Use the returned path
        		'description' => $this->input->post('description'),
        		'category_ids' => $this->input->post('category'), // Pass category IDs as an array
        		'active' => $this->input->post('active'),
        	);

        	$create = $this->model_products->create($data);
        	if($create) { // Check if create returned a product ID
        		$this->session->set_flashdata('success', 'Successfully created');
        		redirect('products/', 'refresh');
        	}
        	else {
        		$this->session->set_flashdata('errors', 'Error occurred!! Could not create product.');
        		redirect('products/create', 'refresh');
        	}
        }
        else {
            // false case

			// Get active categories for the form select
			$this->data['category'] = $this->model_category->getActiveCategory();
			// $this->data['stores'] = $this->model_stores->getActiveStore(); // Stores not needed here anymore

            $this->render_template('products/create', $this->data);
        }
	}

    /*
    * This function is invoked from another function to upload the image into the assets folder
    * and returns the image path (relative path) or empty string or false on error.
    */
	public function upload_image()
    {
    	// Define the relative directory path
        $upload_dir = 'assets/images/product_image/';
        // Create the absolute path using FCPATH
        $config['upload_path'] = FCPATH . $upload_dir;

        // Ensure the upload directory exists and is writable
        if (!is_dir($config['upload_path'])) {
            if (!mkdir($config['upload_path'], 0777, true)) {
                 $this->session->set_flashdata('errors', 'Image Upload Error: Failed to create upload directory.');
                 return false; // Indicate failure
            }
        }
        if (!is_writable($config['upload_path'])) {
             $this->session->set_flashdata('errors', 'Image Upload Error: Upload directory is not writable.');
             return false; // Indicate failure
        }

        $config['file_name'] =  uniqid();
        $config['allowed_types'] = 'gif|jpg|png|jpeg|webp'; // Added jpeg
        $config['max_size'] = '2048'; // Increased size limit to 2MB

        // $config['max_width']  = '1024';
        // $config['max_height']  = '768';

        $this->load->library('upload', $config);
        if ( ! $this->upload->do_upload('product_image'))
        {
            // If upload fails but it's because no file was selected, return an empty string (allow update without changing image)
            if (strpos($this->upload->display_errors('', ''), 'You did not select a file to upload') !== false) {
                return ''; // Return empty string, not false
            }
            // Otherwise, store the error and return false
            $error = $this->upload->display_errors('', ''); // Return error without <p> tags
            $this->session->set_flashdata('errors', 'Image Upload Error: ' . $error);
            return false; // Indicate failure
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            // Return the relative path to store in the database
            $path = $upload_dir . $data['upload_data']['file_name']; // e.g., assets/images/product_image/filename.jpg
            return $path;
        }
    }

    /*
    * If the validation is not valid, then it redirects to the edit product page
    * If the validation is successfully then it updates the data into the database
    * and it stores the operation message into the session flashdata and display on the manage product page
    */
	public function update($product_id)
	{
        if(!in_array('updateProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        if(!$product_id) {
            redirect('dashboard', 'refresh');
        }

        $this->form_validation->set_rules('product_name', 'Product name', 'trim|required');
        $this->form_validation->set_rules('price', 'Price', 'trim|required|numeric');
		$this->form_validation->set_rules('product_type', 'Product Type', 'trim|required|in_list[food,beverage]');
		$this->form_validation->set_rules('unit_of_measure', 'Unit', 'trim');
		$this->form_validation->set_rules('cost_price', 'Cost Price', 'trim|numeric');
        $this->form_validation->set_rules('active', 'Active', 'trim|required');
        // Category validation - not strictly required if user wants to remove all categories
        // $this->form_validation->set_rules('category[]', 'Category', 'required');

        if ($this->form_validation->run() == TRUE) {
            // true case

            $data = array(
                'name' => $this->input->post('product_name'),
                'price' => $this->input->post('price'),
				'product_type' => $this->input->post('product_type'),
				'unit_of_measure' => $this->input->post('unit_of_measure'),
				'cost_price' => $this->input->post('cost_price') ? $this->input->post('cost_price') : 0.00,
                'description' => $this->input->post('description'),
                'category_ids' => $this->input->post('category') ? $this->input->post('category') : [], // Pass category IDs as an array, default to empty array if none selected
                'active' => $this->input->post('active'),
            );


            // Check if a new image was uploaded
            if(isset($_FILES['product_image']) && $_FILES['product_image']['name'] != '') { // Check if a file was actually selected
                $upload_image_path = $this->upload_image();

                // Check if upload was successful (returned a path string)
                if ($upload_image_path !== false) {
                    // If upload returned an empty string (no file selected, which shouldn't happen here due to check above, but good practice)
                    // or a valid path string
                    if ($upload_image_path != '') {
                         // Delete the old image file before updating the path
                         $old_product_data = $this->model_products->getProductData($product_id);
                         // Check if old image exists and is not a directory before unlinking
                         if ($old_product_data && !empty($old_product_data['image']) && file_exists(FCPATH . $old_product_data['image']) && !is_dir(FCPATH . $old_product_data['image'])) {
                             unlink(FCPATH . $old_product_data['image']);
                         }
                        $data['image'] = $upload_image_path; // Update data array with new image path
                    }
                    // If upload_image returned empty string (shouldn't happen here), do nothing with $data['image']
                } else {
                    // Upload failed with an error, redirect back with error message
                    // Error message is already set in upload_image function's session flashdata
                    redirect('products/update/'.$product_id, 'refresh');
                    return; // Stop execution
                }
            }
            // If no new image was uploaded, $data['image'] remains unset, and the model won't update the image field.

            $update = $this->model_products->update($data, $product_id);

            if($update == true) {
                $this->session->set_flashdata('success', 'Successfully updated');
                redirect('products/', 'refresh');
            }
            else {
                $this->session->set_flashdata('errors', 'Error occurred!! Could not update product.');
                redirect('products/update/'.$product_id, 'refresh');
            }
        }
        else {
            // false case

			// Get active categories for the form select
            $this->data['category'] = $this->model_category->getActiveCategory();
            // $this->data['stores'] = $this->model_stores->getActiveStore(); // Stores not needed

			// Get current product data, including the category IDs
            $product_data = $this->model_products->getProductData($product_id);

            if(empty($product_data)) {
                $this->session->set_flashdata('errors', 'Product not found.');
                redirect('products', 'refresh');
            }

            $this->data['product_data'] = $product_data;
			// Decode the category IDs string fetched by the model (using GROUP_CONCAT) into an array
			$this->data['product_data']['category_ids'] = $product_data['category_ids'] ? explode(',', $product_data['category_ids']) : [];

            $this->render_template('products/edit', $this->data);
        }
	}

    /*
    * It removes the data from the database
    * and it returns the response into the json format
    */
	public function remove()
	{
        if(!in_array('deleteProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        $product_id = $this->input->post('product_id');

        $response = array();
        if($product_id) {
             // Get product data to find the image path before deleting the record
             $product_data = $this->model_products->getProductData($product_id);

            $delete = $this->model_products->remove($product_id);
            if($delete == true) {
                // Delete the image file after successful record removal
                 if ($product_data && !empty($product_data['image']) && file_exists(FCPATH . $product_data['image']) && !is_dir(FCPATH . $product_data['image'])) {
                     unlink(FCPATH . $product_data['image']);
                 }
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

    public function fetchProductsJSON()
    {
        // Ensure it's an AJAX request if needed, or add permission checks
        // if (!$this->input->is_ajax_request()) {
        //    exit('No direct script access allowed');
        // }

        $searchTerm = $this->input->get('term'); // Get search term from query parameter
        $products = [];

        if ($searchTerm && strlen($searchTerm) >= 1) { // Search if term is at least 1 char
            $products = $this->model_products->searchActiveProductsByName($searchTerm);
        } else {
            // Optionally, return a few popular/recent products if search term is empty
            // $products = $this->model_products->getActiveProductData(10); // Example: Get 10 active products
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($products));
    }

}
