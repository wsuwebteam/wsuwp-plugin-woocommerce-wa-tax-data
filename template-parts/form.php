<?php
  if (!empty($_POST)) 
  {
    WSUWP\Plugin\WA_Tax_Query\TaxQuery::Process($_POST['txtSDate'], $_POST['txtEDate']);
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