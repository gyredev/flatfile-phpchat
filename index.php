<?php
ob_start();
header ("Content-Type: text/html; charset=utf-8"); 
session_start();
date_default_timezone_set('UTC');
//Set no caching
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
error_reporting(0);//must be 0 chat to work

require('banlist.php');
$bans=new banlist;
$bans->run_init_banlist();

/***   www.phpclub.site.ir   ***/
/*******************************//*
ini_set("error_reporting", E_ALL);
ini_set( "display_errors", 1 );
*/
/* edited by gyrereality.tk, alex kalas diogenes.sinope762@gmail.com
added bbcode, rooms, pass rooms, changeable usernames, chatlogs, antispam
done with "posts per minute" and captcha startup to reduce spam/bot abuse and many other features.


filem time .dat room file if user request spams passed get gp variable less than a day of filem time  or <=0 more than 5x LOG as spammer
*/


///////////////////////////////////////////////////////////////////////////////
			///////////INITIALIZATION VARIABLES AND CONFIG
			////////////////////////////////////////////////////////////////////

if (!isset($_SESSION["time_in"])) {$_SESSION["time_in"]=time();}



$version='<textarea style="width:100%; height:99px; background-color:#220000; font-size:110%; padding:10px;color:#aa2000;">==== v141 recent fixes ====\stronger captcha,nmode captcha modes integrated\nfixed banlist\nadded better data logs\ncan click to add bbcode tags\njoin cmd secure</textarea>';
//get real ip address
$ip_addr=$bans->get_ip();
//tok is unique identifier for each page instance.

//honeypot rand id
//$pot=rand(0,100);

