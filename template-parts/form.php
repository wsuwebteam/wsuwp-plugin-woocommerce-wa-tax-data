<style>
  table{
    width: 80%;
    border-collapse: collapse;
  }
  table, td, th {
  border: 1px solid black;
  }
  td{
    text-align: left;
  }
  
</style>
<script>
  jQuery(document).ready( function($) {
    $( "#txtSDate" ).datepicker();
  });

  jQuery(document).ready( function($) {
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
<div style="padding-top: 50px;">
<?php
  if (!empty($_POST)) 
  {
    $test = new WSUWP\Plugin\WA_Tax_Query\TaxQuery;
    $result = $test->processTaxData($_POST['txtSDate'], $_POST['txtEDate']);
    echo($result);    
  }
  
?>
</div>
