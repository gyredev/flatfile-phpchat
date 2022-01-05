<?php
header ("Content-Type: text/html; charset=utf-8") ; 
session_start();
session_regenerate_id();
date_default_timezone_set('UTC');
//Set no caching
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");



/***   www.phpclub.site.ir   ***/
/*******************************//*
ini_set("error_reporting", E_ALL);
ini_set( "display_errors", 1 );
*/
/* edited by gyrereality.tk, alex kalas gyrereality@hushmail.com
added bbcode, rooms, pass rooms, changeable usernames, chatlogs, antispam
done with "posts per minute" and captcha startup to reduce spam/bot abuse and many other features.


filem time .dat room file if user request spams passed get gp variable less than a day of filem time  or <=0 more than 5x LOG as spammer
*/


///////////////////////////////////////////////////////////////////////////////
			///////////INITIALIZATION
			////////////////////////////////////////////////////////////////////


$version='v115';
$time=time();
$admincontact="<div style='color:#aaceaa;'>Contact Admin: leave me messages in the dev chatroom.<p>Whoami? my past monikers:covert1,ajizizoc,biskelion @lordaeron uswest bnet wc3.</p> </div>";
$refresh = 7 ; // Page refresh time in seconds
$remain = 400 ; // Messages remaining time in seconds
$spamlog="spammers.dat";
$username_length = 60;
$avg_post=0;
$ipsalt="ffakopa39a43j5na1";
$salt="s19ajf2ba77uzi4210ae3221";//salt security
$defaultroom="Lobby";
$max_memoryusage=1293912;