$refresh = 8 ; // Page refresh time in seconds
$remain = 400 ; // Messages remaining time in seconds
$spamlog="abusers.htm";
$username_length = 60;
$avg_post=0;
$ipsalt="ffakopa39a43j5na1";
$salt="s19ajf2ba77uzi4210ae3221";//salt security
$defaultroom="b";
$helpdesc="{$version}<br>list of rooms available (underline require password):  b [Lobby random discussion] <u>ts [top secret]</u> dev [developer and admin contact]<p>commands: /b or // goes to bottom of page, doubleclick on help dialogs to remove from chat. &frasl;j or /join room name and password toggle, &frasl;u  shows users online and current time, /music (random) plays random music from my selected list, /music youtube video url or http://www.youtuberepeater.com/watch?v=(youtube id) to repeat a song, <a onclick=\'params(\"/rst\");\'>/rst or (click to restore saved text)</a>, <a onclick=\'params(\"/stat\");\'>&frasl;stat</a> gives chat stats<p>standard bbcode is allowed. click code to add to post. <a onclick=\'$( \"#message\" ).val( function( index, val ) { return val + \"[youtube][/youtube]\";});\'>[youtube]youtube video id[/youtube]</a> <a onclick=\'$( \"#message\" ).val( function( index, val ) { return val + \"[i][/i]\";});\'>[i][&frasl;i]</a> <a onclick=\'$( \"#message\" ).val( function( index, val ) { return val + \"[b][/b]\";});\'>[b][&frasl;b]</a> <a onclick=\'$( \"#message\" ).val( function( index, val ) { return val + \"[img][/img]\";});\'>[img]image url[&frasl;img]</a> <a onclick=\'$( \"#message\" ).val( function( index, val ) { return val + \"[url][/url]\";});\'>[url]url link[&frasl;url]</a><p>chat timezone is UTC.<p>contact admin in dev room there you can leave questions and/or comments.";
$max_memoryusage=1293912;
//ppt=posts per time period 10 minutes, tbp=time between posts made, timeout=time until checks for posts inactivity timeout
$rooms=array(
"b"=>array("logdata"=>true,"title"=>"gyre reality chat - THE LOBBY","roomscript"=>"
var image=new Array()
image[0]='http://www.mrwallpaper.com/wallpapers/galaxy-cosmic-space.jpg';
image[1]='http://www.hdwallpapers.in/walls/hirosaki_castle_japan-wide.jpg';
image[2]='http://3.bp.blogspot.com/-c2z741NHK04/TWTyoIEaQ5I/AAAAAAAAAHA/s4nPSNyv8jM/s1600/Japanese_Wave_Vista_Background_by_unsanechild.jpg';
image[3]='http://www.moodflow.com/Graphics/Wallpapers/cove_of_dreams_1600.jpg';
image[4]='http://fc07.deviantart.net/fs71/f/2010/136/1/b/Fairy_Tale_Lake_2008_by_moodflow.jpg';
image[5]='http://bigbackground.com/wp-content/uploads/2013/08/zen-buddhism-wallpaper.jpg';
image[6]='http://2.bp.blogspot.com/-JdMhfh0Wmrc/T3HgbuYs7dI/AAAAAAAAADY/eTmdVKVJPs0/s1600/Japanese+wallpaper_Himeji-Castle-Japan.jpg';
image[7]='http://www.moodflow.com/wordpress_moodflow/wp-content/uploads/2010/12/ancient-seabeds.jpg';
image[8]='http://dphclub.com/static/posts/2008-02/dphclub.com_12033215871-18.jpg';
image[9]='http://3.bp.blogspot.com/-GCLok8sY2d4/TaA5Y-1Sa7I/AAAAAAAAB00/TilbR1Dhi4c/s1600/Cool-wallpapers+%25286%2529.jpg';
image[10]='http://www.hdwallpapers.in/walls/thailand_beach_sunset-wide.jpg';
image[11]='http://38.media.tumblr.com/6e455df900b9038552c3c357d0a5a715/tumblr_mnz2kjUKR91qgpse3o1_1280.jpg';
image[12]='http://mde.tv/wordpress/wp-content/themes/mde.tv/images/background-images/moving_wave-cotton_candy_dark.gif';

var rimg0 = image[rand(image.length-1)];

   $(document).ready(function() {
        $('body').css('background-image', 'url('+rimg0+')');
    });
    
","roomstyle"=>"background-image:url(http://www.mrwallpaper.com/wallpapers/galaxy-cosmic-space.jpg); background-attachment:fixed;background-position:center center;","file"=>"lobby.dat","description"=>"[LOBBY] its the main room, hang out and relax","password"=>"","messagehistory"=>40,"maxpostcharacters"=>300,"maxuser"=>12,"ppt"=>.45,"tbp"=>12,"timeout"=>200000),
"ts"=>array("logdata"=>false,"title"=>"top secret","roomstyle"=>"background-color:gray;background-image:url(http://fc06.deviantart.net/fs44/f/2009/059/9/9/DMT_by_leddzeppelin89.jpg);","file"=>"topsecret.dat","description"=>"well 
its a secret room","password"=>"qaz","messagehistory"=>150,"maxpostcharacters"=>700,"maxuser"=>22,"ppt"=>.30,"tbp"=>3,"timeout"=>180000),
"dev"=>array("logdata"=>true,"title"=>"developer room","roomstyle"=>"background-image:url(http://apps.goodereader.com/wp-content/uploads/2012/06/Android-Developer2.png); background-position:top top;","file"=>"dev.dat","password"=>"","messagehistory"=>250,"maxpostcharacters"=>700,"maxuser"=>22,"ppt"=>.50,"tbp"=>33,"timeout"=>180000,"description"=>"give me some suggestions for the site or personal messages"),
);


///////////////////////////////////////////////////////////////////////////////
			///////////END INITIALIZATION
			////////////////////////////////////////////////////////////////////
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

function cleanusername( $x ) {
	global $username_length;
	
	$x=str_ireplace('[youtube]', '', $x);
	$x=str_ireplace('[/youtube]', '', $x);
		$x=str_ireplace('[url]', '', $x);
		$x=str_ireplace('[url]', '', $x);
	$x=str_ireplace('[/url]', '', $x);
				$x=str_ireplace(',', '', $x);

	return parse_generaltext(substr($x,0,$username_length));
}

function generate_captcha($token,$passworded='',$captchatype) {
//generate/validate captcha is syn; with ALL OTHER input spam preventing strategies
	global $salt;
	$captchaimage='';
	$captchaval='';
	$captchadisplay='';
		unset(	$_SESSION['tok'],$_SESSION['tokname'],$_SESSION['chatcaptcha'],$_SESSION['captchas_given_current']);

	switch ($captchatype) {
		case 'textbased':
		
	$arg1 = rand (0,12);
	$arg2 = rand (0,12);
	
$questions=array("{$arg1} * {$arg2} = ?","{$arg1} + {$arg2} = ?","{$arg1} + {$arg2} + {$arg2} = ?");
$answers=array(($arg1*$arg2),($arg1+$arg2),($arg1+$arg2+$arg2));
//pow($arg1,$arg1)

$randquestion=rand(0,sizeof($questions)-1);

$_SESSION['chatcaptcha']=$answers[$randquestion];


	//$_SESSION['chatcaptcha']=md5($answers[$randquestion].$salt);

		if (!isset($_SESSION['chatcaptcha'])) {
			//generate random token
			$_SESSION['tok']=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 2, rand(3,9));
			$_SESSION['tokname']=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 2, rand(3,9));

			 $_SESSION["last_post"]=time();  
			 $_SESSION['captchas_given']+=1;
			 
		 
		//chat is in password mode
		if ($passworded!='') {
			 $_SESSION['chatcaptcha']=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 2, rand(3,9));;
				$captchaval=$_SESSION['chatcaptcha'];
		}else{
				
		 }
		}
		
		break 'textbased';
		case 'imagecaptcha':
		$captchaimage="<img src='captcha/captcha.php?q=".rand(0,1000).">";
$captchadisplay='';	
		if (!isset($_SESSION['chatcaptcha'])) {
			//generate random token
			$_SESSION['tok']=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 2, rand(3,9));
			$_SESSION['tokname']=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 2, rand(3,9));

			 $_SESSION["last_post"]=time();  
			 $_SESSION['captchas_given']+=1;
			 
		 
		//chat is in password mode
		if ($passworded!='') {
			 $_SESSION['chatcaptcha']=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 2, rand(3,9));;
				$captchaval=$_SESSION['chatcaptcha'];
		}else{
				
		 }
		}
		
		break 'imagecaptcha';
		case 'nocaptchahoneypotbot':
		case '':
	$captchadisplay='display:none';	
		if (!isset($_SESSION['chatcaptcha'])) {
			//generate random token
			$_SESSION['tok']=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 2, rand(3,9));
			$_SESSION['tokname']=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 2, rand(3,9));

			 $_SESSION["last_post"]=time();  
			 $_SESSION['captchas_given']+=1;
			 
		 
		//chat is in password mode
		if ($passworded!='') {
			 $_SESSION['chatcaptcha']=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 2, rand(3,9));;
		$captchaval=$_SESSION['chatcaptcha'];

		}else{
				 $_SESSION['chatcaptcha']=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 2, rand(3,9));;
				$captchaval=$_SESSION['chatcaptcha'];

		 }
		}
		break;
	}
		
