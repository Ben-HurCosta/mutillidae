    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
    <html>
    	<head>
    		<link rel="shortcut icon" href="./images/favicon.ico" type="image/x-icon" />
    		<link rel="stylesheet" type="text/css" href="./styles/global-styles.css" />
    	</head>
    	<body>
    		<div>&nbsp;</div>
    		<div class="page-title">Setting up the database...</div><br /><br />
    		<div class="label" style="text-align: center;">If you see no error messages, it should be done.</div>
    		<div>&nbsp;</div>
    		<div class="label" style="text-align: center;"><a href="index.php">Continue back to the frontpage.</a></div>
    		<br />
    		<script>
    			try{
    				window.sessionStorage.clear();
    				window.localStorage.clear();
    			}catch(e){
    				alert("Error clearing HTML 5 Local and Session Storage" + e.toString());
    			};
    		</script>
    		<div class="database-success-message">HTML 5 Local and Session Storage cleared unless error popped-up already.</div>
    <?php

    //Here because of very weird error
    session_start();

    //initialize custom error handler
    require_once 'classes/CustomErrorHandler.php';
    if (!isset($CustomErrorHandler)){
    	$CustomErrorHandler =
    	new CustomErrorHandler("owasp-esapi-php/src/", 0);
    }// end if

    require_once 'classes/MySQLHandler.php';
    $MySQLHandler = new MySQLHandler("owasp-esapi-php/src/", $_SESSION["security-level"]);
    $lErrorDetected = FALSE;

    function format($pMessage, $pLevel ) {
    	switch ($pLevel){
    		case "I": $lStyle = "database-informative-message";break;
    		case "S": $lStyle = "database-success-message";break;
    		case "F": $lStyle = "database-failure-message";break;
    		case "W": $lStyle = "database-warning-message";break;
    	}// end switch

    	return "<div class=\"".$lStyle."\">" . $pMessage . "</div>";
    }// end function

    try{
    	echo format("Attempting to connect to MySQL server on host " . MySQLHandler::$mMySQLDatabaseHost . " with user name " . MySQLHandler::$mMySQLDatabaseUsername,"I");
    	$MySQLHandler->openDatabaseConnection();
    	echo format("Connected to MySQL server at " . MySQLHandler::$mMySQLDatabaseHost . " as " . MySQLHandler::$mMySQLDatabaseUsername,"I");

    	try{
    		echo format("Preparing to drop database " . MySQLHandler::$mMySQLDatabaseName,"I");
    		$lQueryString = "DROP DATABASE IF EXISTS " . MySQLHandler::$mMySQLDatabaseName;
    		$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    		if (!$lQueryResult) {
    			$lErrorDetected = TRUE;
    			echo format("Was not able to drop database " . MySQLHandler::$mMySQLDatabaseName,"F");
    		}else{
    			echo format("Executed query 'DROP DATABASE IF EXISTS' for database " . MySQLHandler::$mMySQLDatabaseName . " with result ".$lQueryResult,"S");
    		}// end if
    	}catch(Exception $e){
    		// We do not want error dropping database to derail entire database setup.
    		echo format("Error was reported while attempting to drop database " . MySQLHandler::$mMySQLDatabaseName,"F");
    		echo format("MySQL sometimes throws errors attempting to drop databases. Here is error in case the error is serious.","I");
    		echo $CustomErrorHandler->FormatError($e, $lQueryString);
    	}//end try

    	echo format("Preparing to create database " . MySQLHandler::$mMySQLDatabaseName,"I");
    	$lQueryString = "CREATE DATABASE " . MySQLHandler::$mMySQLDatabaseName;
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    		echo format("Was not able to create database " . MySQLHandler::$mMySQLDatabaseName,"F");
    	}else{
    		echo format("Executed query 'CREATE DATABASE' for database " . MySQLHandler::$mMySQLDatabaseName . " with result ".$lQueryResult,"S");
    	}// end if

    	echo format("Switching to use database " . MySQLHandler::$mMySQLDatabaseName,"I");
    	$lQueryString = "USE " . MySQLHandler::$mMySQLDatabaseName;
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    		echo format("Was not able to use database " . MySQLHandler::$mMySQLDatabaseName,"F");
    	}else{
    		echo format("Executed query 'USE DATABASE' " . MySQLHandler::$mMySQLDatabaseName . " with result ".$lQueryResult,"I");
    	}// end if

    	$lQueryString = 'CREATE TABLE user_poll_results( '.
    			'cid INT NOT NULL AUTO_INCREMENT, '.
    			'tool_name TEXT, '.
    			'username TEXT, '.
    			'date DATETIME, '.
    			'PRIMARY KEY(cid))';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo format("Executed query 'CREATE TABLE' with result ".$lQueryResult,"S");
    	}// end if

    	$lQueryString = 'CREATE TABLE blogs_table( '.
    			 'cid INT NOT NULL AUTO_INCREMENT, '.
    	         'blogger_name TEXT, '.
    	         'comment TEXT, '.
    			 'date DATETIME, '.
    			 'PRIMARY KEY(cid))';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo format("Executed query 'CREATE TABLE' with result ".$lQueryResult,"S");
    	}// end if

    	$lQueryString = 'CREATE TABLE accounts( '.
    			 'cid INT NOT NULL AUTO_INCREMENT, '.
    	         'username TEXT, '.
    	         'password TEXT, '.
    			 'mysignature TEXT, '.
    			 'is_admin VARCHAR(5),'.
    			 'firstname TEXT, '.
    			 'lastname TEXT, '.
    			 'PRIMARY KEY(cid))';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo format("Executed query 'CREATE TABLE' with result ".$lQueryResult,"S");
    	}// end if

    	$lQueryString = 'CREATE TABLE hitlog( '.
    			 'cid INT NOT NULL AUTO_INCREMENT, '.
    	         'hostname TEXT, '.
    	         'ip TEXT, '.
    			 'browser TEXT, '.
    			 'referer TEXT, '.
    			 'date DATETIME, '.
    			 'PRIMARY KEY(cid))';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo format("Executed query 'CREATE TABLE' with result ".$lQueryResult,"S");
    	}// end if

    	$lQueryString = "INSERT INTO accounts (username, password, mysignature, is_admin, firstname, lastname) VALUES
    		('admin', 'adminpass', 'g0t r00t?', 'TRUE' ,'System' ,'Administrator'),
    		('adrian', 'somepassword', 'Zombie Films Rock!', 'TRUE' ,'Adrian' ,'Crenshaw'),
    		('john', 'monkey', 'I like the smell of confunk', 'FALSE' ,'John' ,'Pentest'),
    		('jeremy', 'password', 'd1373 1337 speak', 'FALSE' ,'Jeremy' ,'Druin'),
    		('bryce', 'password', 'I Love SANS', 'FALSE' ,'Bryce' ,'Galbraith'),
    		('samurai', 'samurai', 'Carving fools', 'FALSE' ,'Samurai' ,'WTF'),
    		('jim', 'password', 'Rome is burning', 'FALSE' ,'Jim' ,'Rome'),
    		('bobby', 'password', 'Hank is my dad', 'FALSE' ,'Bobby' ,'Hill'),
    		('simba', 'password', 'I am a super-cat', 'FALSE' ,'Simba' ,'Lion'),
    		('dreveil', 'password', 'Preparation H', 'FALSE' ,'Dr.' ,'Evil'),
    		('scotty', 'password', 'Scotty do', 'FALSE' ,'Scotty' ,'Evil'),
    		('cal', 'password', 'C-A-T-S Cats Cats Cats', 'FALSE' ,'John' ,'Calipari'),
    		('john', 'password', 'Do the Duggie!', 'FALSE' ,'John' ,'Wall'),
    		('kevin', '42', 'Doug Adams rocks', 'FALSE' ,'Kevin' ,'Johnson'),
    		('dave', 'set', 'Bet on S.E.T. FTW', 'FALSE' ,'Dave' ,'Kennedy'),
    		('patches', 'tortoise', 'meow', 'FALSE' ,'Patches' ,'Pester'),
    		('rocky', 'stripes', 'treats?', 'FALSE' ,'Rocky' ,'Paws'),
    		('tim', 'lanmaster53', 'Because reconnaissance is hard to spell', 'FALSE' ,'Tim' ,'Tomes'),
    		('ABaker', 'SoSecret', 'Muffin tops only', 'TRUE' ,'Aaron' ,'Baker'),
    		('PPan', 'NotTelling', 'Where is Tinker?', 'FALSE' ,'Peter' ,'Pan'),
    		('CHook', 'JollyRoger', 'Gator-hater', 'FALSE' ,'Captain' ,'Hook'),
    		('james', 'i<3devs', 'Occupation: Researcher', 'FALSE' ,'James' ,'Jardine'),
    		('ed', 'pentest', 'Commandline KungFu anyone?', 'FALSE' ,'Ed' ,'Skoudis')";
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo "<div class=\"database-success-message\">Executed query 'INSERT INTO TABLE' with result ".$lQueryResult."</div>";
    	}// end if

    	$lQueryString ="INSERT INTO `blogs_table` (`cid`, `blogger_name`, `comment`, `date`) VALUES
    		(1, 'adrian', 'Well, I''ve been working on this for a bit. Welcome to my crappy blog software. :)', '2009-03-01 22:26:12'),
    		(2, 'adrian', 'Looks like I got a lot more work to do. Fun, Fun, Fun!!!', '2009-03-01 22:26:54'),
    		(3, 'anonymous', 'An anonymous blog? Huh? ', '2009-03-01 22:27:11'),
    		(4, 'ed', 'I love me some Netcat!!!', '2009-03-01 22:27:48'),
    		(5, 'john', 'Listen to Pauldotcom!', '2009-03-01 22:29:04'),
    		(6, 'jeremy', 'Mutillidae is fun', '2009-03-01 22:29:49'),
    		(7, 'john', 'Chocolate is GOOD!!!', '2009-03-01 22:30:06'),
    		(8, 'admin', 'Fear me, for I am ROOT!', '2009-03-01 22:31:13'),
    		(9, 'dave', 'Social Engineering is woot-tastic', '2009-03-01 22:31:13'),
    		(10, 'kevin', 'Read more Douglas Adams', '2009-03-01 22:31:13'),
    		(11, 'kevin', 'You should take SANS SEC542', '2009-03-01 22:31:13'),
    		(12, 'asprox', 'Fear me, for I am asprox!', '2009-03-01 22:31:13')";
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo "<div class=\"database-success-message\">Executed query 'INSERT INTO TABLE' with result ".$lQueryResult."</div>";
    	}// end if

    	$lQueryString = 'CREATE TABLE credit_cards( '.
    			 'ccid INT NOT NULL AUTO_INCREMENT, '.
    	         'ccnumber TEXT, '.
    	         'ccv TEXT, '.
    			 'expiration DATE, '.
    			 'PRIMARY KEY(ccid))';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo format("Executed query 'CREATE TABLE' with result ".$lQueryResult,"S");
    	}// end if

    	$lQueryString ="INSERT INTO `credit_cards` (`ccid`, `ccnumber`, `ccv`, `expiration`) VALUES
    		(1, '4444111122223333', '745', '2012-03-01 10:01:12'),
    		(2, '7746536337776330', '722', '2015-04-01 07:00:12'),
    		(3, '8242325748474749', '461', '2016-03-01 11:55:12'),
    		(4, '7725653200487633', '230', '2017-06-01 04:33:12'),
    		(5, '1234567812345678', '627', '2018-11-01 13:31:13')";

    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo "<div class=\"database-success-message\">Executed query 'INSERT INTO TABLE' with result ".$lQueryResult."</div>";
    	}// end if

    	$lQueryString =
    			'CREATE TABLE pen_test_tools('.
    			'tool_id INT NOT NULL AUTO_INCREMENT, '.
    	        'tool_name TEXT, '.
    	        'phase_to_use TEXT, '.
    			'tool_type TEXT, '.
    			'comment TEXT, '.
    			'PRIMARY KEY(tool_id))';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo format("Executed query 'CREATE TABLE' with result ".$lQueryResult,"S");
    	}// end if

    	$lQueryString ="INSERT INTO `pen_test_tools` (`tool_id`, `tool_name`, `phase_to_use`, `tool_type`, `comment`) VALUES
    		(1, 'WebSecurify', 'Discovery', 'Scanner', 'Can capture screenshots automatically'),
    		(2, 'Grendel-Scan', 'Discovery', 'Scanner', 'Has interactive-mode. Lots plug-ins. Includes Nikto. May not spider JS menus well.'),
    		(3, 'Skipfish', 'Discovery', 'Scanner', 'Agressive. Fast. Uses wordlists to brute force directories.'),
    		(4, 'w3af', 'Discovery', 'Scanner', 'GUI simple to use. Can call sqlmap. Allows scan packages to be saved in profiles. Provides evasion, discovery, brute force, vulneraility assessment (audit), exploitation, pattern matching (grep).'),
    		(5, 'Burp-Suite', 'Discovery', 'Scanner', 'GUI simple to use. Provides highly configurable manual scan assistence with productivity enhancements.'),
    		(6, 'Netsparker Community Edition', 'Discovery', 'Scanner', 'Excellent spider abilities and reporting. GUI driven. Runs on Windows. Good at SQLi and XSS detection. From Mavituna Security. Professional version available for purchase.'),
    		(7, 'NeXpose', 'Discovery', 'Scanner', 'GUI driven. Runs on Windows. From Rapid7. Professional version available for purchase. Updates automatically. Requires large amounts of memory.'),
    		(8, 'Hailstorm', 'Discovery', 'Scanner', 'From Cenzic. Professional version requires dedicated staff, multiple dediciated servers, professional pen-tester to analyze results, and very large license fee. Extensive scanning ability. Very large vulnerability database. Highly configurable. Excellent reporting. Can scan entire networks of web applications. Extremely expensive. Requires large amounts of memory.'),
    		(9, 'Tamper Data', 'Discovery', 'Interception Proxy', 'Firefox add-on. Easy to use. Tampers with POST parameters and HTTP Headers. Does not tamper with URL query parameters. Requires manual browsing.'),
    		(10, 'DirBuster', 'Discovery', 'Fuzzer', 'OWASP tool. Fuzzes directory names to brute force directories.'),
    		(11, 'SQL Inject Me', 'Discovery', 'Fuzzer', 'Firefox add-on. Attempts common strings which elicit XSS responses. Not compatible with Firefox 8.0.'),
    		(12, 'XSS Me', 'Discovery', 'Fuzzer', 'Firefox add-on. Attempts common strings which elicit responses from databases when SQL injection is present. Not compatible with Firefox 8.0.'),
    		(13, 'GreaseMonkey', 'Discovery', 'Browser Manipulation Tool', 'Firefox add-on. Allows the user to inject Javascripts and change page.'),
    		(14, 'NSLookup', 'Reconnaissance', 'DNS Server Query Tool', 'DNS query tool can query DNS name or reverse lookup on IP. Set debug for better output. Premiere tool on Windows but Linux perfers Dig. DNS traffic generally over UDP 53 unless response long then over TCP 53. Online version combined with anonymous proxy or TOR network may be prefered for stealth.'),
    		(15, 'Whois', 'Reconnaissance', 'Domain name lookup service', 'Whois is available in Linux naitvely and Windows as a Sysinternals download plus online. Whois can lookup the registrar of a domain and the IP block associated. An online version is http://network-tools.com/'),
    		(16, 'Dig', 'Reconnaissance', 'DNS Server Query Tool', 'The Domain Information Groper is prefered on Linux over NSLookup and provides more information natively. NSLookup must be in debug mode to give similar output. DIG can perform zone transfers if the DNS server allows transfers.'),
    		(17, 'Fierce Domain Scanner', 'Reconnaissance', 'DNS Server Query Tool', 'Powerful DNS scan tool. FDS is a Perl program which scans and reverse scans a domain plus scans IPs within the same block to look for neighoring machines. Available in the Samurai and Backtrack distributions plus http://ha.ckers.org/fierce/'),
    		(18, 'host', 'Reconnaissance', 'DNS Server Query Tool', 'A simple DNS lookup tool included with BIND. The tool is a friendly and capible command line tool with excellent documentation. Does not posess the automation of FDS.'),
    		(19, 'zaproxy', 'Reconnaissance', 'Interception Proxy', 'OWASP Zed Attack Proxy. An interception proxy that can also passively or actively scan applications as well as perform brute-forcing. Similar to Burp-Suite without the disadvantage of requiring a costly license.'),
    		(20, 'Google intitle', 'Discovery', 'Search Engine','intitle and site directives allow directory discovery. GHDB available to provide hints. See Hackers for Charity site.')";
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo "<div class=\"database-success-message\">Executed query 'INSERT INTO TABLE' with result ".$lQueryResult."</div>";
    	}// end if

    	$lQueryString =
    			'CREATE TABLE captured_data('.
    				'data_id INT NOT NULL AUTO_INCREMENT, '.
    				'ip_address TEXT, '.
    				'hostname TEXT, '.
    				'port TEXT, '.
    				'user_agent_string TEXT, '.
    				'referrer TEXT, '.
    				'data TEXT, '.
    			 	'capture_date DATETIME, '.
    				'PRIMARY KEY(data_id)'.
    			')';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo format("Executed query 'CREATE TABLE' with result ".$lQueryResult,"S");
    	}// end if

    	$lQueryString =
    			'CREATE TABLE page_hints('.
    				'page_name VARCHAR(64) NOT NULL, '.
    				'hint_key INT, '.
    				'hint TEXT, '.
    				'PRIMARY KEY(page_name, hint_key)'.
    			')';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo format("Executed query 'CREATE TABLE' with result ".$lQueryResult,"S");
    	}// end if

    	$lQueryString =
    			'CREATE TABLE page_help('.
    				'page_name VARCHAR(64) NOT NULL, '.
    				'help_text_key INT, '.
    				'order_preference INT, '.
    				'PRIMARY KEY(page_name, help_text_key)'.
    			')';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo format("Executed query 'CREATE TABLE' with result ".$lQueryResult,"S");
    	}// end if

    	$lQueryString ="INSERT INTO `page_help` (`page_name`, `help_text_key`, `order_preference`) VALUES
    			('home.php', 0, 5),
    			('home.php', 1, 5),
    			('home.php', 2, 5),
    			('home.php', 3, 5),
    			('home.php', 4, 5),
    			('home.php', 5, 5),
    			('home.php', 6, 5),
    			('home.php', 7, 0),
    			('home.php', 9, 5),
    			('home.php', 24, 5),
    			('home.php', 39, 5),
    			('home.php', 40, 5),
    			('home.php', 57, 5),
    			('home.php', 56, 5),
    			('home.php', 59, 5),
    			('home.php', 60, 0),
    			('home.php', 61, 3),
    			('home.php', 62, 1),
    			('home.php', 64, 4),
    			('add-to-your-blog.php', 8, 0),
    			('add-to-your-blog.php', 10, 2),
    			('add-to-your-blog.php', 53, 2),
    			('add-to-your-blog.php', 11, 3),
    			('add-to-your-blog.php', 55, 3),
    			('add-to-your-blog.php', 12, 1),
    			('add-to-your-blog.php', 13, 1),
    			('add-to-your-blog.php', 14, 1),
    			('add-to-your-blog.php', 30, 1),
    			('add-to-your-blog.php', 48, 1),
    			('add-to-your-blog.php', 54, 1),
    			('add-to-your-blog.php', 56, 1),
    			('add-to-your-blog.php', 59, 1),
    			('arbitrary-file-inclusion.php', 11, 3),
    			('arbitrary-file-inclusion.php', 55, 3),
    			('arbitrary-file-inclusion.php', 12, 1),
    			('arbitrary-file-inclusion.php', 15, 0),
    			('arbitrary-file-inclusion.php', 16, 1),
    			('arbitrary-file-inclusion.php', 17, 1),
    			('arbitrary-file-inclusion.php', 39, 1),
    			('arbitrary-file-inclusion.php', 40, 1),
    			('arbitrary-file-inclusion.php', 56, 1),
    			('arbitrary-file-inclusion.php', 59, 1),
    			('back-button-discussion.php', 11, 3),
    			('back-button-discussion.php', 55, 3),
    			('back-button-discussion.php', 12, 1),
    			('back-button-discussion.php', 18, 1),
    			('back-button-discussion.php', 19, 1),
    			('back-button-discussion.php', 56, 1),
    			('back-button-discussion.php', 59, 1),
    			('browser-info.php', 11, 3),
    			('browser-info.php', 55, 3),
    			('browser-info.php', 12, 1),
    			('browser-info.php', 18, 1),
    			('browser-info.php', 56, 1),
    			('browser-info.php', 59, 1),
    			('capture-data.php', 1, 1),
    			('capture-data.php', 10, 2),
    			('capture-data.php', 53, 2),
    			('capture-data.php', 11, 3),
    			('capture-data.php', 55, 3),
    			('capture-data.php', 12, 1),
    			('capture-data.php', 48, 1),
    			('captured-data.php', 11, 3),
    			('captured-data.php', 55, 3),
    			('captured-data.php', 12, 1),
    			('captured-data.php', 56, 1),
    			('captured-data.php', 59, 1),
    			('client-side-comments.php', 57, 1),
    			('client-side-comments.php', 56, 1),
    			('client-side-comments.php', 59, 1),
    			('client-side-control-challenge.php', 11, 3),
    			('client-side-control-challenge.php', 55, 3),
    			('client-side-control-challenge.php', 12, 1),
    			('client-side-control-challenge.php', 13, 1),
    			('client-side-control-challenge.php', 30, 1),
    			('client-side-control-challenge.php', 51, 1),
    			('client-side-control-challenge.php', 56, 1),
    			('client-side-control-challenge.php', 59, 1),
    			('conference-room-lookup.php', 29, 1),
    			('conference-room-lookup.php', 30, 1),
    			('conference-room-lookup.php', 56, 1),
    			('conference-room-lookup.php', 59, 1),
    			('conference-room-lookup.php', 63, 1),
    			('conference-room-lookup.php', 64, 1),
    			('content-security-policy.php', 11, 3),
    			('content-security-policy.php', 55, 3),
    			('content-security-policy.php', 12, 1),
    			('content-security-policy.php', 13, 1),
    			('content-security-policy.php', 20, 1),
    			('content-security-policy.php', 30, 1),
    			('content-security-policy.php', 48, 1),
    			('content-security-policy.php', 56, 1),
    			('content-security-policy.php', 59, 1),
    			('content-security-policy.php', 65, 1),
    			('cors.php', 11, 3),
    			('cors.php', 55, 3),
    			('cors.php', 12, 1),
    			('cors.php', 13, 1),
    			('cors.php', 20, 1),
    			('cors.php', 30, 1),
    			('cors.php', 48, 1),
    			('cors.php', 56, 1),
    			('cors.php', 59, 1),
    			('cors.php', 67, 1),
    			('credits.php', 19, 1),
    			('credits.php', 56, 1),
    			('credits.php', 59, 1),
    			('directory-browsing.php', 9, 1),
    			('directory-browsing.php', 56, 1),
    			('directory-browsing.php', 59, 1),
    			('dns-lookup.php', 11, 3),
    			('dns-lookup.php', 55, 3),
    			('dns-lookup.php', 12, 1),
    			('dns-lookup.php', 13, 1),
    			('dns-lookup.php', 20, 1),
    			('dns-lookup.php', 30, 1),
    			('dns-lookup.php', 48, 1),
    			('dns-lookup.php', 56, 1),
    			('dns-lookup.php', 59, 1),
    			('document-viewer.php', 11, 3),
    			('document-viewer.php', 55, 3),
    			('document-viewer.php', 12, 1),
    			('document-viewer.php', 21, 1),
    			('document-viewer.php', 30, 1),
    			('document-viewer.php', 41, 1),
    			('document-viewer.php', 48, 1),
    			('document-viewer.php', 56, 1),
    			('document-viewer.php', 59, 1),
    			('echo.php', 11, 3),
    			('echo.php', 55, 3),
    			('echo.php', 12, 1),
    			('echo.php', 13, 1),
    			('echo.php', 20, 1),
    			('echo.php', 30, 1),
    			('echo.php', 48, 1),
    			('echo.php', 56, 1),
    			('echo.php', 59, 1),
    			('edit-account-profile.php', 10, 2),
    			('edit-account-profile.php', 11, 3),
    			('edit-account-profile.php', 12, 1),
    			('edit-account-profile.php', 14, 1),
    			('edit-account-profile.php', 16, 3),
    			('edit-account-profile.php', 30, 1),
    			('edit-account-profile.php', 48, 1),
    			('edit-account-profile.php', 53, 2),
    			('edit-account-profile.php', 54, 1),
    			('edit-account-profile.php', 55, 3),
    			('edit-account-profile.php', 56, 1),
    			('edit-account-profile.php', 59, 1),
    			('framing.php', 22, 1),
    			('framing.php', 56, 1),
    			('framing.php', 59, 1),
    			('html5-storage.php', 12, 1),
    			('html5-storage.php', 23, 1),
    			('html5-storage.php', 42, 1),
    			('html5-storage.php', 56, 1),
    			('html5-storage.php', 59, 1),
    			('login.php', 1, 1),
    			('login.php', 10, 2),
    			('login.php', 53, 2),
    			('login.php', 11, 3),
    			('login.php', 55, 3),
    			('login.php', 12, 1),
    			('login.php', 13, 1),
    			('login.php', 25, 1),
    			('login.php', 47, 1),
    			('login.php', 48, 1),
    			('login.php', 54, 1),
    			('login.php', 56, 1),
    			('login.php', 59, 1),
    			('login.php', 60, 1),
    			('password-generator.php', 1, 1),
    			('password-generator.php', 11, 3),
    			('password-generator.php', 55, 3),
    			('password-generator.php', 12, 1),
    			('password-generator.php', 18, 1),
    			('password-generator.php', 56, 1),
    			('password-generator.php', 59, 1),
    			('pen-test-tool-lookup.php', 26, 1),
    			('pen-test-tool-lookup-ajax.php', 26, 1),
    			('pen-test-tool-lookup-ajax.php', 56, 1),
    			('pen-test-tool-lookup-ajax.php', 59, 1),
    			('phpinfo.php', 27, 1),
    			('phpinfo.php', 28, 1),
    			('phpinfo.php', 29, 1),
    			('phpinfo.php', 56, 1),
    			('phpinfo.php', 59, 1),
    			('register.php', 10, 2),
    			('register.php', 11, 3),
    			('register.php', 12, 1),
    			('register.php', 14, 1),
    			('register.php', 30, 1),
    			('register.php', 48, 1),
    			('register.php', 53, 2),
    			('register.php', 54, 1),
    			('register.php', 55, 3),
    			('register.php', 56, 1),
    			('register.php', 59, 1),
    			('rene-magritte.php', 22, 1),
    			('rene-magritte.php', 56, 1),
    			('rene-magritte.php', 59, 1),
    			('robots-txt.php', 9, 1),
    			('robots-txt.php', 29, 1),
    			('robots-txt.php', 43, 1),
    			('robots-txt.php', 56, 1),
    			('robots-txt.php', 59, 1),
    			('repeater.php', 11, 3),
    			('repeater.php', 55, 3),
    			('repeater.php', 12, 1),
    			('repeater.php', 13, 1),
    			('repeater.php', 31, 1),
    			('repeater.php', 32, 1),
    			('repeater.php', 56, 1),
    			('repeater.php', 59, 1),
    			('secret-administrative-pages.php', 6, 1),
    			('secret-administrative-pages.php', 27, 1),
    			('secret-administrative-pages.php', 28, 1),
    			('secret-administrative-pages.php', 29, 1),
    			('secret-administrative-pages.php', 44, 1),
    			('secret-administrative-pages.php', 56, 1),
    			('secret-administrative-pages.php', 59, 1),
    			('set-background-color.php', 11, 3),
    			('set-background-color.php', 55, 3),
    			('set-background-color.php', 12, 1),
    			('set-background-color.php', 33, 1),
    			('set-background-color.php', 56, 1),
    			('set-background-color.php', 59, 1),
    			('show-log.php', 11, 3),
    			('show-log.php', 55, 3),
    			('show-log.php', 12, 1),
    			('show-log.php', 34, 1),
    			('show-log.php', 56, 1),
    			('show-log.php', 59, 1),
    			('site-footer-xss-discussion.php', 11, 3),
    			('site-footer-xss-discussion.php', 55, 3),
    			('site-footer-xss-discussion.php', 12, 1),
    			('site-footer-xss-discussion.php', 56, 1),
    			('site-footer-xss-discussion.php', 59, 1),
    			('source-viewer.php', 11, 3),
    			('source-viewer.php', 55, 3),
    			('source-viewer.php', 12, 1),
    			('source-viewer.php', 15, 1),
    			('source-viewer.php', 16, 1),
    			('source-viewer.php', 39, 1),
    			('source-viewer.php', 40, 1),
    			('source-viewer.php', 48, 1),
    			('source-viewer.php', 56, 1),
    			('source-viewer.php', 59, 1),
    			('styling-frame.php', 11, 3),
    			('styling-frame.php', 55, 3),
    			('styling-frame.php', 12, 1),
    			('styling-frame.php', 16, 1),
    			('styling-frame.php', 39, 1),
    			('styling-frame.php', 40, 1),
    			('styling-frame.php', 41, 1),
    			('styling-frame.php', 48, 1),
    			('styling-frame.php', 50, 1),
    			('styling-frame.php', 56, 1),
    			('styling-frame.php', 59, 1),
    			('sqlmap-targets.php', 10, 2),
    			('sqlmap-targets.php', 53, 2),
    			('sqlmap-targets.php', 56, 1),
    			('sqlmap-targets.php', 59, 1),
    			('ssl-misconfiguration.php', 1, 1),
    			('ssl-misconfiguration.php', 56, 1),
    			('ssl-misconfiguration.php', 59, 1),
    			('ssl-misconfiguration.php', 60, 1),
    			('text-file-viewer.php', 11, 3),
    			('text-file-viewer.php', 55, 3),
    			('text-file-viewer.php', 12, 1),
    			('text-file-viewer.php', 15, 1),
    			('text-file-viewer.php', 16, 1),
    			('text-file-viewer.php', 30, 1),
    			('text-file-viewer.php', 35, 1),
    			('text-file-viewer.php', 39, 1),
    			('text-file-viewer.php', 40, 1),
    			('text-file-viewer.php', 56, 1),
    			('text-file-viewer.php', 59, 1),
    			('upload-file.php', 46, 1),
    			('upload-file.php', 11, 2),
    			('upload-file.php', 55, 2),
    			('upload-file.php', 12, 2),
    			('upload-file.php', 54, 2),
    			('upload-file.php', 56, 1),
    			('upload-file.php', 59, 1),
    			('user-agent-impersonation.php', 11, 3),
    			('user-agent-impersonation.php', 55, 3),
    			('user-agent-impersonation.php', 18, 1),
    			('user-agent-impersonation.php', 45, 1),
    			('user-agent-impersonation.php', 56, 1),
    			('user-agent-impersonation.php', 59, 1),
    			('user-info.php', 1, 1),
    			('user-info.php', 10, 2),
    			('user-info.php', 53, 2),
    			('user-info.php', 11, 3),
    			('user-info.php', 55, 3),
    			('user-info.php', 12, 1),
    			('user-info.php', 13, 1),
    			('user-info.php', 30, 1),
    			('user-info.php', 54, 1),
    			('user-info.php', 56, 1),
    			('user-info.php', 59, 1),
    			('user-info-xpath.php', 1, 1),
    			('user-info-xpath.php', 11, 3),
    			('user-info-xpath.php', 55, 3),
    			('user-info-xpath.php', 12, 1),
    			('user-info-xpath.php', 13, 1),
    			('user-info-xpath.php', 30, 1),
    			('user-info-xpath.php', 49, 1),
    			('user-info-xpath.php', 54, 1),
    			('user-info-xpath.php', 58, 1),
    			('user-info-xpath.php', 56, 1),
    			('user-info-xpath.php', 59, 1),
    			('user-poll.php', 10, 2),
    			('user-poll.php', 53, 2),
    			('user-poll.php', 11, 3),
    			('user-poll.php', 55, 3),
    			('user-poll.php', 12, 1),
    			('user-poll.php', 14, 1),
    			('user-poll.php', 21, 1),
    			('user-poll.php', 30, 1),
    			('user-poll.php', 54, 1),
    			('user-poll.php', 56, 1),
    			('user-poll.php', 59, 1),
    			('view-someones-blog.php', 11, 3),
    			('view-someones-blog.php', 55, 3),
    			('view-someones-blog.php', 12, 1),
    			('view-someones-blog.php', 14, 1),
    			('view-someones-blog.php', 30, 1),
    			('view-someones-blog.php', 54, 1),
    			('view-someones-blog.php', 56, 1),
    			('view-someones-blog.php', 59, 1),
    			('view-user-privilege-level.php', 11, 3),
    			('view-user-privilege-level.php', 55, 3),
    			('view-user-privilege-level.php', 12, 1),
    			('view-user-privilege-level.php', 25, 1),
    			('view-user-privilege-level.php', 31, 1),
    			('view-user-privilege-level.php', 38, 1),
    			('view-user-privilege-level.php', 56, 1),
    			('view-user-privilege-level.php', 59, 1),
    			('xml-validator.php', 11, 3),
    			('xml-validator.php', 55, 3),
    			('xml-validator.php', 12, 2),
    			('xml-validator.php', 15, 2),
    			('xml-validator.php', 36, 2),
    			('xml-validator.php', 58, 1),
    			('xml-validator.php', 56, 1),
    			('xml-validator.php', 59, 1),
    			('jwt.php', 66, 1)
    			;";

    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo "<div class=\"database-success-message\">Executed query 'INSERT INTO TABLE' with result ".$lQueryResult."</div>";
    	}// end if

    	$lQueryString =
    		'CREATE TABLE level_1_help_include_files('.
    			'level_1_help_include_file_key INT, '.
    			'level_1_help_include_file_description TEXT, '.
    			'level_1_help_include_file TEXT, '.
    			'PRIMARY KEY(level_1_help_include_file_key)'.
    		')';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo format("Executed query 'CREATE TABLE' with result ".$lQueryResult,"S");
    	}// end if

    	/* NOTE: Be sure to keep indexes in the help_texts table
    	 * relatively the same as the level_1_help_include_files
    	 * table so we can reuse the keys in the page_help table.
    	 */
    	$lQueryString ="
    		INSERT INTO level_1_help_include_files (
    			level_1_help_include_file_key,
    			level_1_help_include_file_description,
    			level_1_help_include_file
    		) VALUES
    		(1, 'SSL Misconfiguration', 'ssl-misconfiguration-hint.inc'),
    		(9, 'Directory Browsing', 'directory-browsing-hint.inc'),
    		(10, 'SQL Injection (SQLi)', 'sql-injection-hint.inc'),
    		(11, 'Cross-site Scripting (XSS)', 'cross-site-scripting-hint.inc'),
    		(12, 'HTML Injection (HTMLi)', 'html-injection-hint.inc'),
    		(13, 'JavaScript Validation Bypass', 'javascript-validation-bypass-hint.inc'),
    		(14, 'Cross-site Request Forgery (CSRF)', 'cross-site-request-forgery-hint.inc'),
    		(16, 'Insecure Direct Object References (IDOR)', 'insecure-direct-object-reference-hint.inc'),
    		(18, 'JavaScript Injection', 'javascript-injection-hint.inc'),
    		(19, 'Unvalidated Redirects', 'unvalidated-redirects-and-forwards.inc'),
    		(20, 'Command Injection (CMDi)', 'command-injection-hint.inc'),
    		(21, 'Parameter Pollution', 'parameter-pollution-hint.inc'),
    		(22, 'Click-Jacking', 'click-jacking-hint.inc'),
    		(23, 'Document Object Model (DOM) Injection', 'dom-injection-hint.inc'),
    		(25, 'Authentication Bypass', 'authentication-bypass-hint.inc'),
    		(26, 'JavaScript Object Notation (JSON) Injection', 'json-injection-hint.inc'),
    		(27, 'Platform Path Disclosure', 'platform-path-disclosure-hint.inc'),
    		(28, 'Application Path Disclosure', 'application-path-disclosure-hint.inc'),
    		(29, 'Information Disclosure', 'information-disclosure-hint.inc'),
    		(30, 'Method Tampering', 'method-tampering-hint.inc'),
    		(31, 'Parameter Addition', 'parameter-addition-hint.inc'),
    		(32, 'Buffer Overflow', 'buffer-overflow-hint.inc'),
    		(33, 'Cascading Style Sheet (CSS) Injection', 'cascading-style-sheet-injection-hint.inc'),
    		(36, 'XML External Entity (XXE) Injection', 'xml-external-entity-attack-hint.inc'),
    		(38, 'CBC Bit-flipping Attack', 'cbc-bit-flipping-attack-hint.inc'),
    		(39, 'Local File Inclusion', 'local-file-inclusion-hint.inc'),
    		(40, 'Remote File Inclusion', 'remote-file-inclusion-hint.inc'),
    		(41, 'Frame Source Injection', 'frame-source-injection-hint.inc'),
    		(42, 'HTML-5 Web Storage Injection', 'html5-web-storage-hint.inc'),
    		(43, 'Robots.txt', 'robots-txt-hint.inc'),
    		(44, 'Secret Administrative Pages', 'secret-administrative-pages-hint.inc'),
    		(45, 'User-agent Impersonation', 'user-agent-impersonation-hint.inc'),
    		(46, 'Unrestricted File Upload', 'unrestricted-file-upload-hint.inc'),
    		(48, 'Application Log Injection', 'application-log-injection.inc'),
    		(49, 'XPath Injection', 'xpath-injection-hint.inc'),
    		(50, 'Path Relative Style-sheet Injection', 'path-relative-stylesheet-injection.inc'),
    		(51, 'Client-side Security Control Bypass', 'client-side-security-control-bypass.inc'),
    		(53, 'SQL Injection with SQLMap', 'sqlmap-hint.inc'),
    		(54, 'Insufficient Transport Layer Protection', 'insufficient-transport-layer-protection.inc'),
    		(55, 'Cross-site Scripting with BeEF Framework', 'beef-framework-hint.inc'),
    		(56, 'Using Burp-Suite', 'burp-suite-hint.inc'),
    		(57, 'Client-side Comments', 'client-side-comments.inc'),
    		(58, 'XML Entity Expansion', 'xml-entity-expansion-hint.inc'),
    		(59, 'Using OWASP Zed Attack Proxy (ZAP)', 'owasp-zap-hint.inc'),
    		(60, 'Set Up HTTPS Self-signed Certificate', 'setting-up-ssl-hint.inc'),
    		(61, 'Set Up Apache Virtual Hosts', 'setting-up-virtual-hosts-hint.inc'),
    		(62, 'Set Up Local Hostnames', 'setting-up-local-hostnames-hint.inc'),
    		(63, 'LDAP Injection', 'ldap-injection-hint.inc'),
    		(64, 'Setting up LDAP Server', 'ldap-setup-hint.inc'),
    		(65, 'Content Security Policy (CSP)', 'content-security-policy-hint.inc'),
    		(66, 'JSON Web Tokens (JWT)', 'jwt-hint.inc'),
    		(67, 'Cross-origin Resource Sharing (CORS)', 'cross-origin-resource-sharing-hint.inc'),
    		(99, 'Hints Not Found', 'hints-not-found.inc')";

    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo format("Executed query 'CREATE TABLE' with result ".$lQueryResult,"S");
    	}// end if

    	$lQueryString =
    			'CREATE TABLE help_texts('.
    				'help_text_key INT, '.
    				'help_text TEXT, '.
    				'PRIMARY KEY(help_text_key)'.
    			')';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo format("Executed query 'CREATE TABLE' with result ".$lQueryResult,"S");
    	}// end if

    	/* NOTE: Be sure to keep indexes in the help_texts table
    	 * relatively the same as the level_1_help_include_files
    	 * table so we can reuse the keys in the page_help table.
    	 */
    	$lQueryString ="INSERT INTO help_texts (help_text_key, help_text) VALUES
    		(0, 'The index page has several global vulnerabilities.'),
    		(1, '<span class=\"label\">SSLStrip</span> can be used to downgrade the connection when the Enforce SSL button is selected.'),
    		(2, 'Output fields such as the logged-in username, signature, and the footer are vulnerable to cross-site scripting.'),
    		(3, 'The hints cookie and other cookies can be hacked to login as another user and gain admin access.'),
    		(4, 'Cookies are missing the HTTPOnly attribute and may be accessed via cross-site scripting.'),
    		(5, 'Check HTML comments for database credentials.'),
    		(6, 'The \"page\" input parameter is vulnerable to insecure direct object reference. Fuzzing the parameter with administrative page names or system file paths is likely to yield results.'),
    		(7, 'This is the home page. Its primary purpose is to provide a starting page for the user and provide instructions. There are no known vulnerabilties on the home.php page.'),
    		(8, '<span class=\"label\">Stored Cross-Site Scripting</span>: Attempt to inject cross-site scripts which will be stored in the backend database. When a user visits this page, the cross-site scripts will be fetched from the database, incorporated into the HTML generated, and sent to the user browser. The user browser will execute the Javascript. One option is to inject a cross-site script which sends the user to the capture-data.php page. You can view captured data on the captured-data.php page.'),
    		(9, '<span class=\"label\">Directory Browsing</span>: The entire site is vulnerable to directory browsing. Looking at the robots.txt file can provide hints of interesting directories.'),
    		(10, '<span class=\"label\">SQL Injection</span>: Attempt to inject special database characters or SQL timing attacks into page parameters. Database errors, page defacement, or noticable delays in response may indicate SQL injection flaws. This page is vulnerable.'),
    		(11, '<span class=\"label\">Reflected Cross-Site Scripting:</span> This page is vulnerable to reflected cross-site scripting because the input is not encoded prior to be used as output. Determine which input field contributes output here and inject scripts. Try to redirect the user to the capture-data.php page which records cookies and other parameters. Visit the captured-data.php page to view captured data.'),
    		(12, '<span class=\"label\">HTML Injection</span>: It is possible to inject your own HTML into this page because the input is not encoded prior to be used as output. Determine which input field contributes output here and inject HTML, CSS, and/or Javascripts in order to alter the client-side code of this page.'),
    		(13, '<span class=\"label\">Javascript Validation Bypass</span>: Set the page to at least security level 1 to activate the javascript validation. Javascript validation can always be bypassed. Use a client-proxy like Burp-Suite to capture the request after it has left the browser. You can alter the request at that time. Also, Javascript can be disabled.'),
    		(14, '<span class=\"label\">Cross Site Request Forgery</span>: This page is vulnerable to cross-site request forgery. There are a few steps to prepare a cross-site script to carry out the cross-site request forgery. Begin by filling out the form capturing the legitimate request. Inject a stored or reflected cross-site script anywhere on the site that will cause the browser to submit a copy of the legitimate request to the server. The server will process the request as if the user had filled out the form themselves.'),
    		(15, '<span class=\"label\">System File Compromise</span>: It is possible to access system files by injecting input parameters with the pathnames of system files. The web application will fetch the system files instead of application files. The system files may be displayed and/or included in page output. Remember web applications are usually served from a system directory like /var/www or C:XAMPP. You may need to move up directories.'),
    		(16, '<span class=\"label\">Insecure Direct Object Reference</span>: This page refers directly to resources by there real name or identifier making it possible to modify the name/ID to access other resources. Determine what resources are fetched. Provide the name or ID of a different resource. Resources can be filenames, record identifiers or others.'),
    		(17, '<span class=\"label\">Server Side Include</span>: It is possible to make the application include application files in this page that are not intended. These files may even come from other sites.'),
    		(18, '<span class=\"label\">Javascript Injection</span>: This page uses at least some of the input from the user to generate Javascript code. Usually in these cases the user input is used to create either a Javascript string or JSON object. Attempt to inject input which when incorporated with the page will form a syntactically correct Javascript statement. This will allow the injection to execute in the context of the browser.'),
    		(19, '<span class=\"label\">Unvalidated Redirects and Forwards</span>: This page refers directly to dynamic URLs. If the user clicks on one of the link, the URL embedded is passed to a page which performs redirection. Try to over-write one of the intended pages beind passed to redirect the user to an arbitrary page. Give the poisoned link you create to a freind and see if they are redirected to a site of your choosing.'),
    		(20, '<span class=\"label\">Operating System Command Injection</span>:  Command injection may occur when a web application passes user input in part or in whole to the operating system for execution. This page incorporates user input into a larger statement that is submitted to an operating system shell for execution. Try to determine the operating system in use. Enter characters that are reserved in shells; especially characters used to concatenate commands.'),
    		(21, '<span class=\"label\">HTTP Parameter Pollution</span>: If multiple parameters with the same name are sent in a request, different application servers will react differently. PHP takes only one of the parameters but not neccesarily the parameters intended by the developer. By duplicating parameters with a value of your choosing and placing that parameters before and-or after the pages native parameters, you can influence the pages behavior. Note that ASP and Java application servers act different.'),
    		(22, '<span class=\"label\">Click-jacking</span>: By placing an invisible overlay over top of a legitimate page, a malicious agent can hijack a users mouse clicks. To overlay the vulnerable page, the malicious agent will host the victim page inside a full page frame with no borders.'),
    		(23, '<span class=\"label\">Document Object Model (DOM) Injection</span>: User input is incorporated into the document object model (DOM) of the page itself. This allows a user to inject HTML which will be incorporated into the source code of the page. The browser will execute this new code immediately.'),
    		(24, 'The UID cookie is used in an SQL query allowing SQL injection via a cookie value.'),
    		(25, '<span class=\"label\">Authentication Bypass</span>: Authentication bypass can be achieved by either hacking the UID cookie or by SQL injecting the login.'),
    		(26, '<span class=\"label\">Javascript Object Notation (JSON) Injection</span>: This page uses JSON to pass data which is later parsed and incorporated into the page. Because the output is not properly encoded, it is possible to carefully craft an injection which will add extra data into the JSON without breaking the JSON syntax. This extra data will be executed by the browser once the data is incorporated into the page.'),
    		(27, '<span class=\"label\">Platform Path Disclosure</span>: Internal system paths are disclosed by this page under certain conditions.'),
    		(28, '<span class=\"label\">Application Path Disclosure</span>: Application file paths are disclosed by this page under certain conditions.'),
    		(29, '<span class=\"label\">Information Disclosure</span>: This page gives away internal system information, configuration information, paths, filenames, or other private information.'),
    		(30, '<span class=\"label\">Method Tampering</span>: Because the page does not specify that the input parameters must be posted, it is possible to submit input parameters via a post or a get. This is a second order vulnerability allowing other vulnerabilities to be exploited easier.'),
    		(31, '<span class=\"label\">Parameter Addition</span>: If extra parameters are submitted, the page will include them in output. A parameter can be added containing scripts which will be executed when loaded in the users browser.'),
    		(32, '<span class=\"label\">Buffer Overflow</span>: If very long input is submitted, it is possible to exhaust the available space alloted on the heap.'),
    		(33, '<span class=\"label\">Cascading Style Sheet Injection</span>: CSS styles can be used to interpret and execute Javascript. If styles can be injected, it is possible to inject a style with embedded Javascript which will be executed when loaded into the users browser.'),
    		(34, '<span class=\"label\">Denial of Service</span>: This page allows denial of service. DOS can be performed by exhausting system resource(s) such as filling up disk drives or consuming available network bandwidth.'),
    		(35, '<span class=\"label\">Phishing/Remote File Inclusion</span>: Due to defects allowing arbitrary web pages to be loaded into this pages frames, phishing and malware downloads are possible.'),
    		(36, '<span class=\"label\">XML External Entity Injection Attack</span>: This page parses XML which the user can influence. If external entities embedded in the XML contain system file directives, it is possible to cause the page to load system files and include the contents in the XML output.'),
    		(38, '<span class=\"label\">Cipher Block Chaining (CBC) Bit Flipping Attack</span>: This page is vulnerable to CBC bit flipping attack.'),
    		(39, '<span class=\"label\">Local File Inclusion</span>: This page is vulnerable to local file inclusion if the user account under which PHP is running has access to files besides the intended web site files.'),
    		(40, '<span class=\"label\">Remote File Inclusion</span>: This page is vulnerable to remote file inclusion if the PHP server configuration parameters \"allow_url_fopen\" and \"allow_url_include\" are set to \"On\" in php.ini.'),
    		(41, '<span class=\"label\">Frame Source Injection</span>: By controlling the parameter which determines the src attribute of a pages frame, a carefully injected value can load any arbitrary page into the frame.'),
    		(42, '<span class=\"label\">HTML 5 Web Storage Theft and Manipulation</span>: Using a cross site scripting attack, this page is vulnerable to having an attacker read, insert, update, or delete the values stored in the HTML5 web storage.'),
    		(43, '<span class=\"label\">Robots.txt</span>: This file gives away sensitive file paths.'),
    		(44, '<span class=\"label\">Secret Administrative Pages</span>: These pages are obscured by not being linked from other pages but they can be found using other vulnerabilities such as directory browsing, robots.txt, and local file inclusion.'),
    		(45, '<span class=\"label\">User Agent Impersonation</span>: Based on the information sent by the browser, this page decides if the user is authorized.'),
    		(46, '<span class=\"label\">Unrestricted File Upload</span>: This page allows dangerous files to be uploaded.'),
    		(47, '<span class=\"label\">Username Enumeration</span>: This page allows usernames to be enumerated.'),
    		(48, '<span class=\"label\">Application Log Injection</span>: Some inputs on this page are recorded into log records which can be read by visiting the Show Log page. Vulnerabilities on the Show Log page may allow injections in log records to execute.'),
    		(49, '<span class=\"label\">XPath Injection</span>: Some inputs on this page are vulnerable to XPath injection.'),
    		(50, '<span class=\"label\">Path Relative Stylesheet Injection</span>: Within this page is an iframe containing another page. The page being framed is vulnerable to path relative stylesheet injection.'),
    		(51, '<span class=\"label\">Client-side Security Control Bypass</span>: This page attempts to implement security using client-side security controls. Any page using such controls, including this page, is vulnerable to security control bypass.'),
    		(53, '<span class=\"label\">SQL Injection with SQLMap</span>: This page contains an sql injection vulnerability. The SQLMap tool may be able to automate testing and confirming this vulnerability.'),
    		(54, '<span class=\"label\">Insufficent Transport Layer Protection</span>: This page is vulnerable to interception with wireshark or tcpdump.'),
    	    (63, '<span class=\"label\">LDAP Injection</span>: This page is vulnerable to LDAP injection.')";

    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo "<div class=\"database-success-message\">Executed query 'INSERT INTO TABLE' with result ".$lQueryResult."</div>";
    	}// end if

    	$lQueryString = 'CREATE TABLE youTubeVideos( '.
    			'recordIndetifier INT NOT NULL, '.
    			'identificationToken VARCHAR(32), '.
    			'title VARCHAR(128),
    			PRIMARY KEY (recordIndetifier),
    			UNIQUE KEY (identificationToken))';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = TRUE;
    	}else{
    		echo format("Executed query 'CREATE TABLE' with result ".$lQueryResult,"S");
    	}// end if

    	$lQueryString = "INSERT INTO youTubeVideos(recordIndetifier, identificationToken, title)
    		VALUES
    			(13, 'la5hTlSDKWg', 'Using Burp Intruder Sniper to Fuzz Parameters'),
    			(99, '7vWTEbOfa-8', 'Introduction to Burp-Suite Intruders Character Frobber Payload'),
    	        (100, 't0uMReqs8Ng', 'Introduction to Burp-Suite Intruders Grep-Extract Feature'),
                (137, 'Cazzls2sZVk', 'How to Create Ubuntu Virtual Machine (VirtualBox)'),
    	        (138, '8VCeFRwRmRU', 'How to Install VirtualBox Guest Additions (Linux)'),
    	        (139, '33CAgRKztqU', 'How to Create Ubuntu Virtual Machine (VMware)'),
    	        (140, 'cZtkVOHRYts', 'LAMP Stack: Part 1 - How to Install Apache Web Server'),
    	        (141, 'Z6IhGGxJidM', 'LAMP Stack: Part 2 - How to Install PHP'),
    	        (142, 'PsfuaRySts4', 'LAMP Stack: Part 3 - How to Install MySQL Server'),
    	        (143, 'yHCUd_5A8vo', 'How to Reset Root Password in MySQL/MariaDB'),
    	        (144, 'TcUOaeL5SJU', 'How to Install PHP Curl Library'),
    	        (145, 'e37RtQEnUhU', 'How to Install PHP XML Library'),
    	        (146, 'ZNAmmiEQuCM', 'How to Install PHP mbstring Library'),
    	        (147, '0wIUci7s3gM', 'How to Display Errors in PHP Pages'),
    	        (148, 'TcgeRab7ayM', 'How to Install Mutillidae on Linux'),
    	        (149, 'sJd0ir9-jSc', 'How to Create Self-Signed Certificate in Apache'),
    	        (150, '79mOiU3GfnQ', 'How to Create Virtual Hosts in Apache'),
                (163, 'MB_96xLh4As', 'Burp Suite 2: How to Install on Windows'),
                (164, 'ItUvwGu4Lo4', 'Burp Suite 2: How to Install on Linux'),
                (165, 'lUDrlP9x6hA', 'Burp Suite 2: Create Shortcut on Desktop (Linux)'),
                (166, '7ePmWhypzBI', 'Burp-Suite 2: Configure Firefox with Burp Suite'),
                (167, 'tsZdiuRNWRg', 'Burp Suite 2: Adding Burps Certificate to Firefox'),
                (168, 'l18nZpYiQZM', 'Burp Suite 2: Configuring Intercept Feature'),
                (169, 'K_92lb0k9FU', 'Burp Suite 2: Setting Scope'),
                (170, 'AsZqWHk7_bw', 'Burp Suite 2: Configuring Site Map and Targets'),
                (171, '9SL9968PgUk', 'Burp Suite 2: Site Map Filters'),
                (172, 'k0U47sS2o8g', 'Burp Suite 2: Proxy History'),
                (173, '5ZpM1gQ5PuQ', 'Burp Suite 2: Repeater Tool'),
                (174, 'QLDk5zI2cdM', 'Burp Suite 2: Intruder Tool - Sniper Mode'),
                (175, 'NPVddIPYn6M', 'Burp Suite 2: Intruder Tool - Battering Ram Mode'),
                (176, 'iG7003AC8ys', 'Burp Suite 2: Intruder Tool - Pitchfork Mode'),
                (177, 'ehGsDQbMXn8', 'Burp Suite 2: Intruder Tool - Cluster Mode'),
                (178, 'lT56Z54K-Jo', 'Burp Suite 2: Comparer Tool'),
                (179, 'KlWZ5pKg-PM', 'Burp Suite 2: Decoder Tool'),
                (180, 'C03EUbgRLNE', 'Burp Suite 2: Adding Extentions'),
                (181, 'ISb60TrNp8U', 'Burp Suite 2: Configuring Upstream Proxy'),
                (182, 'abqyFbeFIq4', 'How to Install Java on Windows'),
                (183, 'fJYyfZNNk5A', 'How to Install OWASP ZAP on Windows'),
                (184, 'bPr8yO_3kOg', 'How to install Java on Linux (Debian, Ubuntu, Kali)'),
                (185, 'a6_TprVx7LE', 'How to Install OWASP ZAP on Ubuntu'),
                (186, 'MpuFW_mkJ3M', 'How to Install OWASP ZAP on Linux'),
                (187, '1lblqC2Favk', 'How to Create Shortcut for OWASP ZAP (Linux)'),
                (188, 'jHGNLvSpaLs', 'How to Install and Configure Foxy Proxy with Firefox'),
                (189, 'ICPqz1Al9fk', 'How to Proxy Web Traffic through OWASP ZAP'),
                (190, 'fa5LAfXmwoo', 'How to Intercept HTTP Requests with OWASP ZAP'),
                (191, 'pGCBivHNRn8', 'How to Spider a Web Site with OWASP ZAP'),
                (192, 'b6IR2KgiOcw', 'OWASP ZAP Breakpoints: Part 1 - Trapping HTTP Requests'),
                (193, 'H2tKdwMcKnk', 'OWASP ZAP Breakpoints: Part 2 - Trapping Specific HTTP Requests'),
                (194, 'uSfGeyJKIVA', 'How to Fuzz Web Applications with OWASP ZAP (Part 1)'),
                (195, 'tBXX_GAK7BU', 'How to Fuzz Web Applications with OWASP ZAP (Part 2)'),
                (196, 'K6qwqMt_Ldc', 'OWASP ZAP: Web App Vulnerability Assessment (Single Page)'),
                (197, 'KeSUiCr-WGo', 'OWASP ZAP: Automated Web App Vulnerability Assessment (Entire Site)'),
                (198, 'RVzs8aCnpHw', 'OWASP ZAP: Web App Vulnerability Assessment (Partial Site)'),
                (199, 'ySzxNgQ6Qpk', 'How to Start OWASP ZAP from Command Line'),
                (200, 'dId4FS_Gyn4', 'Extending OWASP ZAP with Add-Ons'),
                (201, '1CJB8BtW0pQ', 'Using OWASP ZAP with Burp-Suite: Best of Both Worlds'),

    	        (204, 'Eqfe_HYX7MU', 'How to check HTTPS certificate from command line'),
                (205, 'Hz29n-Swx1w', 'How to check HTTPS Certificates for common issues'),
                (206, 'TpNa09xn34I', 'cURL Error: SSL Certificate Problem'),
                (207, 'inD4iQGGjb8', 'What is Content Security Policy? - Part 1'),
                (208, 'IgOLBR9bHhE', 'What is Content Security Policy? - Part 2'),
                (209, 'TO7pmkMh1Tg', 'What is Content Security Policy? - Part 3'),
                (210, 'HMm5GC6bcYs', 'What is Content Security Policy? - Part 4'),
                (211, 'leiGAuCgYY0', 'What is Content Security Policy? - Part 5'),
                (212, 'ot-dSAIEkK8', 'Content Security Policy: Script Source (script-src)'),
                (213, 'xV5o3G9wilU', 'Content Security Policy: Frame Ancestors'),
                (214, 'T86igOlSJBM', 'What are Web Server Banners?'),
                (215, 'ixy7Fr0s9Fk', 'How to Set HTTP Headers Using Apache Server'),
                (216, '7O391fuNZMk', 'Check HTTP Headers with cURL'),
                (217, 'Df8l_epP38k', 'How Cache Control Headers Work'),
                (218, 'fXu8KWIrnK4', 'What is HTTP Strict Transport Security (HSTS)?'),
                (219, '81A_cAxusTM', 'What is the HSTS Preload list?'),
                (220, 'd8liHExs5tc', 'Cookies: Part 1 - How HTTPOnly Works'),
                (221, 'ZSUGfIy1ds8', 'Cookies: Part 2 - How Secure Cookies Work'),
                (222, 'brNL_bvUCck', 'Cookies: Part 3 - How SameSite Works'),
                (223, 'JrSFc_KeNzc', 'What is the X-Frame-Options Header?'),
                (224, 'zaHznZL9SuA', 'What is the X-Content-Type-Options Header?'),
                (225, 'lCmiYKgq-o8', 'What is the Referrer Policy Header?'),
                (226, 'l7WFXv5cXzA', 'What is the XSS Protection Header?'),
                (227, 'lKX1MIIxFCg', 'SSLScan: Part 1 - How to test HTTPS, TLS, & SSL ciphers'),
                (228, 'DDbwrMrwOFc', 'SSLScan: Part 2 - How to Interpret the Results'),
                (229, 'zUwPhHigi_M', 'How to Install SSLScan on Windows'),
                (234, '2-U2s0akMqE', 'Output Encoding: Part 1 - How It Stops Cross-site Scripting (XSS)'),
                (235, 'JlLVhdlqe1Q', 'Output Encoding: Part 2 - How to Test if it Works'),
                (236, '2uWAZsCm-W8', 'How Cross-site Request Forgery (CSRF) Tokens Work'),
            	(237, 'QDIppL_j6Vo', 'What is CORS? - Part 1 - Explanation'),
            	(238, 'CxFuAcThKPA', 'What is CORS? - Part 2 - Demonstration')";


	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
	if (!$lQueryResult) {
		$lErrorDetected = TRUE;
	}else{
		echo "<div class=\"database-success-message\">Executed query 'INSERT INTO TABLE' with result ".$lQueryResult."</div>";
	}// end if


	/* ***********************************************************************************
	 * Create accounts.xml password.txt file from MySQL accounts table
	 * ************************************************************************************/
	$lAccountXMLFilePath="data/accounts.xml";
	$lPasswordFilePath="passwords/accounts.txt";

	echo format("Trying to build XML version of accounts table to update accounts XML ".$lAccountXMLFilePath,"I");
	echo format("Do not worry. A default version of the file is included if this does not work.","I");

	echo format("Trying to build text version of accounts table to update password text file ".$lPasswordFilePath,"I");
	echo format("Do not worry. A default version of the file is included if this does not work.","I");

	$lAccountsXML = "";
	$lAccountsText = "";
	$lQueryString = "SELECT username, password, mysignature, is_admin FROM accounts;";
	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);

	if (isset($lQueryResult->num_rows)){
		if ($lQueryResult->num_rows > 0) {
			$lResultsFound = TRUE;
			$lRecordsFound = $lQueryResult->num_rows;
		}//end if
	}//end if

	if ($lResultsFound){

		echo format("Executed query 'SELECT * FROM accounts'. Found ".$lRecordsFound." records.","S");

		$lAccountsXML='<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
		$lAccountsXML.="<Employees>".PHP_EOL;
		$lCounter=1;
		$cTAB = CHR(9);

		while($row = $lQueryResult->fetch_object()){
			$lAccountType = $row->is_admin?"Admin":"User";
		   	$lAccountsXML.=$cTAB.'<Employee ID="'.$lCounter.'">'.PHP_EOL;
		   	$lAccountsXML.=$cTAB.$cTAB.'<UserName>'.htmlspecialchars($row->username).'</UserName>'.PHP_EOL;
		   	$lAccountsXML.=$cTAB.$cTAB.'<Password>'.htmlspecialchars($row->password).'</Password>'.PHP_EOL;
		   	$lAccountsXML.=$cTAB.$cTAB.'<Signature>'.htmlspecialchars($row->mysignature).'</Signature>'.PHP_EOL;
		   	$lAccountsXML.=$cTAB.$cTAB.'<Type>'.htmlspecialchars($lAccountType).'</Type>'.PHP_EOL;
		   	$lAccountsXML.=$cTAB.'</Employee>'.PHP_EOL;

		   	$lAccountsText.=$lCounter.",".$row->username.",".$row->password.",".$row->mysignature.",".$lAccountType.PHP_EOL;
		   	$lCounter+=1;
		}// end while

		$lAccountsXML.="</Employees>".PHP_EOL;

		try{
			/* Ubuntu 12.04LTS PHP cannot parse short syntax of
			 * is_writable(pathinfo($lAccountXMLFilePath)['dirname']).
			 * Replacing with long form version.
			 */
			if (is_writable(pathinfo($lAccountXMLFilePath, PATHINFO_DIRNAME))) {
				file_put_contents($lAccountXMLFilePath,$lAccountsXML);
				echo format("Wrote accounts to ".$lAccountXMLFilePath,"S");
			}else{
				throw new Exception("Oh snap. Trying to create an XML version of the accounts file did not work out.");
			}//end if
		}catch(Exception $e){
			echo format("Could not write accounts XML to ".$lAccountXMLFilePath." - ".$e->getMessage(),"W");
			echo format("Using default version of accounts.xml","W");
		};// end try

		try{
			/* Ubuntu 12.04LTS PHP cannot parse short syntax of
			 * is_writable(pathinfo($lAccountXMLFilePath)['dirname']).
			 * Replacing with long form version.
			 */
			if (is_writable(pathinfo($lPasswordFilePath, PATHINFO_DIRNAME))) {
				file_put_contents($lPasswordFilePath,$lAccountsText);
				echo format("Wrote accounts to ".$lPasswordFilePath,"S");
			}else{
				throw new Exception("Oh snap. Trying to create an text version of the accounts file did not work out.");
			}//end if
		}catch(Exception $e){
			echo format("Could not write accounts XML to ".$lPasswordFilePath." - ".$e->getMessage(),"W");
			echo format("Using default version of accounts.txt","W");
		};// end try

	} else {
		$lErrorDetected = TRUE;
		echo format("Warning: No records found when trying to build XML and text version of accounts table ".$lQueryResult,"W");
	}// end if ($lResultsFound)

	$MySQLHandler->closeDatabaseConnection();

} catch (Exception $e) {
	$lErrorDetected = TRUE;
	echo $CustomErrorHandler->FormatError($e, $lQueryString);
}// end try