//ppt=posts per time period 10 minutes, tbp=time between posts made, timeout=time until checks for posts inactivity timeout
$rooms=array(
"Lobby"=>array("icon"=>"http://theurbanavenue.com/file/2014/04/051-Main-Lobby.jpg","file"=>"lobby.dat","description"=>"its the main room","password"=>"","messagehistory"=>40,"maxpostcharacters"=>300,"maxuser"=>12,"ppt"=>30,"tbp"=>13,"timeout"=>300000,"description"=>"welcome to the main lobby. hang out and relax."),
"Top Secret"=>array("icon"=>"","file"=>"topsecret.dat","description"=>"well 
its a secret room","password"=>"qaz","messagehistory"=>150,"maxpostcharacters"=>700,"maxuser"=>22,"ppt"=>60,"tbp"=>3,"timeout"=>410000,"description"=>"secrets occultism and covert subjects"),
"Todolist"=>array("icon"=>"","file"=>"todolist.dat","description"=>"well 
its a secret room","password"=>"123","messagehistory"=>250,"maxpostcharacters"=>700,"maxuser"=>22,"ppt"=>60,"tbp"=>3,"timeout"=>410000,"description"=>"a list of things i need to do lol"),
"dev"=>array("icon"=>"http://apps.goodereader.com/wp-content/uploads/2012/06/Android-Developer2.png","file"=>"dev.dat","description"=>"well 
its a secret room","password"=>"","messagehistory"=>250,"maxpostcharacters"=>700,"maxuser"=>22,"ppt"=>40,"tbp"=>7,"timeout"=>410000,"description"=>"give me some suggestions for the site or personal messages"),


);
function describe_rooms() {
	global $rooms;
$text="";

$rt=sizeof($rooms);
$x=0;
$key=array_keys($rooms);
while ($rt>$x) {
	
	
	$text=$text."<span style='background-image: url(".$rooms[$key[$x]]['icon']."); background-repeat: repeat;background-position:right top;'><input type='button' 
onclick='$(\"#room\").val(\"".$key[$x]."\");' value='".$key[$x]."'>  <span style='background-color:#000000; 				filter:alpha(opacity=77);-moz-opacity:.77;-khtml-opacity: .77; opacity: .77;'>".$rooms[$key[$x]]['description']."</span></span>";
	
	++$x;
}

return "<style type='text/css'>.describe_rooms input { 
font-size:200%; }
.describe_rooms span {
border:1px solid #888888;
padding:15px;
margin:5px;
display:block;
clear:both;
background-color:#000000;
color:#00ffff;

}
</style><div title=\"type the room exactly as seen into the 
room input\" style=\"\">List of rooms: ( ".$rt." )  <div 
class='describe_rooms'>
".$text."</div></div>";	
	
}

///////////////////////////////////////////////////////////////////////////////
			///////////END INITIALIZATION
			////////////////////////////////////////////////////////////////////


class banlist {
    public function handle_banned_user($a) {
    
header('Location: http://www.google.bg/#q='.$a);
die('<META HTTP-EQUIV="refresh" CONTENT="0;URL=gfshfgh"><script><iframe>');

    }
    
    public function run_request_spammer(){
    
   // $this->settings['request_per_fivemin'];
  //  $this->settings['post_per_fivemin']; $this->settings['post_per_fivemin'] (time()-$_SESSION['time_in'])/

//100 requests per 5 min
  if( ( $_SESSION['page_views'] > 10 && time()-$_SESSION['time_in']>600 ) &&  $_SESSION['page_views']/((time()-$_SESSION['time_in'])/300)>100  ) {
    $this->handle_banned_user('spammer');
    }
    
    }
    
    	
	public function blocked_hostname(){
	//hostname/useragent
	$badhost=array(
	'priv',
	'pro',
	'ghost',
	'hide',
	'ass',
	);
	
	if (in_array(gethostbyaddr($this->get_ip()), $badhost) || in_array($_SERVER['HTTP_USER_AGENT'],$badhost)) {
	
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
	
	
public function run_banlist() {
$isbanned=0;

			if (!isset($_SESSION['runblist'])){

		//require_once($this->bans_location);
		
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
*/

//array('127.0.0.1','16','localhosts','reason for ban'),

array('220.248.0.0','13','','china - hacks and spams'),









);
		
		for($i=0;$i<sizeof($banlist);$i++){
		if ( 
		//$this->ipcidrcheck($banlist[$i][0],$banlist[$i][1]) 
		
		$this->ip_in_network($this->get_ip(), $banlist[$i][0],$banlist[$i][1]) ||
		$this->blocked_hostname() ) {
		$isbanned=1; $_SESSION['bannedreason']=$banlist[$i][3];
		$this->handle_banned_user($banlist[$i][3]);
				   
		}
		
		}

		    $_SESSION['runblist']=$isbanned;
		    
			}//end

}

}//endclass
$bans=new banlist;
$bans->run_banlist();

				if (isset($_POST["password"]) && isset($_POST["room"])){
					
					$_SESSION["password"]=md5($_POST["password"].$salt);
					
					$_SESSION["room"]=($_POST["room"]);
					
					
							if (isset($rooms[$_POST["room"]]["password"]) && md5($rooms[$_POST["room"]]["password"] . $salt)!=$_SESSION["password"]) {
									if (!isset($_SESSION["timesbadpassword"])){
										$_SESSION["timesbadpassword"]=1;
										}else{ $_SESSION["timesbadpassword"]+=1; 
										}
							}
							
					
					 }

function quickconvert($text, $key = '') {
	// Author: halojoy, July 2006
// Modified and commented by: laserlight
    if ($key == '') {
        return $text;
    }
    $key = str_replace(' ', '', $key);
    if (strlen($key) < 8) {
       return 'kerror';
    }
    $key_len = strlen($key);
    if ($key_len > 32) {
        $key_len = 32;
    }
    $key = substr($key, 0, $key_len);
    $text_len = strlen($text);
    $lomask = str_repeat("\x1f", $text_len); // Probably better than str_pad
    $himask = str_repeat("\xe0", $text_len);
    $k = str_pad("", $text_len, $key); // this one _does_ need to be str_pad
    $text = (($text ^ $k) & $lomask) | ($text & $himask);
    return $text;
}

function generate_captcha() {
	global $salt;
	
	
	$arg1 = rand (0,12);
	$arg2 = rand (0,12);
	
$questions=array("{$arg1} * {$arg2} = ?","{$arg1} + {$arg2} = ?","{$arg1} + {$arg2} + {$arg2} = ?");
$answers=array(($arg1*$arg2),($arg1+$arg2),($arg1+$arg2+$arg2));
//pow($arg1,$arg1)

$randquestion=rand(0,sizeof($questions)-1);
	$_SESSION['chatcaptcha']=md5($answers[$randquestion].$salt);
	
return "<div id='captchaq' style='font-size:300%; color:#9988cc; font-weight:bold;width:500px; border-right:4px dotted #ff0;text-align:center;'>".str_pad($questions[$randquestion], rand(0,9), " ", STR_PAD_BOTH)."</div>";




}

function validate_captcha($answer) {
		global $salt;

	if ($answer!=null && md5($answer.$salt)==$_SESSION['chatcaptcha']){
		return true;
	}

	return false;
}

function atpeakmemory() {

return true;

}

/////////////how many times did they refresh/captcha wrong
/*if (  !validate_captcha($_GET['captcha']) ) {
$bans->handle_banned_user('you failed the captcha tests too many times. sorry.');

}
*/









 function parse_generaltext($subject){
if ($subject==null){return null;}


	//just text replacements
	
  //strip watch var in embed for proper playback, stripping from whole string isnt user friendly utube links.

$search  = array(
'autoplay=1',
'"', 
'\'',
'||',//file seperator required

'&autoplay=1',

);
$replace = array(
'',
 '&#34;',
 '&#39;',
'',

'',

);



$bbcode=array (

//rem script ,xss
array("/<script\b[^>]*>(.*?)<\/script>/is","script"),
array("/on(.*?)\{4,10}(=)/is","$1-"),
//commenting

array("/\[url\](.*?)\[\/url\]/i","<a href=\"$1\" target=\"_blank\">$1</a>"),

array("/\[url\=(.*?)\](.*?)\[\/url\]/i","<a href=\"$1\" target=\"_blank\">$2</a>"),


array("/\[youtube\](.*?)\[\/youtube\]/i","<iframe width=\"428\" height=\"228\" src=\"https://www.youtube.com/embed/$1\" frameborder=\"0\" allowfullscreen></iframe>"),



array("/\[img\](.*?)\[\/img]/i","<img src='$1'>"),
//text tags

array("/\[i\](.*?)\[\/i\]/i","<i>$1</i>"),

array("/\[b\](.*?)\[\/b\]/i","<b>$1</b>"),

);

$subject=str_ireplace($search, $replace, $subject);
$subject=strip_tags($subject, '');


//begin proc recursive




	for($z=0;$z<sizeof($bbcode);$z++){
	preg_match_all($bbcode[$z][0], $subject, $matches, PREG_SET_ORDER);

			foreach ($matches as $val) {

				$subject = preg_replace($bbcode[$z][0], $bbcode[$z][1], $subject);

			}

	}
	

return nl2br($subject);
}



function getuniqueusers($array,$t) {
//used to crudely setup slots in chat for max users to post. user is ip encoded


	$onlinetime=$t!=null?$t:600000;
	
	$users=array();
	
	
	$users=array_unique($users);
}

function cleanusername( $x ) {
	global $username_length;
	
	$x=str_ireplace('[youtube]', '', $x);
	$x=str_ireplace('[/youtube]', '', $x);
		$x=str_ireplace('[url]', '', $x);
	$x=str_ireplace('[/url]', '', $x);
				$x=str_ireplace(',', '', $x);

	return urldecode(parse_generaltext(substr(htmlentities($x),0,$username_length)));
}




$_SESSION["room"]=!isset($_SESSION["room"])?"Lobby":$_SESSION["room"];
$inroom=isset($_SESSION["room"])&& isset($rooms[$_SESSION["room"]]["file"])?$_SESSION["room"]:$defaultroom;
$file=$rooms[$inroom]["file"];

if(!file_exists($file)){
touch($file);

}


if (isset($_POST["room"])){$_SESSION["room"]=$_POST["room"];}

if (isset($_POST["username"])) { $_SESSION["username"]=$_POST["username"];}
$user=isset($_POST["username"])?cleanusername($_POST["username"]):"";
$user=isset($_SESSION["username"])?cleanusername($_SESSION["username"]):"";

$user = strlen($user)<3?"Anonymous":$user;


$users = array($user) ;

$_SESSION["last_post"]=!isset($_SESSION["last_post"])?0:$_SESSION["last_post"];


$correctpassword=1;


$password=isset($_SESSION["password"])?$_SESSION["password"]:null;
		if (
						$rooms[$inroom]["password"]!=null && ( md5($rooms[$inroom]["password"] . $salt)!=$password
							) || (isset($_SESSION["timesbadpassword"]) 
							&& $_SESSION["timesbadpassword"]>6)  ) {
								$correctpassword=0; 
							if (isset($_GET["gp"])) {
	
	die( "params('addexception', '".urlencode("incorrect ".$inroom." room password.")."');");

						}
							
																	}
							
							
									if ( $avg_post > $rooms[$inroom]['ppt'] ) {
								
							if (isset($_GET["gp"])) {
										if 
										(!isset($_SESSION["timespostpertenminute"])){ 
								$_SESSION["timespostpertenminute"]=1; 
								}else{ $_SESSION["timespostpertenminute"]+=1; 
								}
								
						die( "params('addexception','[spam] You exceed the ".$rooms[$inroom]['ppt']." posts per 10 minutes in ".$inroom.". 	[".$avg_post."][".$_SESSION["timespostpertenminute"]."] Do not exceed 25.');");
							
						}
							
																				}
							
					///////////////////////////////////////////////////////////////////////////////
			///////////POST		
			///////////////////////////////////////////////////////////////////////
			
			
			///control here by # of unique users posting in chat file by 10 minutes, if #users exceeds max throw exception max user slot exceeded and prevent more messages same for get post
			
						if (isset($_POST["captcha"]) && $correctpassword==1 && (time()-$_SESSION["last_post"])>$rooms[$inroom]['tbp']  && isset($_POST["msg"]) && strlen($_POST["msg"])>=1 && strlen(urldecode($_POST["msg"]))<=$rooms[$inroom]["maxpostcharacters"] ){
				if (!validate_captcha($_POST["captcha"])) {
						die( "params('addexception', '".urlencode("#incorrect captcha")."');");
				return;
				}
				if ( !$bans->validate_token($_SESSION['chatpot'],$_SESSION['chattime'],$salt) || ($time-$_SESSION['chattime'])<=2.2 ) {
						die( "params('addexception', '".urlencode("<pre>(づ｡◕‿‿◕｡)づ </pre>")."');");
				return;
				
	
					
}
				
				
				
$f = file_get_contents ($file) ;

$ff=null;
if ($f!=null) {
	$e=explode("||", $f) ;
	



	$ff=implode(array_slice(explode("\r\n",$f),0,$rooms[$inroom]["messagehistory"]),"\r\n");
}
			
				$convert=parse_generaltext($_POST["msg"]);
	
		
			$fp = fopen($file, "w") ;
			if (!$fp) { die ("Can not write to file .") ; }



				fputs ($fp, $user."||".time()."||".$convert."\r\n".$ff);
				
if (!isset($_SESSION["time_in"])) {$_SESSION["time_in"]=time();}

if (!isset($_SESSION["total_post"])){
								$_SESSION["total_post"]=1; 
								}else{$_SESSION["total_post"]+=1;}
$avg_post=( 1+( time()-$_SESSION["time_in"] ) /600 ) * $_SESSION["total_post"];

//backup saved for each month
							if(!file_exists("backup-".date("M-Y").".htm")){
							touch("backup-".date("M-Y").".htm");

							}else{
				
							$bu = fopen("backup-".date("M-Y").".htm", "a") ;
							 fputs($bu, "<div>".quickconvert($bans->get_ip(),$ipsalt)."     ".$user."     ".date("y-m-d H:i:s")."     {$convert}</div>") ;
							fclose($bu) ;
							}


			fclose($fp) ;
			
			die(  );
						}
			//log backup-month-year.htm

						
	//if ( ($e[0]!=$user && $e[2]=="\r\n") || ( $i<$max && ($e[1]+$remain)>time() && $e[2]!="\r\n") ) fputs($fp, $f[$i]) ;


///////////////////////////////////////////////////////////////////////////////
			///////////GETPOST
			////////////////////////////////////////////////////////////////////
			
if (isset($_GET["gp"]) && isset($_GET["latestpost"]) && validate_captcha($_GET["captcha"]) )
{
					if ( !$bans->validate_token($_SESSION['chatpot'],$_SESSION['chattime'],$salt) || ($time-$_SESSION['chattime'])<=2.2 ) {
			die( "params('addexception', '".urlencode("<pre>(づ｡◕‿‿◕｡)づ </pre>")."');");
				return;
					
}

	
	
	if ($_GET["latestpost"]>=0 ) {
			//if ($_GET["latestpost"]>=filemtime($file)-86400) { return; }
		$f2 = file($file) ;
	
		$color=0;
		$i=sizeof($f2)-1;

		if (!isset($f2) || $i<-1) {  return false; }
		while ( !$i<=-1 && !empty($f2[$i])) {
			$e=explode("||", $f2[$i]) ;
			if ( (isset($f2[$i]) && isset($e[1])) && $e[1]>$_GET["latestpost"]) {
		  
		  echo "params('addpost','".urlencode($e[1])."||".$e[1]."||".urlencode($e[0])."||".urlencode($e[2])."');";
	  }
		  
		--$i;
		}
	
	}else{ $_SESSION['badpost']+=1;  }
	
			die(  ) ;
}
else
{
if ( empty($_GET) ) {
				die( "params('addexception', '".urlencode("invalid request")."');");
}

//can send them to google or kill chat if they get captcha wrong!!
				if (isset($_GET["gp"]) && isset($_POST["msg"]) && strlen($_POST["msg"])>$rooms[$inroom]["maxpostcharacters"]) {
					
						die( "params('addexception', '".urlencode("post too long")."');");
				}
				
				else if ( isset($_GET["gp"]) && !validate_captcha($_GET["captcha"]) ){
						die( "params('addexception', '".urlencode("incorrect captcha")."');");

		
				}
				
				
				else if ( isset($_GET["gp"]) || isset($_POST['msg']) ){
						die(  );
		
				}

///////////////////////////////////////////////////////////////////////////////
			///////////CHAT RETURN USER PAGE
			////////////////////////////////////////////////////////////////////
			
			

$tok=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 2, rand(2,6));
$bans->generate_token($tok,$time,$salt);

	die("<html><head><title>Gyrereality Chatroom [{$inroom}]</title><meta http-equiv='Content-Type' content='text/html; charset=utf-8'><script type='text/javascript' src='jquery-1.10.1.js'></script><link rel='shortcut icon' href='../res/gyre.png' type='image/x-icon' /></head><body align='center'>
		<style type='text/css'>
	
	body{
	font-size:12pt;
	font-family:monospace,Times New Roman,Times,serif;
		background-color:#000000;
		color:#cdcdcd;
				text-align:left;
		vertical-align:top;
	padding:5px; 
	margin:0px;
	}
	::selection {
	background: #00ba22;color:#ffffff; /* Safari */
	}
::-moz-selection {
	background: #00ba22;color:#ffffff; /* Firefox */
}
a:link{
color:#00ffff;	
text-decoration:none;
}
a:hover,a:visited{
color:#ff00ff;	
}

	.p1{
		text-align:left;
		vertical-align:top;
		background-color:#131313;
		padding:8px;
		word-wrap:break-word;
	}
	.p2{
		background-color:#050505;
		text-align:left;
		vertical-align:top;
		padding:8px;
		word-wrap:break-word;
	}
	.p2 img,.p1 img{
		display:block;
		max-width:350px;
		vertical-align:middle;
		

	}
				.p2 img:hover,.p1 img:hover{
				display:block;
				max-width:100%;
				vertical-align:middle;


				filter:alpha(opacity=100);
				-moz-opacity:1;
				-khtml-opacity: 1;
				opacity: 1;
					}
	
	
		.p2 param,.p1 param{
		display:block;
		vertical-align:middle;
		filter:alpha(opacity=80);
		-moz-opacity:0.8;
		-khtml-opacity: 0.8;
		opacity: 0.8;
	}
	
	
	#userstyle{
		color:#2572ff;font-weight:bold;font-size:105%;	vertical-align:center;
	text-align:inherit;

	}
	#userstyle #a{
		font-size:110%;font-style:bold;
	}
	#userstyle img {
	display:inline;
	vertical-align:center;
	float:justify;
	max-height:110px;
	max-width:300px;
	}

	#chat {
		-moz-border-radius: 1em 1em 1em 1em;
		border-radius: 1em 1em 1em 1em;
		z-index:1;
		border:1px solid #373737;
		
		padding:5px;
		margin:0px;
	}
	.trans{
		filter:alpha(opacity=70);
		-moz-opacity:0.70;
		-khtml-opacity: 0.70;
		opacity: 0.70;
	}
		.trans2{
		filter:alpha(opacity=92);
		-moz-opacity:0.92;
		-khtml-opacity: 0.92;
		opacity: 0.92;
	}
	#helpwindow {
	text-shadow: 0.1em 0.1em 0.2em black;
		padding:4px;
		margin:0px;
		position:absolute;
		top:0px;
		left:0px;
		background-color:#000000;
		color:#dddccc;
		z-index:10;
