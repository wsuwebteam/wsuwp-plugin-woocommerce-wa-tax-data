<?php namespace WSUWP\Plugin\WA_Tax_Query;

class TaxQuery
{
    public function __construct()
    {

    }

    /*******************************
     *   Get inputs start date and end date
     *   Sanitize user inputs
     *   Call function to fetch data from the DB
     *   *** Maybe process here in a loop or process on display side.***
    ********************************/
    public static function processTaxData($StartDate = false, $EndDate = false) 
    {        
        $query_args = array(
            'post_type' => 'shop_order',
            'post_status' => 'wc-completed',
            'meta_key' => '_date_completed',
            'meta_query' => array( // WordPress has all the results, now, return only the events after today's date
                'relation' => 'AND',
                array(
                    'key' => '_date_completed', // Check the start date field
                    //'value' => array('1617260400','1619766000'),//$StartDate, $EndDate), Tried a BETWEEN compare   
                    'value' => strtotime($StartDate),
                    //'value' => date($StartDate), // Start Date
                    'compare' => '>=', // Return the ones after the start date
                    'type' => 'integer' // Let WordPress know we're working with date
                ),
                array(
                    'key' => '_date_completed', // Check the start date field
                    'value' => strtotime($EndDate),
                    //'value' => date($EndDate), // End Date
                    'compare' => '<=', // Return the ones than the end date
                    'type' => 'integer' // Let WordPress know we're working with date
                    ) 
            ),
        );

        $orders = get_posts( $query_args );
        //echo("<table>")
        $F_Name = "";
        $L_Name = "";
        foreach ( $orders as $order ) {

            $meta = get_post_meta( $order->ID );
            foreach($meta as $details)
            {
                var_dump($details);
                if(isset($details['_billing_first_name']))
                {
                    $F_Name = $details['_billing_first_name'];
                }
                elseif(isset($details['_billing_last_name']))
                {
                    $L_Name = $details['_billing_last_name'];
                }
            }
            //var_dump($meta);
            var_dump($F_Name);
            var_dump($L_Name);
            echo($F_Name . " " . $L_Name);
            

            //echo '<li>' . $order->ID . ' ' . get_post_meta( $order->ID, '_order_tax', true ) . '</li>';
            // var_dump( $order );
            // see all meta var_dump( get_post_meta( $order->ID ) )
        }


        /*// Clean up the user passed parms
        $StartDate = sanitize_text_field($StartDate);
        $EndDate = sanitize_text_field($EndDate);
        //fetch data from the database.
        $TaxData = self::fetchTaxData($StartDate, $EndDate);
        // process data from the db
        $filename = 'TaxData' . " " . $StartDate . "--" . $EndDate;
        $output = fopen('php://output', 'w');
        fputcsv( $output, array('Order_ID', 'Ship_Date', 'Customer_Name', 'Company_Name', 'Address_Line1', 'Address_Line2', 'City', 'State', 'Zip', 'Tax', 'Tax_Code'));
        foreach ( $TaxData as $key => $value ) 
        {
            $modified_values = array(
                        $value['post_id'],
                        $value['ShipDate'],
                        $value['CustomerFName'] . " " . $value['CustomerLName'],
                        $value['Company_Name'],
                        $value['Address_Line1'],
                        $value['Address_Line2'],
                        $value['City'],
                        $value['State'],
                        $value['Zip'],
                        $value['Tax'],
                        $value['Tax_Code']
            );
            
            fputcsv( $output, $modified_values );
        } 
        return $TaxData;
        //var_dump($TaxData[0][0]);
        // Return download csv file
        /*header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"" . $filename .  ".csv\";" );
        header("Content-Transfer-Encoding: binary");exit;*/
    }

    /*************************************
     *  Use wpdb object to rund SQL query to pull tax data
     *  Return tax data to calling object
     *  Returns customer address information used to calclulate taxes along with state tax codes identifying tax zones to set amounts.
     *************************************/
    public static function fetchTaxData($StartDate, $EndDate)
    {        
        global $wpdb;
        $strQString = "SELECT p.post_id, MAX(CASE WHEN pm.meta_key = '_date_completed' THEN FROM_UNIXTIME(pm.meta_value) END) as ShipDate, MAX(CASE WHEN pm.meta_key = '_shipping_first_name' THEN pm.meta_value END) as CustomerFName, ";
        $strQString .= "MAX(CASE WHEN pm.meta_key = '_shipping_last_name' THEN pm.meta_value END) as CustomerLName, MAX(CASE WHEN pm.meta_key = '_shipping_company' THEN pm.meta_value END) as CompanyName, MAX(CASE WHEN pm.meta_key = '_shipping_address_1' THEN pm.meta_value END) as AddressLine1, ";
        $strQString .= "MAX(CASE WHEN pm.meta_key = '_shipping_address_2' THEN pm.meta_value END) as AddressLine2, MAX(CASE WHEN pm.meta_key = '_shipping_city' THEN pm.meta_value END) as City, MAX(CASE WHEN pm.meta_key = '_shipping_state' THEN pm.meta_value END) as State, ";
        $strQString .= "MAX(CASE WHEN pm.meta_key = '_shipping_postcode' THEN pm.meta_value END) as Zip, MAX(CASE WHEN pm.meta_key = '_order_tax' THEN pm.meta_value END) as Tax, MAX(CASE WHEN pm.meta_key = '_order_shipping_tax' THEN pm.meta_value END) as ShippingTax ";
        $strQString .= "FROM local.wp_postmeta p JOIN local.wp_postmeta pm ON p.post_id = pm.post_id WHERE p.post_id IN(SELECT pp.ID FROM local.wp_posts pp WHERE pp.post_type = 'shop_order' AND pp.post_status = 'wc-completed') ";
        $strQString .= "AND p.meta_key = '_date_completed' AND p.meta_value BETWEEN UNIX_TIMESTAMP($StartDate) AND UNIX_TIMESTAMP($EndDate) GROUP BY p.post_id";

        return $wpdb->get_results($strQString);
    }
}
