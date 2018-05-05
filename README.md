# Protected.design
Protected.design uses Vue.js on the front-end. It communicates through REST API with the barebones Wordpress PHP + MySQL back-end.

To generate a SHA-256 hash, it uses CryptoJS library. It then sends the hash (and the message, if provided by the user) to a separate dedicated Linux server to generate Ethereum raw transaction and sign it offline using php-eth-raw-tx tool. Generated tx hex is returned to Protected.design and then broadcasted through Etherscan's API to the Ethereum blockchain.

Server that does the tx signing contains the private key and other sensitive information. It is not a part of this repository.


## How to run the app? ##

1. Install fresh Wordpress on your Apache web server.
2. Upload Protected.design folder from this repository to your Wordpress /wp-content/themes/ folder.
3. Update /setup/_auth.php and /setup/_auth.js files with your credentials.
4. Open /setup/create_db.php file in your browser. It will create the necessary database tables.
5. Activate Protected.design theme in your Wordpress settings.
6. Create a new empty page. Select Protected.design template and set it as a default Homepage in your Wordpress settings.

You can then use a preferred solution to sign transaction offline. Transaction hex should be returned to the app's broadcast_tx/tx endpoint with the correct auth token. Then the tx will be automatically broadcasted by the app to the Ethereum blockchain.

check_donations.php file is set to */4 cron and is checking donation address balance for incoming transactions. If someone donates any amount greater than 0.001 ETH, the script initiates a grouped protection. Grouped protection is also initiated on every Sunday GMT 00:00.

For payment processing Protected.design uses Cardinity service and its PHP library.