return "<pre style=\"font-size:9pt; width:100%;padding:0px;margin:0px;font-weight:bold;color:#00ffff;\">       
  ____   _____ ___ ___ ___  __  _   _ _______   __   ____  _  __ _____  
 / _\ `v' | _ | __| _ | __|/  \| | | |_   _\ `v' /  / _| || |/  |_   _| 
| [/\`. .'| v | _|| v | _|| /\ | |_| | | |  `. .'  | \_| >< | /\ || |   
 \__/ !_! |_|_|___|_|_|___|_||_|___|_| |_|   !_!    \__|_||_|_||_||_|   
</pre>".$captchaimage."<input type='text'  placeholder='Captcha' style='".$captchadisplay."' id='captcha' value='".$captchaval."' name='captcha'><input id='captchaanswer' onclick='params(\"/captcha\");' type='button' value='join chatrooms &rarr;'>
<textarea name='pmessage' id='pmessage' alt='".substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 2, rand(3,9))."' style='display:none;'></textarea><input type='text' value='".$_SESSION['tok']."' name='pot".$_SESSION['tokname']."' id='pot' style='display:none;'><input type='hidden' value='".time()."' name='time'><div style='padding:70px 0px 0px 0px;'> <a href='/uploadserver' target='_blank'>gyrereality upload server</a> | <a href='http://z15.invisionfree.com/Dk3/index.php?' target='_blank'>dk3 forums</a> <a href='index.php?qaz=harvestmoon'></a><br /></div>";


}


function validate_captcha($answer,$mode='') {
		global $salt;



	if ( isset($_GET['pmessage']) && $_GET['gp']==1 && ($_GET['pot'.$_SESSION['tokname']]!=$_SESSION['tok'] ) || ( $_GET['pmessage']!='') ) {

			die( "killpoll();changemusic('https://www.youtube.com/watch?v=0HQg9eLzh2I'); params('addexception','".urlencode("incorrect captcha pot pot [".$_GET['pot'.$_SESSION['tokname']]." ".$_SESSION['tok']."];".$_POST['pot'])."');");

		return false;
	}

	if ( isset($_GET['pmessage']) && !isset($_GET['gp']) && ($_POST['pot'.$_SESSION['tokname']]!=$_SESSION['tok'] ) || ( $_POST['pmessage']!='') ) {

			die( "killpoll();changemusic('https://www.youtube.com/watch?v=0HQg9eLzh2I'); params('addexception','".urlencode("incorrect captcha pot pot 2 [".$_GET['pot'.$_SESSION['tokname']]." ".$_SESSION['tok']."];".$_POST['pot'])."');");

		return false;
	}



	if ($answer==$_SESSION['chatcaptcha']){
		return true;
	}
	
	return false;
}



function quickconvert($text, $key = '') {
	// Author: halojoy, July 2006
// Modified and commented by: laserlight
    if ($key == '') {
        return $text;
    }
$text=urldecode($text);
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
    return urlencode($text);
}


function getuniqueusers($array,$t) {
//used to crudely setup slots in chat for max users to post. user is ip encoded


	$onlinetime=$t!=null?$t:600000;
	
	$users=array();
	
	
	$users=array_unique($users);
}



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


