<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller maalum kwa ajili ya kufanya data migration (Run Once!).
 */
class Migrate extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Hakikisha database library imekuwa loaded
        $this->load->database();
        // Unaweza kuhitaji ku-load model kama unatumia models, lakini hapa tutatumia DB direct
        // $this->load->model('model_products');

        // MUHIMU: Zuia access isipokuwa kama una-run kutoka command line au localhost (kwa usalama zaidi)
        // if (!$this->input->is_cli_request() && $_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
        //     show_error('Access Denied. This script should only be run locally or via CLI.');
        //     exit;
        // }
        echo "<h1>Migration Controller Initialized</h1>";
        echo "<p><strong>WARNING:</strong> Run these functions ONLY ONCE and ensure you have a database backup!</p>";
        echo "<hr>";
    }

    /**
     * Function kuu ya kuhamisha data ya product categories.
     * Fikia kwa URL: /index.php/migrate/product_categories
     */
    public function product_categories() {
        echo "<h2>Starting Product Categories Migration...</h2>";

        // 1. Pata bidhaa zote na category_id zao za zamani
        // Tunatumia query builder kupata data
        $query = $this->db->select('id, category_id, name')->get('products');

        if (!$query) {
            echo "<p style='color:red;'>Error fetching products: " . $this->db->error()['message'] . "</p>";
            return;
        }

        $products = $query->result_array();

        if (empty($products)) {
            echo "<p>No products found in the 'products' table.</p>";
            return;
        }

        echo "<p>Found " . count($products) . " products to process.</p>";
        echo "<ol>";

        $insert_batch_data = []; // Tutatumia batch insert kwa ufanisi

        foreach ($products as $product) {
            $product_id = $product['id'];
            $product_name = $product['name'];
            $old_category_json = $product['category_id']; // Hii ndiyo column ya zamani yenye JSON

            echo "<li>Processing Product ID: {$product_id} ('{$product_name}') - Old JSON: {$old_category_json} <br>";

            // 2. Decode JSON string
            // Tunatumia 'true' ili kupata array badala ya object
            $category_ids = json_decode($old_category_json, true);

            // 3. Angalia kama JSON ilikuwa valid na ni array
            if (json_last_error() === JSON_ERROR_NONE && is_array($category_ids)) {
                if (empty($category_ids)) {
                     echo "<span style='color:orange;'> - No category IDs found in JSON or JSON was empty array.</span><br>";
                     continue; // Nenda kwa bidhaa inayofuata
                }
                // 4. Loop kupitia category IDs zilizopatikana
                foreach ($category_ids as $category_id) {
                    // Safisha category ID (kama ni string, ifanye integer)
                    $category_id = (int) $category_id;

                    if ($category_id > 0) {
                        // 5. Andaa data kwa ajili ya kuingiza kwenye product_categories
                        $insert_data = [
                            'product_id' => $product_id,
                            'category_id' => $category_id
                        ];

                        // Ongeza kwenye batch array
                        $insert_batch_data[] = $insert_data;
                        echo "<span style='color:green;'> - Prepared link: Product ID {$product_id} -> Category ID {$category_id}</span><br>";
                    } else {
                        echo "<span style='color:orange;'> - Invalid Category ID found: '{$category_id}' (from JSON). Skipping.</span><br>";
                    }
                }
            } else {
                // JSON ilikuwa invalid au haikuwa array
                 echo "<span style='color:red;'> - Failed to decode JSON or result was not an array. JSON Error: " . json_last_error_msg() . "</span><br>";
            }
             echo "</li>";
        }
         echo "</ol>";

        // 6. Ingiza data yote kwa pamoja (Batch Insert)
        if (!empty($insert_batch_data)) {
            echo "<p>Attempting to insert " . count($insert_batch_data) . " relationships into 'product_categories' table...</p>";

            // Futa data iliyopo kwanza? (Optional - kuwa mwangalifu sana hapa)
            // $this->db->empty_table('product_categories');
            // echo "<p style='color:orange;'>Cleared existing data from product_categories table.</p>";

            $inserted = $this->db->insert_batch('product_categories', $insert_batch_data);

            if ($inserted) {
                echo "<p style='color:green; font-weight:bold;'>Successfully inserted " . $this->db->affected_rows() . " relationships!</p>";
            } else {
                echo "<p style='color:red;'>Error during batch insert: " . $this->db->error()['message'] . "</p>";
            }
        } else {
            echo "<p>No valid relationships found to insert.</p>";
        }

        echo "<h2>Product Categories Migration Finished.</h2>";
        echo "<hr>";
        echo "<p><strong>Remember to remove or disable this controller ('Migrate.php') after successful migration.</strong></p>";
    }

    // Unaweza kuongeza functions zingine za migration hapa baadaye kama zitahitajika
    // Mfano: public function migrate_something_else() { ... }

}

