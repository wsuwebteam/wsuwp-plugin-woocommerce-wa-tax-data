<?php namespace WSUWP\Plugin\WA_Tax_Query;

class TaxQuery
{
    /*******************************
     *   Get inputs start date and end date
     *   Sanitize user inputs
     *   Call function to fetch data from the DB
     *   *** Maybe process here in a loop or process on display side.***
    ********************************/
    public static function processTaxData($StartDate = false, $EndDate = false) 
    {
        /**********************
         * Clean up user inputs
         *********************/
        $StartDate = sanitize_text_field($StartDate);
        $EndDate = sanitize_text_field($EndDate);
        
        if(strtotime($StartDate) && strtotime($EndDate))
        {
            /**********************************
            *   Set up file to download to user
            **********************************/
            $filename = 'TaxData' . " " . $StartDate . "--" . $EndDate;
            $output = fopen('php://output', 'w');
            fputcsv( $output, array('Order_ID', 'Ship_Date', 'Customer_Name', 'Company_Name', 'Address_Line1', 'Address_Line2', 'City', 'State', 'Zip', 'Tax', 'Tax_Code'));
            $orders = self::fetchTaxData($StartDate, $EndDate);
            /*************************************************
             *  Set up each orders data to make an export row.
             ************************************************/
            foreach ( $orders as $order ) 
            {            
                $CustomoerFName = get_post_meta( $order->ID, '_shipping_first_name', true );
                $CustomoerLName = get_post_meta( $order->ID, '_shipping_last_name', true );
                $CompanyName = get_post_meta( $order->ID, '_shipping_company', true );
                $AddressLine1 = get_post_meta( $order->ID, '_shipping_address_1', true );
                $AddressLine2 = get_post_meta( $order->ID, '_shipping_address_1', true );
                $City = get_post_meta( $order->ID, '_shipping_city', true );
                $State = get_post_meta( $order->ID, '_shipping_state', true );
                $Zip = get_post_meta( $order->ID, '_shipping_postcode', true );
                $Tax = (double)get_post_meta( $order->ID, '_order_tax', true ) + (double)get_post_meta( $order->ID, '_order_shipping_tax', true );
                $TaxCode = 
                /******************************
                 *  add datarow to the csv file
                 *****************************/
                $modified_values = array(
                    $order->ID,
                    $CustomoerFName . " " . $CustomoerLName,
                    $CompanyName,
                    $AddressLine1,
                    $AddressLine2,
                    $City,
                    $State,
                    $Zip,
                    $Tax,                    
                    $TaxCode
                );
        
                fputcsv( $output, $modified_values );
            }
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header('Content-Type: text/csv; charset=utf-8');
            header("Content-Disposition: attachment; filename=\"" . $filename .  ".csv\";" );
            header("Content-Transfer-Encoding: binary");
            exit;

        }
    }   
    
    /**********************************************
     *  Remove or wrap to contain comma in csv file
     *********************************************/
    public static function RemoveComma ($input)
    {

    }

    /***********************************
     *  Get zip +4 from USPS web service
     **********************************/
    public static function zip4($Address, $City, $State, $Zip)
    {

    }

    /*********************************************
     *  Get tax code from WA State tax web service
     ********************************************/
    public static function fetchTaxCode($zip)
    {

    }

    /*************************************
     *  Using get posts with meta query to pull orders for the date range.
     *  Return order data to calling object
     *  Returns customer order information to process. This returns all completed orders within the date range. 
     *************************************/
    public static function fetchTaxData($StartDate, $EndDate)
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

        return $orders;
    }
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