$_SESSION["room"]=!isset($_SESSION["room"])?"Lobby":$_SESSION["room"];
$inroom=isset($_SESSION["room"])&& isset($rooms[$_SESSION["room"]]["file"])?$_SESSION["room"]:$defaultroom;
$file=$rooms[$inroom]["file"];

if(!file_exists($file)){
touch($file);
chmod($file,0600);
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
							&& $_SESSION["timesbadpassword"]>4)  ) {
								$correctpassword=0; 
							if (isset($_GET["gp"])) {
	
	die( "killpoll();changemusic('https://www.youtube.com/watch?v=0HQg9eLzh2I'); params('addwarning','".urlencode("".$inroom." requires a password<br /> incorrect ".$inroom." room password.")."');");

						}
							
																	}
							
							
									if ( isset($_SESSION["total_post"]) && ($_SESSION["avg_post"] < $rooms[$inroom]['ppt']) ) {
								
							if (isset($_GET["gp"])) {
							
								
						die( "changemusic('https://www.youtube.com/watch?v=dQw4w9WgXcQ'); params('addexception','".urlencode("[spam] You exceed the posts per time in ".$inroom." [".$_SESSION["avg_post"]."]. the max is [".$rooms[$inroom]['ppt']."]. ")."');");
							
        
						}
							
                                                                            }
                                                                            
                                                                            



///////////////////////////////////////////////////////////////////////////////
			///////////END INITIALIZATION
			////////////////////////////////////////////////////////////////////


							
					///////////////////////////////////////////////////////////////////////////////
			///////////POST		
			///////////////////////////////////////////////////////////////////////
			
			////////////////////////////////////////////////////////////////////
			
			////////////////////////////////////////////////////////////////////
			
			////////////////////////////////////////////////////////////////////


