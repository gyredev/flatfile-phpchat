<?php

class banlist {

public function handle_banned_user($x) {
	$send='http://www.youtube.com/#'.$x;
	header('Location: '.$send);
	die('<meta http-equiv="refresh" content="0;url='.$send.'"> <iframe src="'.$send.'">');
}

public function honeypot_emails(){
   // array of possible top-level domains
  $tlds = array("com", "net", "gov", "org", "edu", "biz", "info");

  // string of possible characters
  $char = "0123456789abcdefghijklmnopqrstuvwxyz";

  // start output
  echo "<p>\n";

  // main loop - this gives 1000 addresses
  for ($j = 0; $j < 1000; $j++) {

    // choose random lengths for the username ($ulen) and the domain ($dlen)
    $ulen = mt_rand(5, 10);
    $dlen = mt_rand(7, 17);

    // reset the address
    $a = "";

    // get $ulen random entries from the list of possible characters
    // these make up the username (to the left of the @)
    for ($i = 1; $i <= $ulen; $i++) {
      $a .= substr($char, mt_rand(0, strlen($char)), 1);
    }

    // wouldn't work so well without this
    $a .= "@";

    // now get $dlen entries from the list of possible characters
    // this is the domain name (to the right of the @, excluding the tld)
    for ($i = 1; $i <= $dlen; $i++) {
      $a .= substr($char, mt_rand(0, strlen($char)), 1);
    }

    // need a dot to separate the domain from the tld
    $a .= ".";

    // finally, pick a random top-level domain and stick it on the end
    $a .= $tlds[mt_rand(0, (sizeof($tlds)-1))];

    // done - echo the address inside a link
    echo "<a href=\"mailto:". $a. "\">". $a. "</a><br>\n";

  } 

  // tidy up - finish the paragraph
  echo "</p>\n";
}
  
    public function run_request_spammer(){
		
if (!isset($_SESSION["time_in"])) {$_SESSION["time_in"]=time();}

    
   // $this->settings['request_per_fivemin'];
  //  $this->settings['post_per_fivemin']; $this->settings['post_per_fivemin'] (time()-$_SESSION['time_in'])/

//100 requests per 5 min
  if( ( $_SESSION['page_views'] > 10 && time()-$_SESSION['time_in']>300 ) &&  $_SESSION['page_views']/((time()-$_SESSION['time_in'])/60)>110  ) {
                
    $this->handle_banned_user('spammer');
    }
    
					$_SESSION['page_views']+=1;
    
    }
    
    	
	public function run_block_hostnames(){

/*
 * # block proxy servers from site access
# https://perishablepress.com/press/2008/04/20/how-to-block-proxy-servers-via-htaccess/

RewriteEngine on
RewriteCond %{HTTP:VIA}                 !^$ [OR]
RewriteCond %{HTTP:FORWARDED}           !^$ [OR]
RewriteCond %{HTTP:USERAGENT_VIA}       !^$ [OR]
RewriteCond %{HTTP:X_FORWARDED_FOR}     !^$ [OR]
RewriteCond %{HTTP:PROXY_CONNECTION}    !^$ [OR]
RewriteCond %{HTTP:XPROXY_CONNECTION}   !^$ [OR]
RewriteCond %{HTTP:HTTP_PC_REMOTE_ADDR} !^$ [OR]
RewriteCond %{HTTP:HTTP_CLIENT_IP}      !^$
RewriteRule ^(.*)$ - [F]
*/		
	//hostname/useragent bad list
	$badhost=array(
	'priv',
	'pro',
	'ghost',
	'hide',
	'ass',
	'l33t',
	'elite',
	'cloak',

	);
	
	if (in_array(gethostbyaddr($this->get_ip()), $badhost) || in_array($_SERVER['HTTP_USER_AGENT'],$badhost)) {
	$this->handle_banned_user('hostname blocked');
	return true;
	}
	return false;
	}
    
    

public function get_ip()
{

if (!empty($_SERVER["HTTP_CLIENT_IP"]))
{
//check for ip from share internet
$ip = $_SERVER["HTTP_CLIENT_IP"];
}
elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
{
// Check for the Proxy User
$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
}
else
{
$ip = $_SERVER["REMOTE_ADDR"];
}
// This will print user's real IP Address
// does't matter if user using proxy or not.
return $ip;

}


public function generate_token($tok,$t,$salt) {
	global $time;
	//session_id() 
	$_SESSION['chattime']=$time;
	$_SESSION['chatpot']=$tok;
	$token=md5( $this->get_ip(). $tok. $t . $salt );
$_SESSION['chattoken']=$token;
return $token;
}
public function validate_token($tok,$t,$salt) {

if ($_SESSION['chattime'] == $t &&
 isset($_SESSION['chattoken']) && 
md5( $this->get_ip(). $tok. $t . $salt ) == $_SESSION['chattoken'] ) {
	return true;
}
return false;

}

    
     public function ipcidrcheck ($IP, $CIDR) {
    list ($net, $mask) = split ("/", $CIDR);
   
    $ip_net = ip2long ($net);
    $ip_mask = ~((1 << (32 - $mask)) - 1);

    $ip_ip = ip2long ($IP);

    $ip_ip_net = $ip_ip & $ip_mask;

    return ($ip_ip_net == $ip_net);
  }
    
