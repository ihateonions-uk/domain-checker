<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Domain Checker</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" href="style.css" type="text/css" media="all" /><link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300' rel='stylesheet' type='text/css'>
</head>
<body>
<h2>Domain Checker</h2>

<?php 

# index.php
#######################################
# Domain Search © 2016 First witten by Scott Connell and then adapted and improved by Joshua George
# License: Free for personal and commercial use.
# Source: http://programs.orgfree.com
#######################################

$definitions = array(
"com" => array("whois.crsnic.net","No match for"),
"net" => array("whois.crsnic.net","No match for"),				
"org" => array("whois.pir.org","NOT FOUND"),					
"co.uk" => array("whois.nic.uk","No match"),
"io" => array("whois.crsnic.net","No match for"),
"org.uk" => array("whois.crsnic.net","No match for"),
"biz" => array("whois.crsnic.net","No match for"),
"london" => array("whois.crsnic.net","No match for"),
"co" => array("whois.crsnic.net","No match for"),
"mobi" => array("whois.dotmobiregistry.net","NOT FOUND")
);

function printForm()
{
global $keyword,$ext,$definitions;

$action = htmlspecialchars($_SERVER["PHP_SELF"], ENT_QUOTES);
$keyword = str_replace(" src", "", strtolower($keyword));

print <<<ENDHTM
<form method="post" action="$action" class="form-style">
<p>Enter your desired domain name without the extension.</p>
<p><input type="text" name="keyword" value="$keyword" placeholder="my-new-domain-name" /></p>
<p><input class="button" type="submit" value="Submit" /></p>
</form>

ENDHTM;
}

if(isset($_POST['keyword']) && strlen($_POST['keyword']) > 0)
{
$keyword = $_POST['keyword'];

// This will fix a lot of user input errors, remove if you wish.
$keyword = preg_replace('/[^0-9a-zA-Z\-]/','', $keyword);

	if(strlen($keyword) < 2)
	{
	print "<p class=\"error\">Error: The keyword \"$keyword\" is too short.</p>\n";
	printForm();
	exit(print "</body></html>\n");
	}
	if(strlen($keyword) > 63)
	{
	print "<p class=\"error\">Error: The keyword is too long. Max 63 characters. You have ". strlen($keyword) ." characters.</p>\n";
	printForm();
	exit(print "</body></html>\n");
	}
	if(!preg_match("/^[a-zA-Z0-9\-]+$/", $keyword))
	{
	print "<p class=\"error\">Error: Keyword cannot contain special characters.</p>\n";
	printForm();
	exit(print "</body></html>\n");
	}
	if(preg_match("/^-|-$/", $keyword))
	{
	print "<p class=\"error\">Error: Keywords cannot begin, or end with a hyphen.</p>\n";
	printForm();
	exit(print "</body></html>\n");
	}

	printForm();
	print "<table cellspacing=\"0\" class=\"data\">\n";

	foreach($definitions as $key => $value)
	{
	$ext = $key;
	$server = $definitions[$ext][0];
	$nomatch = $definitions[$ext][1];

		if(!$server_conn = @fsockopen($server, 43))
		{
		print "<tr><td class=\"short error\">Error</td><td class=\"error\">Could not connect to whois server at: ". $server ."</td></tr>\n";
		}
		else
		{
		$response = "";

		fputs($server_conn, "$keyword.$ext\r\n");

			while(!feof($server_conn))
			{
			$response .= fgets($server_conn, 128);
			}

		fclose($server_conn);

			if(preg_match("/$nomatch/", $response))
			{
			print "<tr><td class=\"short green\">Available</td><td><a href=\"https://www.123-reg.co.uk/order/domain?X-CSRF-Token=a7b7e2cae578b8e7b464bd4240addb723444ebf8&domain=$keyword.$ext\">http://$keyword.$ext</a></tr>\n";
			}
			else
			{
			print "<tr><td class=\"short red\">Registered</td><td><a href=\"http://$keyword.$ext\">http://$keyword.$ext</a></td></tr>\n";
			}
		}
	}

print "</table>";
}
else {
printForm();
}

?>

</body>
</html>