display:none;
				-moz-border-radius: 1em 1em 1em 1em;
		border-radius: 1em 1em 1em 1em;
	}
	
	#helpwindow a:link{
color:#00ffff;	
text-decoration:none;
}
#helpwindow a:hover,a:visited{
color:#ff00ff;	

}
	
	
	#warning,.warning{
		background-color:#121212;padding:10px;color:#10ca20;font-size:120%; max-height:300px;overflow:auto; word-wrap:break-word;
	}
	#exception,.exception {
			background-color:#121212;padding:10px;color:#a53415;font-size:120%; max-height:300px;overflow:auto; word-wrap:break-word;
	
	}
	
	
	.captcha{
	text-shadow: 0.1em 0.1em 0.1em #323232;
			background-color:#010101;
		color:#dadada;
	
		margin:0px;
		padding:0px;
		float:right;
		position:absolute;
		left:0px;
		top:0px;
		min-height:1000px;
		z-index:9;
		width:100%;


}
	
	.hidevideo{
	position:absolute;
	left:-1000px;
}
	#usersonline{
	font-size:150%;
	text-align:center;
	
}
	#captchadec{
	display:inline;
	vertical-align:center;
text-align:center;
position:absolute;
top:10px;
left:11%;
z-index:-100;
}
		#captchadec img{
	 max-width:74%; 
	 max-height:580px;
}
#otherinfo {
	margin:0px;
	padding:5px;
	background-color:#232323;
	font-size:150%;
	color:#ffaa33;
	display:none;
}
#otherinfo img{
	max-height:200px;
	vertical-align:center;
}

	#otherinfo .user{
	font-weight:bold;
	font-size:200%;
	color:#664300;
}
#otherinfo .userdec {
color:#209f15;
margin-right:9px;
text-shadow: 1px 1px #777;
}


