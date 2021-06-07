<?php
  if (!empty($_POST)) 
  {
    $test = new WSUWP\Plugin\WA_Tax_Query\TaxQuery;
    $result = $test->processTaxData($_POST['txtSDate'], $_POST['txtEDate']);
    //var_dump($result);
    /* foreach ( $result as $key ) 
    {
        echo($key['post_id']);
        echo($key['ShipDate']);
        echo($key['CustomerFName'] . " " . $value['CustomerLName']); 
        //echo("wtf is going on here now damnit");
    } */
    //::processTaxData();
  }
  
?>




<script>
  $( function() {
    $( "#txtSDate" ).datepicker();
  });

  $( function() {
    $( "#txtEDate" ).datepicker();
  } );
</script>
<form action="" method="post">
  <label for="txtSDate">Start Date</label>
  <input type="text" id="txtSDate" name="txtSDate"></input>
  <label for="txtEDate">End Date</label>
  <input type="text" id="txtEDate" name="txtEDate"></input>
  <input type="submit" value="Submit"/>
</form>