//must setup to return errors in jquery for exception in posting mode.

			///control here by # of unique users posting in chat file by 10 minutes, if #users exceeds max throw exception max user slot exceeded and prevent more messages same for get post
			
                if (   isset($_POST["msg"]) && isset($_POST["captcha"]) && strlen($_POST["msg"])>=1 && strlen(urldecode($_POST["msg"]))<=$rooms[$inroom]["maxpostcharacters"] ){



if ($correctpassword!=1  ) {
    						die( "        changemusic( 'https://www.youtube.com/watch?v=HU2ftCitvyQ' ); params('addexception', '".urlencode("wrong password")."');");
						return;
		
    }
if ($_SESSION["last_post"]>0 && (time()-$_SESSION["last_post"])<$rooms[$inroom]['tbp']  ) {
	//this means theyre posting faster than the js scripted times in the form field, meaning they are abusing through other means
	
	$file = fopen("../".$spamlog."","a");
fputs($file,time().' '.$ip_addr." passing js<br>");
fclose($file);

    						die( "changemusic('https://www.youtube.com/watch?v=Ktbhw0v186Q'); params('addexception', '".urlencode("posting too fast")."');");
						return;
		
    }
    
    			if ( isset($_SESSION["total_post"]) && ($_SESSION["avg_post"] < $rooms[$inroom]['ppt']) ) {
		$file = fopen("../".$spamlog."","a");
fputs($file,time().' '.$ip_addr." ppt<br>");
fclose($file);
						die( "changemusic('https://www.youtube.com/watch?v=eh7lp9umG2I'); params('addexception','".urlencode("[spam] You exceed the posts per time in ".$inroom." [".$_SESSION["avg_post"]."]. the max is [".$rooms[$inroom]['ppt']."]. ")."');");
		
                                                                            }
    

//issue somehow passworded rooms couldnt post? 
if (!validate_captcha(urldecode($_POST["captcha"]),$rooms[$inroom]['password']) ) {
								die( "params('addexception', '".urlencode("9999 incorrect captcha")."');");
						return;
						}		
						
						
						
						
				if ( (  (time()-$_SESSION['time_in'])<=3  )  ) {
						die( "params('addexception', '".urlencode("<pre>slo down buddy (づ｡◕‿‿◕｡)づ </pre>")."');");
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
				

			fclose($fp) ;
if (!isset($_SESSION["total_post"])){
								$_SESSION["total_post"]=1; 
								}else{$_SESSION["total_post"]+=1;}
								
							$min=(time()-$_SESSION["time_in"]) /60;
$avg_post= ( 1+( $min ) / ($_SESSION["total_post"]) );
$_SESSION["avg_post"]=$avg_post;
//backup saved for each month
						
					//	if ($rooms[$inroom]["password"] ==null ){
                        		if ($rooms[$inroom]["logdata"]) {
                        							if(!file_exists("backup-$inroom-".date("M-Y").".htm")){
							touch("backup-$inroom-".date("M-Y").".htm");
                            chmod("backup-$inroom-".date("M-Y").".htm",0600);
                            }
                           //hide msg if passworded
                            //if ($rooms[$inroom]["password"] !=null ) { $convert=''; }
									$bu = fopen("backup-$inroom-".date("M-Y").".htm", "a") ;
									 fputs($bu, "<div>".quickconvert($ip_addr,$ipsalt)."     ".$avg_post."     ".$inroom."     ".$user."     ".date("y/m/d H:i:s")."     {$convert}</div>") ;
									fclose($bu) ;
									
						//		}	
								}
			
			$_SESSION["last_post"]=time();
			die(  );
						}
			//log backup-month-year.htm
//if ( ($e[0]!=$user && $e[2]=="\r\n") || ( $i<$max && ($e[1]+$remain)>time() && $e[2]!="\r\n") ) fputs($fp, $f[$i]) ;


///////////////////////////////////////////////////////////////////////////////
			///////////GETPOST
			////////////////////////////////////////////////////////////////////
			
			////////////////////////////////////////////////////////////////////
			
			////////////////////////////////////////////////////////////////////
			
			////////////////////////////////////////////////////////////////////
            
if (isset($_GET["gp"]) && isset($_GET["latestpost"]) && isset($_GET['captcha'])  ) {

                            $secin=time()-$_SESSION['time_in'];
								if (isset($bans) && $_SESSION['captchas_given']>=1 && isset($_SESSION['time_in'])  && ($secin ) > (470) ) {
									//no posts or in chat over 2hour   $rooms[$inroom]['timeout']
								//($_SESSION["total_post"]+1) <= ( ($secin)/(480) )
								
								$lastin=$_SESSION["last_post"]<5?$_SESSION['time_in']:$_SESSION["last_post"];
								$lastin=time()-$lastin;
								//|| $secin > 14400  $_SESSION["total_post"]<=0 && $lastin >470 ) ||    $_SESSION["total_post"]+1)
                        if ( isset($_SESSION["total_post"]) && ( $_SESSION['captchas_given']>33 ||  (($_SESSION["total_post"]+1)*.90) < ($lastin)/470 ) ) {
                            //reset it for them force refresh let them run for 8min posting
                            die( "changemusic('1'); params('addexception', '".urlencode("chat session timed/expired out, login again. [".$secin.",".$lastin.",captcha max=33 ".$_SESSION['captchas_given']."]")."');");
                            
                           
                            
						return;
						}
						
								}


if ($correctpassword!=1  ) {
    						die( "killpoll(); params('addwarning', '".urlencode("wrong password 2")."');");
						return;
		
    }
    
if (!validate_captcha(urldecode($_GET['captcha']),$rooms[$inroom]['password']) ) {
    						die( "changemusic('http://ubcesa.ca/wp-content/uploads/2013/05/censored.jpg');params('addexception', '".urlencode("444444 captcha wrong")."');");
						return;
                        
		
    }    
    
    
     
    
    
					if ( ( (time()-$_SESSION['chattime'])<=3 ) ) {
			die( "changemusic('https://www.youtube.com/watch?v=_CMlOXHiD_k'); params('addexception','".urlencode("<pre>slo down buddy (づ｡◕‿‿◕｡)づ </pre>")."');");
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
		  
		  echo "params('addpost','".$e[1]."||".urlencode($e[0])."||".urlencode($e[2])."');";
	  }
		  
		--$i;
		}
	
	}else{ $_SESSION['badpost']+=1;  }
	
			die(  ) ;
}
else
{


//can send them to google or kill chat if they get captcha wrong!!
				if (isset($_GET["gp"]) && isset($_POST["msg"]) && strlen($_POST["msg"])>$rooms[$inroom]["maxpostcharacters"]) {
					
						die( "params('addexception', '".urlencode("post too long")."');");
				}
				
				 if ( isset($_GET["gp"]) && !validate_captcha(urldecode($_GET['captcha']),$rooms[$inroom]['password'])  && $rooms[$inroom]['password']=='' ){
						die( "params('addexception', '".urlencode("incorrect captcha -> <- ")."');");

		
				}
				
				
				else if ( isset($_GET["gp"]) || isset($_POST['msg']) ){
						die(  );
		
				}

///////////////////////////////////////////////////////////////////////////////
			///////////CHAT RETURN USER PAGE
			////////////////////////////////////////////////////////////////////
			

    $gcaptcha=generate_captcha($_SESSION['tok'],$rooms[$inroom]['password'],'nocaptchahoneypotbot');
    $passworded=$rooms[$inroom]['password']==''?0:1;
	$onload=$passworded==1?'params("/captcha");':'';
	die("<html><head><title>{$rooms[$inroom]['title']}</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<meta name='identifier-url' content='gyrereality.byethost18.com' />
<meta name='title' content='GYREREALITY Chatrooms' />
<meta name='description' content='chatroom discussing a variety of topics, mostly gaming and life. based around community that played wc3 tft ajizizoc' />
<meta name='abstract' content='chatroom discussing a variety of topics, mostly gaming and life. based around community that played wc3 tft ajizizoc, dk3 wc3, calford' />
<meta name='keywords' content='gyrereality, dk3, dragon-knight3, ajizizoc, biskelion, wc3, fps, gosu players' />
<meta name='author' content='covert' />
<meta name='revisit-after' content='1' />
<meta name='language' content='EN' />
<meta name='copyright' content='©copyleft' />
<meta name='robots' content='All' />
<script type='text/javascript' src='jquery-1.10.1.js'></script>
<link rel='stylesheet' href='style.css' id='stylesheet' type='text/css' media='screen, projection' />
<link rel='shortcut icon' href='gyre.png' type='image/x-icon' />
</head><body align='center' onload='".$onload."' style='".$rooms[$inroom]["roomstyle"]."'>
<script type='text/javascript'>
function rand(max) {
    return Math.floor(Math.random() * (max - 0 + 1)) + 0;
}
".$rooms[$inroom]["roomscript"]."
</script>
	<div style='vertical-align:center;text-align:center;padding:1px;margin:0px 0px 0px 0px;width:100%;' id='chat_nav'><a name='top'></a><input placeholder='Message' maxlength='".$rooms[$inroom]["maxpostcharacters"]."' title='message ( type /? for help)' name='msg' style='width:70%' value='' id='message' onkeyup='entersubmitpost();savetext();' ondblclick=''><input placeholder='Username' maxlength='".$username_length."' id='username' title='username' name='username' 
					value='' onkeyup='params(\"saveuser\");' style='width:15%'> <input type='submit' name='send' onmousedown='submitpost();' value='Send'></div>
		
		
			<div id='chat' class=''><div style='background-color:#000000; padding:5px;' class='trans'>Loading Chatroom... <span style='color:#00ff00;'>".$rooms[$inroom]["description"]."</span> Type <a href='#a' onclick='window.windowup=false;params(\"/users\");'>/u </a>for users online, <a href='#b' onclick='params(\"helpwindow\");'>/? or /help</a> for help with using the chat.<br /> <a href='/uploadserver' target='_blank'>gyrereality upload server</a> | <a href='http://z15.invisionfree.com/Dk3/index.php?' target='_blank'>dk3 forums</a> <a href='index.php?qaz=harvestmoon'></a></div></div><br>
				<div class='captcha' id='captchascreen'><div id='captchadec'></div><img src='http://intermartialarts.com/sites/default/files/jeet-kune-do.jpg' style='position:absolute;top:333px;z-index:0; left:11px;'> <img src='http://intermartialarts.com/sites/default/files/jeet-kune-do.jpg' style='z-index:0;position:absolute;top:333px; right:70px;'><img src='http://images.fineartamerica.com/images-medium-large-5/third-eye-mynzah.jpg' style='width:180px;z-index:0;position:absolute;top:233px; left:530px;'><div class='trans' style='position:absolute; left:13.5%;	-moz-border-radius: 1em 1em 1em 1em;
		border-radius: 1em 1em 1em 1em; top:1%; width:70%; z-index:3;color:#ffffff;background-color:#000000;border:1px solid #121212;padding:8px;text-align:center;'>".$gcaptcha."</div></div>
		

				
<div id='musicplayer' style='text-align:center;color:#992800;font-size:144%;text-shadow: 0.1em 0.1em 0.2em white;'></div>

		<script type='text/javascript'>
<!--

$( '#chatpassword' ).val('');
 
 //begin captcha shuf	 
			var hue = 'rgb(' + (Math.floor(Math.random() * 256)) + ',' + (Math.floor(Math.random() * 256)) + ',' + (Math.floor(Math.random() * 256)) + ')';
			
$('#captchaq').css('color',hue);

var images=new Array()

images[0]='http://www.thegorgeousdaily.com/wp-content/uploads/2012/11/alex-grey-1.jpg';
images[1]='https://images.duckduckgo.com/iu/?u=http%3A%2F%2Fandrewouellette.com%2Fwp-content%2Fuploads%2F2011%2F01%2FOm_in_Lotus_by_Ledrahan.jpg&f=1';
images[2]='https://images.duckduckgo.com/iu/?u=http%3A%2F%2Fauntiedogmasgardenspot.files.wordpress.com%2F2013%2F06%2Fsyrianrue.jpg&f=1';
images[3]='https://images.duckduckgo.com/iu/?u=http%3A%2F%2Fwww.computerfreewallpapers.com%2Fbulkupload%2Fwall6%2FAbstract%2FSpace%2520Art%2FSpace-Art-Wallpapers-29.jpg&f=1';
images[4]='https://images.duckduckgo.com/iu/?u=http%3A%2F%2F2.bp.blogspot.com%2F-OKC7hz6suiY%2FTygtZyX4jRI%2FAAAAAAAAIgg%2FP7zbNLWvrao%2Fs1600%2FPassion_flower_beautiful%252B%2525285%252529.jpg&f=1';
images[5]='http://www.bestfon.info/images/joomgallery/originals/armas_5/vss_vintorez_20130330_1650009294.jpg';

//images[4]='';
var picture='';

    function randpic() {

window.picture =images[rand(images.length-1)];
setTimeout(function() {
$('#captchadec').fadeOut('slow');
}, 1500);
setTimeout(function() {
$('#captchadec').html('<img src=\"'+picture+'\" alt=\"'+picture+'\">').fadeIn('slow');

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

var passworded='".$passworded."';

var tnamespace = {};

var chattimer='';
var usertimer='';

var lastwarning=null;

var refresh='".($refresh*1000)."';
var timebetweenpost='".($rooms[$inroom]['tbp']*1000)."';
var timeout='".($rooms[$inroom]['timeout'])."';
var timeoutobj='';
var addpost=1;

var scrollfromtop=0;

var windowup=false;

var userpost=new Array();
var userpost_time=new Array();



function screenwindowup() {
    return false;
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

function resumechat() {
    window.chattimer=setInterval(\"runchat();\", window.refresh);
    window.addpost=1;
window.initrun=0;
}

function killpoll() {
	clearInterval(window.chattimer);
	clearInterval(window.timeoutobj);
}

function stopchat() {
clearInterval(window.chattimer);
clearInterval(window.timeoutobj);
window.addpost=0;
window.initrun=2;
}

function savetext() {

var t=$('#message').val();
if (t.length>=7) {
setCookie('savetext',t,365);
}

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


function initchat(){

if (window.initrun==1){ return; }
$('#message').focus();

		clearInterval(captchadec);
	 window.chattimer=setInterval(\"runchat();\", window.refresh);
	 window.timeoutobj=setInterval(\"chattimeout();\", timeout);
	 
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
window.chattimer=setInterval(\"runchat();\", window.refresh);

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
		params('addwarning','captchacode:'+window.captchacode+'<br />posts shown:'+window.totalpost+'<br>posts you have made:'+window.postsmade+'<br>current username:'+params(\"username\")+'<br />rimg0='+window.rimg0+'<br />picture='+window.picture+'');

break;
case 'blur':
		params('addwarning','AFK');
stopchat();
break;
case 'focus':
		params('addwarning','back');
resumechat();
break;


case '/captcha':
  
    if (window.passworded==0) {
		  window.captchacode=$('#captcha').val();

    if (window.initrun!=0 || window.captchacode==''){ return false; }


}else{
  window.captchacode=$('#captcha').val();

  }
  
  
				if ($('#captchascreen').css('display')=='none') {
$('#captchascreen').show('slow');
}else{
	$('#captchascreen').hide('slow');
}

initchat();
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


var lasttimeoutput=difference<=59? ''+(Math.round(difference*10)/10)+' minutes ago.':''+(Math.round(hours*100)/100)+' hour(s), '+Math.round(days)+' day(s) or '+Math.round(weeks)+' week(s) ago.';





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
					useronline+='<span class=\"userdec\">'+usern+'</span>  '+laston+'mins ago';
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

$(\"html, body\").animate({ scrollTop: 0 }, \"slow\");
		screenwindowup();		
        params('addwarning','<div class=\'\'>Current Time:'+timenow+'<br /> Last user post was: '+time+'<br />The last post was '+lasttimeoutput+'<br /> Users last seen ONLINE:<hr><div class=\'user\'> '+useronline+'</div><hr></div>');



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
case '/b':
case '//':
$(\"#message\").val(\"\");
$(\"html, body\").animate({ scrollTop: $(window).scrollTop() + $(window).height() }, \"slow\");

break;
case '/join':
case '/j':
$(\"#message\").val(\"\");
		
		
//$.post(\"index.php\",{ username : params('username'), password : a[2], room : a[1], captcha : window.captchacode },function(data,status){setInterval(\"location=''\",4000);});
params('addexception', '<fieldset><legend>join room</legend><form action=\"index.php\" method=\"POST\"><input type=\"text\" placeholder=\"room\" name=\"room\"><br /><input type=\"password\" placeholder=\"password\" name=\"password\"><br /><input type=\"submit\" value=\"travel to some room\"> </form></fieldset>');

          //setInterval(\"location.href=''\",11000);
          $(\"html, body\").animate({ scrollTop: 0 }, \"slow\");
break;
case '/rst':
var t=getCookie('savetext');
	params('addwarning', '<textarea style=\"width:600px;\">'+t+'</textarea>');


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

return $('#username').val();
break;
		case 'olduser':
		document.getElementById(\"username\").value=getCookie('username');
		break;
		case 'saveuser':

setCookie('username',params('username'),365);

break;
case 'helpwindow':
screenwindowup();
$(\"html, body\").animate({ scrollTop: 0 }, \"slow\");
params('addwarning',urlencode('".($helpdesc)."'));


break;

case 'addwarning':
			if ( window.lastwarning==y ) {return;}
window.lastwarning=y;
var post=\"<div id='warning' ondblclick='$(this).hide();' class='trans2'>\"+urldecode(y)+\"<div style=\'clear:both\'></div></div>\"; 	
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
var post=\"<div id='exception' ondblclick='' class='trans2'>\"+urldecode(y)+\"<div style=\'clear:both\'></div></div>\"; 	
		$('#chat').prepend(post); 
		//if spam detect

break;
case 'addpost':

	if (y!='') {

		var pie=y.split('||');
		if (pie[0]>latestpost) { window.latestpost=pie[0]; }
var cascade=window.totalpost%2;
cascade=cascade==0?'p1':'p2';


window.userpost[window.totalpost]=pie[1];
window.userpost_time[window.totalpost]=pie[0];

var d = new Date(pie[0]*1000);

var date=''+d.getFullYear()+'/'+(d.getMonth()+1)+'/'+d.getDate()+' '+d.getHours()+':'+d.getMinutes()+':'+d.getSeconds()+'';
var usern=urldecode(pie[1]);
var post=\"<div id='\"+pie[1]+\"' class='\"+cascade+\" '><span>\"+date+\" <font id='userstyle'>\"+usern+\"</font>:</span> \"+urldecode(pie[2])+\"<div style=\'clear:both\'></div></div>\"; 
window.totalpost+=1;
if (getCookie(urldecode(pie[1]))=='block') {
$('#'+urldecode(pie[1])).hide();
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
	
			$.post(\"index.php\",{ pot".$_SESSION['tokname']." : $('#pot').val(), pmessage : $('#pmessage').val(), username : params('username'), msg : params('msg'), captcha : window.captchacode },function(data,status){   
    
	if (data != null) {
		//param addpost
				
eval(data);
	//
		
		$('chat').scrollTop($('#chat').prop('scrollHeight'));
}

                });
		$(\"#message\").val(\"\");
		clearInterval(window.timeoutobj);
		window.timeoutobj=setInterval(\"chattimeout();\", 480000);
	 
	}
}
	
	
}


function runchat() {
	
$.post('index.php?gp=1&pot".$_SESSION['tokname']."='+$(\"#pot\").val()+'&pmessage='+$(\"#pmessage\").val()+'&latestpost='+window.latestpost+'&captcha='+window.captchacode+'',function(data,status){

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

		if (getCookie('savemusic') != null && url == 'saved') {
				//push direct url
			url=getCookie('savemusic');
			
			 }
if (url==''||url=='undefined' || !url || url=='saved') { $('#musicplayer').html('');  return;}


	
									 
									 if (url == 'random') {
											
											//get stream 
											
											var rs=new Array()
								rs[0]='http://classic.battle.net/window.shtml';
								

						var alength=rs.length;
						var picture =rand(alength-1);



										var url=rs[picture];

										}else if(url == '1') {
																var rs=new Array()
								rs[0]='https://www.youtube.com/watch?v=bSDb8pD6sno';
									rs[1]='https://www.youtube.com/watch?v=Tb-gI_pFog0';
									rs[2]='https://www.youtube.com/watch?v=eB9WgR_N4h4';
									rs[3]='https://www.youtube.com/watch?v=cUbCp2WgkYw';
										rs[4]='https://www.youtube.com/watch?v=vS3C0s3wuN4';
										rs[5]='https://www.youtube.com/watch?v=6rYhRqf757I';
									
									
									
									
						var alength=rs.length;
						var picture =rand(alength-1);



										var url=rs[picture];
										
										}
		
					//parse
			url=url.replace('youtube.com/watch?v=','youtube.com/embed/');
			
					if (url.indexOf('youtube.com')>=0 ) {
url=url.replace('watch?v=','embed/');
				url=url+'?autoplay=1';
			}
			$('#musicplayer').html('<div class=\"trans\" style=\'padding:60px;\' title=\"('+picture+' of '+(alength-1)+') saved music setting: '+getCookie(\"savemusic\")+'\"><iframe style=\"width:550px; height:250px;\" src=\"'+url+'\" frameborder=0 autoplay=\"true\" allowTransparency=\"true\" allowfullscreen></iframe></div>');
}

// run initialization cq cmds
/////////////////
params('olduser');


$(document).ready(function() { 

changemusic('saved');

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