// if no errors were detected, send the user back to the page that requested the database be reset.
//We use JS instead of HTTP Location header so that HTML5 clearing JS above will run
if(!$lErrorDetected){
	/*If the user came from the database error page but we do not have
	 * database errors anymore, send them to the home page.
	 */
	$lHTTPReferer = "";
	if (isset($_SERVER["HTTP_REFERER"])) {
		$lHTTPReferer = $_SERVER["HTTP_REFERER"];
	}//end if

	$lReferredFromDBOfflinePage = preg_match("/database-offline.php/", $lHTTPReferer);
	$lReferredFromPageWithURLParameters = preg_match("/\?/", $lHTTPReferer);

	if ($lReferredFromDBOfflinePage || $lReferredFromPageWithURLParameters){
		$lPopUpNotificationCode = "&popUpNotificationCode=SUD1";
	}else{
		$lPopUpNotificationCode = "?popUpNotificationCode=SUD1";
	}// end if any parameters in referrer

	$lRedirectLocation = str_ireplace("database-offline.php", "index.php?page=home.php", $lHTTPReferer).$lPopUpNotificationCode;
	echo "<script>if(confirm(\"No PHP or MySQL errors were detected when resetting the database.\\n\\nClick OK to proceed to ".$lRedirectLocation." or Cancel to stay on this page.\")){document.location=\"".$lRedirectLocation."\"};</script>";
}// end if

$CustomErrorHandler = null;
?>
	</body>
</html>