.nav{ z-index: 9999; position: fixed; top:0px;width:100%; background-color:#111; } 

	</style> 
	<div style='vertical-align:center;text-align:center;padding:1px;margin:0px 0px 0px 0px;width:100%;' id='chat_nav'><a name='top'></a><input placeholder='Message' maxlength='".$rooms[$inroom]["maxpostcharacters"]."' title='message ( type /? for help)' name='msg' style='width:70%'
					value='' id='message' onkeyup='entersubmitpost();savetext();' ondblclick=''><input placeholder='Username' maxlength='".$username_length."' id='username' title='username' name='username' 
					value='' onkeyup='params(\"saveuser\");' style='width:15%'> <input type='submit' name='send' onmousedown='submitpost();' value='Send'></div>
		
		
			<div id='chat'><div style=''>Loading Chatroom [<span style='font-weight:bold;color:#b94400;'>".$inroom."</span>]... Type <a href='#a' onclick='window.windowup=false;params(\"/users\");'>/u </a>for users online, <a href='#b' onclick='params(\"helpwindow\");'>/? or /help</a> for help with using the chat. <a href='../index.php'>Go back to gyrereality boards</a></div></div><br>
				<div class='captcha' id='captchascreen'><div id='captchadec'></div><div class='trans' style='background-color:#454545;padding:25px 0px 0px 11%;'>Captcha is required to chat (it helps to fight spam bots). ".generate_captcha()."<input type='text' placeholder='Captcha' id='captcha' name='captcha'><input id='captchaanswer' onmousedown='params(\"/captcha\");' type='button' value='answer'>
				<br /> <p></p><a href='#b' 
				onclick='params(\"helpwindow\");'>click to join 
				different chatroom</a>
				<input type='text' value=' ' name='pot' id='pot' style='display:none;'><input type='hidden' value='".$_SESSION['time']."' name='time'></div></div>
		
		<div style='display:none;' id='helpwindow' class='trans2'><div 
		style='cursor:pointer;margin:2px; padding:0px; color:#ff3300; 
		text-align:right;clear:both;' onclick='params(\"helpwindow\");'>[ X 
		]</div><div id='helpinfo'><div title='click to show usersonline 
		stats.' class='usersonline trans' 
		onclick='window.windowup=false;params(\"/users\");' 
		id='usersonline'>You're in room: <a 
		title='".$rooms[$inroom]["description"]."'>{$inroom}</a> 
		</div>{$admincontact}<br /><div 
		style='float:left;display:block;width:100%;padding:0px;text-align:center;margin:0px;overflow:auto; background-color:#341414;font-size:150%;' class='describe_rooms'>
		<form action='index.php' method='post'><input id='room' placeholder='Join Room' 
		title='join room' name='room' 
		size=10 ><br /><input placeholder='Password' name='password' 
		type='password' id='chatpassword' title='password' size=10><br 
		/><input type='submit' value='Travel to this room!'></form><p></p>
		".describe_rooms()." </div><div><a href='#' onclick='var text=getCookie(\"savetext\"); $(\"#message\").val(text);'>click to restore last saved text</a></div><b>Rules:</b> Do not spam. Be nice to others! <p><b>bbcode allowed: (click code to add to post)</b> 

		 <a href='#' onclick='$(\"#message\").val($(\"#message\").val()+\"[b][/b]\");'>[b]#bold text[/b]</a> <a href='#' onclick='$(\"#message\").val($(\"#message\").val()+\"[i][/i]\");'>[i]#italic text[/i]</a> <a href='#' onclick='$(\"#message\").val($(\"#message\").val()+\"[url][/url]\");'>[url]#url of page to link[/url]</a> <a href='#' onclick='$(\"#message\").val($(\"#message\").val()+\"[img][/img]\");'>[img]#url of image[/img]</a>  <a href='#' onclick='var s=prompt(\"Youtube video id to embed\",\"\");var s=s.replace(\"https://www.youtube.com/watch?v=\",\"\");  $(\"#message\").val($(\"#message\").val()+\"[youtube]\"+s+\"[/youtube]\");'>[youtube]#video code id ex: 6gSPhR2oBkc[/youtube]</a><br 
		/></p>
		<p><b>Commands:</b>  /? or /help (display help info and join rooms),/users or /u (toggle user data like last posting and active users), /stat (shows general stats), /music (url of your youtube song, you can use /music http://www.youtuberepeater.com/watch?v=   >> =(youtube id) to loop your song. To shutoff type '/music' to keep set off and /music random to play random prelist. /music (youtube url) saves the url so when you come back to chat you can listen again :) ) 
		</p><p><b>Want Smilies?? :) </b>Here's a list of smilies websites below, find the smiley you want from any site and post the [img] tag in the chat message!<br /><a href='http://www.emoticons.online.fr/index.php?cat=Default' target='_blank'>http://www.emoticons.online.fr/index.php?cat=Default</a>
		<br />
		<a href='http://www.runemasterstudios.com/graemlins/' target='_blank'>http://www.runemasterstudios.com/graemlins/</a>
		<br />
		<a href='http://www.sympato.ch/smiley2.php' target='_blank'>http://www.sympato.ch/smiley2.php</a></p>
		<p><b>What's the timezone used by the chat? </b>its 'UTC'. </p>

		<div style='color:#3350a0;text-align:center; letter-spacing:3pt; clear:both;'>{$version}</div>
		</div>
		<div style='display:none;' id='otherinfo'></div>

					</div>
<div id='musicplayer' style='text-align:center;color:#992800;font-size:55%;'></div>

		<script type='text/javascript'>
<!--
 $( '#pot' ).val('".$tok."');
  $( '#chatpassword' ).val('');
 $( '#captcha' ).val('');	
 
 //begin captcha shuf	 
			var hue = 'rgb(' + (Math.floor(Math.random() * 256)) + ',' + (Math.floor(Math.random() * 256)) + ',' + (Math.floor(Math.random() * 256)) + ')';
			
$('#captchaq').css('color',hue);

var images=new Array()

images[0]='http://www.thegorgeousdaily.com/wp-content/uploads/2012/11/alex-grey-1.jpg';
images[1]='https://images.duckduckgo.com/iu/?u=http%3A%2F%2Fandrewouellette.com%2Fwp-content%2Fuploads%2F2011%2F01%2FOm_in_Lotus_by_Ledrahan.jpg&f=1';
images[2]='https://images.duckduckgo.com/iu/?u=http%3A%2F%2Fauntiedogmasgardenspot.files.wordpress.com%2F2013%2F06%2Fsyrianrue.jpg&f=1';
images[3]='https://images.duckduckgo.com/iu/?u=http%3A%2F%2Fwww.computerfreewallpapers.com%2Fbulkupload%2Fwall6%2FAbstract%2FSpace%2520Art%2FSpace-Art-Wallpapers-29.jpg&f=1';
images[4]='https://images.duckduckgo.com/iu/?u=http%3A%2F%2F2.bp.blogspot.com%2F-OKC7hz6suiY%2FTygtZyX4jRI%2FAAAAAAAAIgg%2FP7zbNLWvrao%2Fs1600%2FPassion_flower_beautiful%252B%2525285%252529.jpg&f=1';


    function randpic() {
var alength=images.length;
var picture =rand(alength);


setTimeout(function() {
$('#captchadec').fadeOut('slow');
}, 1500);
setTimeout(function() {
$('#captchadec').html('<img src=\"'+images[picture]+'\" alt=\"'+picture+'\">').fadeIn('slow');

}, 3300);

    }
$('#captchadec').html('<img src=\"http://www.moodflow.com/wordpress_moodflow/wp-content/uploads/2010/12/before-the-storm.jpg\">');
    
    
    
var captchadec=setInterval('randpic();', 16000);

var maxuser='".$rooms[$inroom]['maxuser']."';
var maxrow='".$rooms[$inroom]['messagehistory']."';
var initrun=0;
var latestpost=0;
var totalpost=0;
var postsmade=0;
var timeouts=0;
var captchacode='';

var tnamespace = {};

var chattimer=null;
var usertimer=null;

var lastwarning=null;

var refresh='".($refresh*1000)."';
var timebetweenpost='".($rooms[$inroom]['tbp']*1000)."';
var timeout='".($rooms[$inroom]['timeout'])."';

var addpost=1;

var scrollfromtop=0;

var windowup=false;

var userpost=new Array();
var userpost_time=new Array();



function screenwindowup() {
		if (window.windowup) {
		$( 'object' ).removeClass( 'hidevideo' );
		$( 'iframe' ).removeClass( 'hidevideo' );
		window.windowup=false;
		}else{
			$( 'object' ).addClass( 'hidevideo' );
			$( 'iframe' ).addClass( 'hidevideo' );
				window.windowup=true;
		}
}



function setCookie(c_name,value,exdays)
{
var exdate=new Date();
exdate.setDate(exdate.getDate() + exdays);
var c_value=escape(value) + ((exdays==null) ? \"\" : \"; expires=\"+exdate.toUTCString());
document.cookie=c_name + \"=\" + c_value;
}
function getCookie(c_name)
{
var c_value = document.cookie;
var c_start = c_value.indexOf(\" \" + c_name + \"=\");
if (c_start == -1)
  {
  c_start = c_value.indexOf(c_name + \"=\");
  }
if (c_start == -1)
  {
  c_value = null;
  }
else
  {
  c_start = c_value.indexOf(\"=\", c_start) + 1;
  var c_end = c_value.indexOf(\";\", c_start);
  if (c_end == -1)
  {
c_end = c_value.length;
}
c_value = unescape(c_value.substring(c_start,c_end));
}
return c_value;
}

function stopchat() {
window.clearInterval(chattimer);
window.addpost=0;
window.initrun=2;
}

function savetext() {

var text=$('#message').val();
if (text!=null) 
setCookie('savetext',text,365);

}

function chattimeout() {

if (window.postsmade<=0) {
		params('addexception', 'your chat session timed out due to inactivity.');

}else if (window.timeouts>=1 ) {
			if ((window.postsmade+1)<=window.timeouts) {
			params('addexception', 'your chat session timed out.');
}

							}

	window.timeouts+=1;
	
}


function initchat( ){

if (window.initrun==1){ return; }
$('#message').focus();

		clearInterval(captchadec);
	 window.chattimer=setInterval(\"runchat();\", refresh);
	 setInterval(\"chattimeout();\", timeout);
	 
	 runchat();
	window.initrun=1;
	
}



function entersubmitpost() {
			$(\"#message\").keyup(function(event) {
				if(event.which==13) {
					submitpost();
				}
			});	
	
}

function rand ( n )
{
  return ( Math.floor ( Math.random ( ) * n )+0 );
}

function urldecode(url) {
if (url!=null){
 return decodeURIComponent(url.replace(/\+/g, ' '));
}

}
function urlencode(str) {
  //       discuss at: http://phpjs.org/functions/urlencode/
  //      original by: Philip Peterson

  str = (str + '')
    .toString();

  return encodeURIComponent(str)
    .replace(/!/g, '%21')
    .replace(/'/g, '%27')
    .replace(/\(/g, '%28')
    .
  replace(/\)/g, '%29')
    .replace(/\*/g, '%2A')
    .replace(/%20/g, '+');
}




function addpostwait(x) {
window.addpost=0;
		setInterval(function(){
    window.addpost=1;
},window.timebetweenpost);
		
		

}

function pagetimeout(x) {
//slow request
if (window.initrun!=1){ return; }

if (x==1) {
	clearInterval(window.chattimer);
window.chattimer=setInterval(\"runchat();\", refresh);

return;
}

clearInterval(window.chattimer);
//params('addwarning', '0');
}





function params(x,y,z){

var a=x.split(' ');
var b=x.substr(0,1);
if (b=='/') {
	switch(a[0]){
				case '/stat':
		params('addwarning','posts shown:'+window.totalpost+'<br>posts you have made:'+window.postsmade);

break;

case '/captcha':

if (window.initrun!=0){ return false; }
window.captchacode=$('#captcha').val();


if (  window.captchacode=='') {alert('Answer the question.'); return false;}
initchat( );
				if ($('#captchascreen').css('display')=='none') {
$('#captchascreen').show('slow');
}else{
	$('#captchascreen').hide('slow');
}



break;
case '/u':
case '/users':
				window.windowup=false;
var a = new Date(window.latestpost*1000);
  var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
     var year = a.getFullYear();
     var month = months[a.getMonth()];
     var date = a.getDate();
     var hour = a.getHours();
     var min = a.getMinutes();
     var sec = a.getSeconds();
     var time = date+' '+month+' '+year+' '+hour+':'+min+':'+sec ;

var difference=Math.round((new Date()).getTime() / 1000);

difference=difference-window.latestpost;
difference=difference/60;
var hours=(difference/60);

var days=difference/1440;
var weeks=difference/10080;

var useronline='';


var lasttimeoutput=difference<=59? ''+(Math.round(difference*10)/10)+' minutes ago.':''+(Math.round(hours*100)/100)+' hours, '+Math.round(days)+' days and '+Math.round(weeks)+' weeks ago.';





var a = new Date();
  var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
     var year = a.getFullYear();
     var month = months[a.getMonth()];
     var date = a.getDate();
     var hour = a.getHours();
     var min = a.getMinutes();
     var sec = a.getSeconds();
     var timenow = date+' '+month+' '+year+' '+hour+':'+min+':'+sec ;

var now=Math.round((new Date()).getTime() / 1000);


var i=window.totalpost;
	while(i>=0 ) {
		
				if ( window.userpost_time[i]!=null) {
				var usern=urldecode(window.userpost[i]);
					var laston=Math.round((now-(window.userpost_time[i]))/60);
					useronline+='<span class=\"userdec\">'+usern+'</span>'+laston+'mins ago';
						if ( i>0 ) 
						{
						useronline+=', ';
						}else{
						useronline+='.';
						}
					
				}
				
				
					--i;
	}
if (useronline=='') {
useronline='nobody is actively posting.';
}

		screenwindowup();
		if ($('#helpwindow').css('display')=='none') {
		$('#helpwindow').show();
		}
window.scrollTo(0,0);		
		//$('#chat').hide();
$('#helpinfo').hide();
$('#otherinfo').show('slow');
$('#otherinfo').html('<div class=\'\'>Current Time:'+timenow+'<br /> Last user post was: '+time+'<br />The last post was '+lasttimeoutput+'<br /> Users last seen ONLINE:<hr><div class=\'user\'> '+useronline+'</div></div>');



break;
case '/music':
if ( a[1] !='' && a[1] !=' ' ) {
	
	setCookie('savemusic',a[1],365);
changemusic(a[1]);

}else{
	setCookie('savemusic','',365);
	params('addwarning', 'music stopped');
	changemusic('');
	
}

break;

case '/help':
case '/?':
params('helpwindow');
break;

		
	}
	$(\"#message\").val(\"\");
	return 1;
}
	
	switch(x) {

case 'msg':
if ($('#message').val()!=null) {
	
return $('#message').val();
}

return false;
break;
case 'username':
		if ($('#username').val().length<3) {
			$('#username').val('Anonymous');
		}
return $('#username').val();
break;
		case 'olduser':
		document.getElementById(\"username\").value=getCookie('username');
		break;
		case 'saveuser':
		var gu=document.getElementById(\"username\").value;
		setCookie('username',gu,365);

break;
case 'helpwindow':
screenwindowup();
window.scrollTo(0,0);

		if ($('#helpwindow').css('display')=='none') {
		$('#helpwindow').show('slow');
		//$('#chat').hide();
		}else {
			$('#helpwindow').hide('slow');
			$('#helpinfo').show();
			$('#otherinfo').hide();
			//$('#chat').show();



}

break;

case 'addwarning':
			if ( window.lastwarning==y ) {
			return;
			}
window.lastwarning=y;
var post=\"<div id='warning' ondblclick='$(this).hide();' class='trans'>\"+urldecode(y)+\"<div style=\'clear:both\'></div></div>\"; 	
		$('#chat').prepend(post); 
		//if spam detect
		//stopchat();
		
		
break;
case 'addexception':
			stopchat();
			window.addpost=0;
			if ( window.lastwarning==y ) {
			return;
			}
window.lastwarning=y;
var post=\"<div id='exception' ondblclick='' class='trans'>\"+urldecode(y)+\"<div style=\'clear:both\'></div></div>\"; 	
		$('#chat').prepend(post); 
		//if spam detect
		
		
		
break;
case 'addpost':

	if (y!='') {

		var pie=y.split('||');
		if (pie[0]>latestpost) { window.latestpost=pie[0]; }
var cascade=window.totalpost%2;
cascade=cascade==0?'p1':'p2';


window.userpost[window.totalpost]=pie[2];
window.userpost_time[window.totalpost]=pie[1];

var d = new Date(pie[1]*1000);

var date=''+d.getFullYear()+'/'+(d.getMonth()+1)+'/'+d.getDate()+' '+d.getHours()+':'+d.getMinutes()+':'+d.getSeconds()+'';
var usern=urldecode(pie[2]);
var post=\"<div id='\"+urldecode(pie[2])+\"' class='\"+cascade+\"'>\"+date+\" <font id='userstyle'><font id='a'>\"+urldecode(pie[2])+\"</font></font> <font color='#84a293'>:</font> \"+urldecode(pie[3])+\"<div style=\'clear:both\'></div></div>\"; 
window.totalpost+=1;		
if (getCookie(urldecode(pie[2]))=='block') {
$('#'+urldecode(pie[2])).hide();
}

		$('#chat').prepend(post); 

		if (!window.hv) {  $( 'object' ).addClass( 'hidevideo' ); }
	}else{

	}


break;


	}
return 0;
}

function submitpost() {
		if (window.addpost==0 || window.initrun!=1) {
		
		return; }
		
			window.addpost=0;
			addpostwait(0);
		
		
	if (params(params('msg'))==1) { }else{
	if (params('msg').length>1) {
		window.postsmade+=1;
			$.post(\"index.php\",{ username : params('username'), msg : params('msg'), captcha : window.captchacode });
		$(\"#message\").val(\"\");
		
		setInterval(\"chattimeout();\", 600000);
	 
	}
}
	
	
}


function runchat( ) {
	
	 
	

  $.post('index.php?gp=1&latestpost='+window.latestpost+'&captcha='+window.captchacode+'',function(data,status){

	if (data != null) {
		//param addpost
				
eval(data);
	//
		
		$('chat').scrollTop($('#chat').prop('scrollHeight'));
	}
	  
   // alert('Data:' + data + 'Status:' + status);
  });
  




 //	var strip_old_string = getdata.replace(window.lastdata, '');//gi global insen



}

function changemusic( x ) {
var url=urldecode(x);

	
if (url==''||url=='undefined' || !url) { $('#musicplayer').html('no music playing');  return;}


		if (getCookie('savemusic') != '' && getCookie('savemusic').length>7 ) {
				//push direct url
			url=getCookie('savemusic');
			
			
		}else {

				if (url == 'random') {
					
					//get stream 
					
					var rs=new Array()
		rs[0]='http://www.youtuberepeater.com/watch?v=Pr6wXZiXyug';
		rs[1]='http://classic.battle.net/window.shtml';


var alength=rs.length;
var picture =rand(alength);



				var url=rs[picture];
			
				}


		}
		
					//parse
			url=url.replace('youtube.com/watch?v=','youtube.com/embed/');
			
					if (url.indexOf('youtube.com')>=0 ) {
url=url.replace('watch?v=','embed/');
				url=url+'?autoplay=1';
			}

			$('#musicplayer').html('<div title=\"('+picture+' of '+alength+') saved music setting: '+getCookie(\"savemusic\")+'\"> playing: <a href=\"'+url+'\" target=\"_blank\">'+url+'</a></div><iframe style=\"width:550px; height:250px;\" src=\"'+url+'\" frameborder=0 autoplay=\"true\" allowfullscreen></iframe><br /> | <a href=\"http://www.shoutcast.com/\" target=\"_blank\">shoutcast</a> |');
		


}

// run initialization cq cmds
/////////////////
params('olduser');


$(document).ready(function() { 

changemusic('random');

});



/*
	$(document).blur(function(){
	  pagetimeout(0);
	});

	$(document).focus(function(){
	  pagetimeout(1);
	});
*/

$(document).scroll(function () {
	if ($(document).scrollTop() > 50 && window.initrun==1 ) {

	$('#chat_nav').attr('class','nav');
	}else{
			$( '#chat_nav').removeClass('nav');
	}

});




// --></script>
		
		
		
		
		</body></html>");
}



?>