	   public function ip_in_network($ip, $net_addr, $net_mask){
	    if($net_mask <= 0){ return false; }
		$ip_binary_string = sprintf("%032b",ip2long($ip));
		$net_binary_string = sprintf("%032b",ip2long($net_addr));
		return (substr_compare($ip_binary_string,$net_binary_string,0,$net_mask) === 0);
	}
	
	
public function run_init_banlist() {
$isbanned=0;

			if (!isset($_SESSION['runblist'])){

		/*
list of bans;
*/


$banlist = 
array(
/*
*

ip, cidr mask range, hostname, reason for banning
* 
*ranges: 8 (Class A), 16 (Class B) or 24 (Class C).
* 
* 
a.b.0.0/13 	+0.7.255.255 	255.248.000.000 	524,288 	8 B 	b = 0 ... (8n) ... 248
a.b.0.0/12 	+0.15.255.255 	255.240.000.000 	1,048,576 	16 B 	b = 0 ... (16n) ... 240
a.b.0.0/11 	+0.31.255.255 	255.224.000.000 	2,097,152 	32 B 	b = 0 ... (32n) ... 224
a.b.0.0/10 	+0.63.255.255 	255.192.000.000 	4,194,304 	64 B 	b = 0, 64, 128, 192
a.b.0.0/9 	+0.127.255.255 	255.128.000.000 	8,388,608 	128 B 	b = 0, 128
a.0.0.0/8 	+0.255.255.255 	255.000.000.000 	16,777,216 	256 B = 1 A 	
a.0.0.0/7 	+1.255.255.255 	254.000.000.000 	33,554,432 	2 A
* 
*/

//ip, cidr range #,message or note user detail

array('220.248.0.0','13','china - hacks and spams'),

);
		
		for($i=0;$i<sizeof($banlist);$i++){
		if (
		
		//ipcidrcheck seems over rich
		//$this->ipcidrcheck($banlist[$i][0],$banlist[$i][1]) 
		$this->ip_in_network($this->get_ip(), $banlist[$i][0],$banlist[$i][1]) )  {
		$isbanned=1; $_SESSION['bannedreason']=$banlist[$i][2];
		$this->handle_banned_user($banlist[$i][2]);
				   
		}
		
		}

		    $_SESSION['runblist']=$isbanned;
		    
			}//end
						
//probe for spammers
 $this->run_request_spammer();
 
 //block list of bad hostnames
 $this->run_block_hostnames();
if (isset($_GET['qaz'])) { 
	 if ($_GET['qaz']=='qaz') {
		 $this->handle_banned_user('bad hostname.');
			die;
		 }
	else if ($_GET['qaz']=='harvestmoon') {
		 $this->honeypot_emails();
			die;
		 }
}	 
	 
 
}

}//endclass

?>
