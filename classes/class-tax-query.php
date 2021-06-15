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
            $orders = self::FetchTaxData($StartDate, $EndDate);
            self::SetUpCSV($orders, $output);
            
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
    
     /**********************************************************
     *  Create the .csv file to export
     *********************************************************/
    public static function SetUpCSV ($orders, $output)
    {
        /*************************************************
         *  Set up each orders data to make an export row.
         ************************************************/
        foreach ( $orders as $order ) 
        {   
            $OrderID = $order->ID;
            $ShipDate = date("m/d/Y", get_post_meta( $order->ID, '_date_completed', true ));//get_post_meta( $order->ID, '_date_completed', true );            
            $CustomoerFName = get_post_meta( $order->ID, '_shipping_first_name', true );
            $CustomoerLName = get_post_meta( $order->ID, '_shipping_last_name', true );
            $CompanyName = get_post_meta( $order->ID, '_shipping_company', true );
            $AddressLine1 = get_post_meta( $order->ID, '_shipping_address_1', true );
            $AddressLine2 = get_post_meta( $order->ID, '_shipping_address_2', true );
            $City = get_post_meta( $order->ID, '_shipping_city', true );
            $State = get_post_meta( $order->ID, '_shipping_state', true );
            $Zip = get_post_meta( $order->ID, '_shipping_postcode', true );
            $Zip4 = "";
            if (!strpos($Zip, '-') && $State == 'WA')
            {
                $Zip4 = self::zip4($AddressLine1, $City, $State, $Zip);
                $Zip .= "-" . $Zip4;
            }
            $Tax = (Float)get_post_meta( $order->ID, '_order_tax', true ) + (float)get_post_meta( $order->ID, '_order_shipping_tax', true );
            if($State === 'WA')
            {
                $TaxCode = self::FetchTaxCode($AddressLine1, $City, $Zip);   
            }     
            else
            {
                $TaxCode = "";
            }               
            
            /******************************
             *  add datarow to the csv file
             *****************************/
             $modified_values = array(
                $OrderID,
                $ShipDate,
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
            //var_dump($modified_values);
            fputcsv( $output, $modified_values );
        }
    }
    /**********************************************
     *  Remove or wrap to contain comma in csv file
     *********************************************/
    public static function RemoveCommas ($input)
    {
        if(str_contains($input, ','))
        {
            $input = str_replace(',', '', $input);            
        }
        return $input;
    }

    /***********************************
     *  Get zip +4 from USPS web service
     **********************************/
    public static function zip4($Address, $City, $State, $Zip)
    {
        $request_XML = <<<EOT
             <AddressValidateRequest USERID="775WASHI4754"><Address ID="0"><Address1></Address1><Address2>
             EOT;
        $request_XML .= $Address;           
        $request_XML .= "</Address2><City>";        
        $request_XML .= $City;
        $request_XML .= "</City><State>";
        $request_XML .= $State;
        $request_XML .= "</State><Zip5>";
        $request_XML .= $Zip;
        $request_XML .= "</Zip5><Zip4></Zip4></Address></AddressValidateRequest>";
        $request_XML = urlencode($request_XML);

        $url = "http://production.shippingapis.com/ShippingAPI.dll?API=Verify&XML=" . $request_XML;
        $response = file_get_contents($url);
        $responseXML = simplexml_load_string($response);

        return $responseXML->Address->Zip4;
    }

    /*********************************************
     *  Get tax code from WA State tax web service
     ********************************************/
    public static function FetchTaxCode($Address, $City, $Zip)
    {
        $TaxCode = "";
        // URL for WA tax API
        $URL = "https://webgis.dor.wa.gov/webapi/AddressRates.aspx?output=text&addr=" . urlencode($Address) . "&city=" . urlencode($City) . "&zip=" . urlencode($Zip);
        $response = wp_remote_get($URL);
        if(! is_wp_error($response))
        {
            $responseXML = $response['body'];
        }
        else
        {
            echo("error in web get: <br/>");
        }
        
        
        if(strpos($responseXML, 'LocationCode=') > -1)
        {
            $output = substr($responseXML, 13, strpos($responseXML, " ") - 13);
        }
        
        return $output;
    }

    /*************************************
     *  Using get posts with meta query to pull orders for the date range.
     *  Return order data to calling object
     *  Returns customer order information to process. This returns all completed orders within the date range. 
     *************************************/
    public static function FetchTaxData($StartDate, $EndDate)
    {        
        $query_args = array(
            'post_type' => 'shop_order',
            'numberposts' => '-1',
            'post_status' => 'wc-completed',
            'meta_key' => '_date_completed',
            'meta_query' => array( 
                'relation' => 'AND',
                array(
                    'key' => '_date_completed', // Check the start date field
                    'value' => strtotime($StartDate),
                    'compare' => '>=', // Return the ones after the start date
                    'type' => 'integer'
                ),
                array(
                    'key' => '_date_completed', // Check the end date field
                    'value' => strtotime($EndDate),
                    'compare' => '<=', // Return the ones before the end date
                    'type' => 'integer'
                    ) 
            ),
        );

        $orders = get_posts( $query_args );

        return $orders;
    }
}

