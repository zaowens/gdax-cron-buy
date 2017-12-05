<?php
require_once('lib/CoinbaseExchange.php');

	//Configure these settings
	$your_address   	 = ""; // Block.io Bitcoin address you send from
	$mysql_user     	 = ""; // MySQL Database Username
	$mysql_password 	 = ""; // MySQL Database User Password
	$mysql_db_name  	 = ""; // MySQL Database Name 
	$GDAX_passphrase     = ""; //GDAX
	$GDAX_key      		 = ""; //GDAX
	$GDAX_secret    	 = ""; //GDAX
	
//MySQL connect
mysql_connect("localhost","$mysql_user","$mysql_password") or die ('Database cannot connect...');
mysql_select_db("$mysql_db_name");

// Fetch JSON file 
$json = file_get_contents("https://blockchain.info/rawaddr/$your_address"); // URL has Unique Token that expires
$data = json_decode($json,true);
$feed = $data['txs'];

// Connect to GDAX
$exchange = new CoinbaseExchange();
$exchange->auth($GDAX_key, $GDAX_passphrase, $GDAX_secret);

// Sort through JSON for our $values we need
foreach ($feed as $value) {
	$input_address = $value[inputs][0][prev_out][addr]; 
	if ($input_address == "$your_address") {
		$transaction = $value[hash];
		// Check if transaction is already in DB by looking for its blockchain unique hash
		$check_transaction = mysql_num_rows(mysql_query("select id from transactions where bitcoin_transaction='$transaction'"));
		if ($check_transaction == 0) {
			$total = $value[out][0][value];
			$total = $total * 0.00000001;
			print"$total - $transaction<BR>";
			mysql_query("insert into transactions (bitcoin_transaction, bitcoin_amount) VALUES ('$transaction',$total)")  or die ('INSERT ERROR.');
			
			// Connect to GDAX and preform MARKET trade for amount of bitcoin transfered out of Block.io wallet
			$exchange->placeOrderMarket('buy', $total, 'BTC-USD');
			$log_data = "GDAX [BUY] $total BTC-USD for tx=$transaction \n";
			// Write the contents to the file, 
			// using the FILE_APPEND flag to append the content to the end of the file
			// and the LOCK_EX flag to prevent anyone else writing to the file at the same time
			file_put_contents('log.txt', $log_data, FILE_APPEND | LOCK_EX);
		}
	}
}
?>