
This PHP script will submit a market buy order to GDAX to purchase bitcoin when the monitored bitcoin address from block.io sends bitcoin to another address. This script is helpful 
when running a bitcoin ATM that does not support your required exchange. The script is executed every 60 seconds by cronjob. Script fetches transactions of your block.io (or any) bitcoin 
address from the blockchain.info API and sends market buy request to GDAX API. Script then adds transaction to MySQL database so that it knows it already bought on GDAX for that transaction. 

For safety, /lib/CoinbaseExchange/CoinbaseExchange.php is set to the GDAX sandbox API. You will need to change this to https://api.gdax.com before going live. Please test this script VERY thoroughly 
before going live. 

DO NOT upload this script to a public folder on your webserver. This script is ment to run in your hosting "home" directory.

Be sure to check this repository for updates.


