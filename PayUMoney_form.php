<?php
// Merchant key here as provided by Payu
$MERCHANT_KEY = "rjQUPktU";

// Merchant Salt as provided by Payu
$SALT = "e5iIg1jwi8";

// End point - change to https://secure.payu.in for LIVE mode
$PAYU_BASE_URL = "https://test.payu.in";

$action = '';

$posted = array();
if(!empty($_POST)) {
    //print_r($_POST);
  foreach($_POST as $key => $value) {    
    $posted[$key] = $value; 
	
  }
}

$formError = 0;

if(empty($posted['txnid'])) {
  // Generate random transaction id
  $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
} else {
  $txnid = $posted['txnid'];
}
$hash = '';
// Hash Sequence
$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
if(empty($posted['hash']) && sizeof($posted) > 0) {
  if(
          empty($posted['key'])
          || empty($posted['txnid'])
          || empty($posted['amount'])
          || empty($posted['firstname'])
          || empty($posted['email'])
          || empty($posted['phone'])
          || empty($posted['productinfo'])
          || empty($posted['surl'])
          || empty($posted['furl'])
		  || empty($posted['service_provider'])
  ) {
    $formError = 1;
  } else {
    //$posted['productinfo'] = json_encode(json_decode('[{"name":"tutionfee","description":"","value":"500","isRequired":"false"},{"name":"developmentfee","description":"monthly tution fee","value":"1500","isRequired":"false"}]'));
	$hashVarsSeq = explode('|', $hashSequence);
    $hash_string = '';	
	foreach($hashVarsSeq as $hash_var) {
      $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
      $hash_string .= '|';
    }

    $hash_string .= $SALT;


    $hash = strtolower(hash('sha512', $hash_string));
    $action = $PAYU_BASE_URL . '/_payment';
  }
} elseif(!empty($posted['hash'])) {
  $hash = $posted['hash'];
  $action = $PAYU_BASE_URL . '/_payment';
}
?>
<html>
    <head>
        <title>Fill Your Details To Pay Money</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <script>
            var hash = '<?php echo $hash ?>';
            function submitPayuForm() {
              if(hash == '') {
                return;
              }
              var payuForm = document.forms.payuForm;
              payuForm.submit();
            }
        </script>
  </head>
  <body onload="submitPayuForm()" style="background-color: #efefef;">
		<header class="header" >
			<nav class="navbar navbar-style" style="background-color: #a5c339;">
				<div class="container" >
					<div class="navbar-header">
						<img class="logo" src="https://media-exp1.licdn.com/dms/image/C560BAQFgHU3sTF4LfQ/company-logo_200_200/0/1519895156650?e=2159024400&v=beta&t=1iqBaESC2l4UUW7JjEjq0R_HQhwRTaaqyQG1k46q4bs">
					</div>
					<ul class="nav navbar-nav navbar-right">
						<li><a href="dashboard.php" style="color: white; font-size: 16px;">Home</a></li>
						<li><a href="PayUMoney_form.php" style="color: white; font-size: 16px;">Donate</a></li>
					</ul>
				</div>
			</nav>
		</header>
        <div class="container">
    	    <div class="thumbnail">
	        <center>
                <h2>PayU Form</h2><br/>
            <?php if($formError) { ?>
              <span style="color:red">Please fill all mandatory fields.</span>
              <br/>
              <br/>
            <?php } ?>
            <form action="<?php echo $action; ?>" method="post" name="payuForm">
              <div class="form-group">
                  <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
                  <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
                  <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
                  <input type="hidden" name="hash_abc" value="<?php echo $hash_string ?>"/>
                  <table>
                    <tr>
                      <td>Amount: </td>
                      <td><input name="amount" class="form-control" value="<?php echo (empty($posted['amount'])) ? '' : $posted['amount'] ?>" /></td>
                    </tr>
                    <tr>
                      <td>Name: </td>
                      <td><input name="firstname" id="firstname" class="form-control" value="<?php echo (empty($posted['firstname'])) ? '' : $posted['firstname']; ?>" /></td>
                    </tr>
                    <tr>
                      <td>Email: </td>
                      <td><input name="email" id="email" class="form-control" value="<?php echo (empty($posted['email'])) ? '' : $posted['email']; ?>" /></td>
                    </tr>
                    <tr>
                      <td>Phone: </td>
                      <td><input name="phone" class="form-control" value="<?php echo (empty($posted['phone'])) ? '' : $posted['phone']; ?>" /></td>
                    </tr>
                    <tr>
                      <td>Product Info: </td>
                      <td colspan="3"><textarea name="productinfo" class="form-control"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea></td>
                    </tr>
                    <tr>
                      <td>Success URL: </td>
                      <td colspan="3"><input name="surl" class="form-control" value="<?php echo (empty($posted['surl'])) ? '' : $posted['surl'] ?>" size="64" /></td>
                    </tr>
                    <tr>
                      <td>Failure URL: </td>
                      <td colspan="3"><input name="furl" class="form-control" value="<?php echo (empty($posted['furl'])) ? '' : $posted['furl'] ?>" size="64" /></td>
                    </tr>
                    <tr>
                      <td colspan="3"><input type="hidden" name="service_provider" value="payu_paisa" size="64" /></td>
                    </tr>
                    <tr>
                      <?php if(!$hash) { ?>
                        <td colspan="4"><input type="submit"  class="btn btn-primary" value="Submit" /></td>
                      <?php } ?>
                    </tr>
                  </table>
              </div>
            </form>
            </center>
            </div>
        </div>
  </body>
</html>