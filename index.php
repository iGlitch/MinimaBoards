<?php

$script_start_time = microtime(true);

date_default_timezone_set("US/Eastern");

$threadsperpage=25;
$postsperpage=15;

$editexpire=60*60*24; // 1 day

$timefmt="g:i A T";
$datefmt="F j, Y";

$cookie_uname = ""; //PUT SOMETHING HERE
$cookie_token = "minimal"; //PUT SOMETHING HERE
$cookie_site = "."; //PUT LINK TO BOARDS HERE & keep the . prepended
$cookie_path = "/";
$cookie_expire = 60*60*24*60; // 60 days

$my_path = "index.php";
$full_path = ""; //PUT LINK TO index.php HERE

$editable_whitelist=array(
26929,
34609);

$tags_search=array(
 "/\\r\\n?/",
 "/\[yt\=(.*?)\]/i",
 "/\\n/",
 "/\\t/",
 "/\[url\=(.*?)\](.*?)\[\/url\]/i",
 "/\[img\=(.*?)\]/i",
 "/\[i\](.*?)\[\/i\]/i",
 "/\[u\](.*?)\[\/u\]/i",
 "/\[b\](.*?)\[\/b\]/i",
 "/\[em\](.*?)\[\/em\]/i",
 "/\[small\](.*?)\[\/small\]/i",
 "/&amp;#([0-9]*);/i", // don't destroy unicode
);

$tags_replace=array(
 "<br>","<br>",
 "&nbsp;&nbsp;&nbsp;&nbsp;",
 "<object height=\"390\" width=\"640\"><param name=\"movie\" value=\"http://www.youtube.com/v/\\1?version=3\"><param name=\"allowFullScreen\" value=\"true\"><param name=\"allowScriptAccess\" value=\"always\"><embed src=\"http://www.youtube.com/v/\\1?version=3\" type=\"application/x-shockwave-flash\" allowfullscreen=\"true\" allowScriptAccess=\"always\" width=\"640\" height=\"390\"></embed></object>",
 "<a href=\"\\1\">\\2</a>",
 "<img src=\"\\1\">",
 "<i>\\1</i>",
 "<u>\\1</u>",
 "<b>\\1</b>",
 "<em>\\1</em>",
 "<small>\\1</small>",
 "&#\\1;",
);

$tags_decode_search=array(
 "/<br>/",
 "/&nbsp;&nbsp;&nbsp;&nbsp;/",
 "/<object height\=\"390\" width\=\"640\"><param name\=\"movie\" value\=\"http:\/\/www.youtube.com\/v\/(.*?)\?version\=3\"><param name\=\"allowFullScreen\" value\=\"true\"><param name\=\"allowScriptAccess\" value\=\"always\"><embed src\=\"http:\/\/www.youtube.com\/v\/(.*?)?version\=3\" type\=\"application\/x\-shockwave\-flash\" allowfullscreen\=\"true\" allowScriptAccess\=\"always\" width\=\"640\" height\=\"390\"><\/embed><\/object>/",
 "/<a href\=\\\"(.*?)\\\">(.*?)<\/a>/",
 "/<img src=\\\"(.*?)\\\">/",
 "/<i>(.*?)<\/i>/",
 "/<u>(.*?)<\/u>/",
 "/<b>(.*?)<\/b>/",
 "/<em>(.*?)<\/em>/",
 "/<small>(.*?)<\/small>/",
);

$tags_decode_replace=array(
 "\n",
 "\t",
 "[yt=\\1]",
 "[url=\\1]\\2[/url]",
 "[img=\\1]",
 "[i]\\1[/i]",
 "[u]\\1[/u]",
 "[b]\\1[/b]",
 "[em]\\1[/em]",
 "[small]\\1[/small]",
);

function pageheader($title=NULL) {
   $messages=array(
   "So, you wanted a message board, eh?",
   "Waah! Mommy, where are my cookies",
   "Keep It Simple, Stupid",
   "Minimalist, yet functional (barely)",
   "Not even your father's rock &amp; roll (we're still beating the rocks together)",
   "What, you want the server to search *for* you?",
   "It's one of the places to be! :)",
   "It's like eating",
   "The world is corrupt!",
   "Not even remotely secure",
   "We bring slightly less buggy things to life",
   "Brute force ROMhacking since 2004",
   "With a side of search",
   "It's like you want",
   "Anything else is gaslight",
   "take that, morning-me",
   "obscure enough to be secure?",
   "Welcome to the Blast Radius",
   "Therefore you are wrong.",
   "it shifts to attack mode",
   "activate, resonate, precipitate",
   );

   echo "<html><head><title>Minimal Boards - ";
   if (is_null($title)) echo $messages[rand(0,count($messages)-1)];
   else echo $title;
?>
</title>
<link rel="stylesheet" type="text/css" href="forum.css">
<link rel="alternate" type="application/rss+xml" title="RSS" href="forum.php?rss">
</head><body>
<?php
}

function tagsinstructions() {
?>
<td rowspan=4 class="forumcaption"><b>Tags:</b><br><br>
<b>bold</b>: [b]bold[/b]<br>
<i>italics</i>: [i]italics[/i]<br>
<em>emphasis</em>: [em]emphasis[/em]<br>
<u>underline</u>: [u]underline[/u]<br>
<small>small</small>: [small]small[/small]<br>
<a href="http://www.google.com">Link</a>: [url=http://www.google.com]Link[/url]<br>
<img src="http://www.hcs64.com/images/mm1.png"><br>[img=http://path.to/img.png]
<?php
}

function NewPostForm($threadid) {
global $cookie_uname, $cookie_token;
?>

<form action="<?php echo "index.php"; ?>?addpost" method="POST">
<table>
<tr><td class="formcaption">User Name<td>
<?php
if (!isset($_COOKIE[$cookie_uname]) || !isset($_COOKIE[$cookie_token]))
   echo "<input type=\"text\" name=\"author\">";
else
   echo $_COOKIE[$cookie_uname];

tagsinstructions();
?>
</tr>
<tr><td class="formcaption">Password<td>
<?php
if (!isset($_COOKIE[$cookie_uname]) || !isset($_COOKIE[$cookie_token]))
   echo "<input type=\"password\" name=\"pass\">";
else
   echo "**********";
?>
</tr>
<tr><td class="formcaption">Subject<td><input type="text" name="subject"></tr>
<tr><td class="formcaption">Message<td><textarea name="message" rows="15" cols="50"></textarea>
<tr><td align="center" colspan="2"><input type="submit" value="Submit"></td></tr>
</table>
<input type="hidden" name="inresponseto" value="<?php echo $threadid; ?>">
</form>

<?php

}   // end function NewPostForum

function EditPostForm($postid,$content,$subject) {
global $cookie_uname, $cookie_token;
?>
<form action="<?php echo $my_path;?>?editpost2" method="POST">
<table>
<tr><td class="formcaption">User Name<td>
<?php
if (!isset($_COOKIE[$cookie_uname]) || !isset($_COOKIE[$cookie_token]))
   echo "<input type=\"text\" name=\"author\">";
else
   echo $_COOKIE[$cookie_uname];

tagsinstructions();
?>
</tr>
<tr><td class="formcaption">Password<td>
<?php

if (!isset($_COOKIE[$cookie_uname]) || !isset($_COOKIE[$cookie_token]))
   echo "<input type=\"password\" name=\"pass\">";
else
   echo "**********";
?>
</tr>
<tr><td class="formcaption">Subject<td><input type="text" name="subject" value="<?php echo $subject; ?>"></tr>
<tr><td class="formcaption">Message<td><textarea name="message" rows="15" cols="50"><?php echo $content; ?></textarea>
<tr><td align="center" colspan="2"><input type="submit" value="Submit"> <input type="reset" value="Reset"></td></tr>
</table>
<input type="hidden" name="posttoupdate" value="<?php echo $postid; ?>">
</form>

<?php

}   // end function EditPostForm

function PrevNext($action, $ppage, $npage, $highr, $nresults) {
echo "<table border=0><tr>\n";
if( $ppage > 0) {
	echo "<td>";
	echo "<form action=\"$action\" method=\"POST\">\n";
	echo "<input type=\"hidden\" name=\"query\" value=\"$_POST[query]\">\n";
	echo "<input type=\"hidden\" name=\"searchwhere\" value=\"$_POST[searchwhere]\">\n";
	echo "<input type=\"hidden\" name=\"searchhow\" value=\"$_POST[searchhow]\">\n";
	echo "<input type=\"hidden\" name=\"page\" value=\"$ppage\">\n";
	echo "<input type=\"submit\" value=\"Previous Page\">\n";
	echo "</form>\n";
	echo "</td>\n";
}
if(($highr) < $nresults) {
	echo "<td>";
	echo "<form action=\"$action\" method=\"POST\">\n";
	echo "<input type=\"hidden\" name=\"query\" value=\"$_POST[query]\">\n";
	echo "<input type=\"hidden\" name=\"searchwhere\" value=\"$_POST[searchwhere]\">\n";
	echo "<input type=\"hidden\" name=\"searchhow\" value=\"$_POST[searchhow]\">\n";
	echo "<input type=\"hidden\" name=\"page\" value=\"$npage\">\n";
	echo "<input type=\"submit\" value=\"Next Page\">\n";
	echo "</form>\n";
	echo "</td>\n";
}
echo "</tr></table>\n";
}

// authenticate by user name/pass or by cookies
// return user id, die if authentication fails
function authenticate($dbh,$user,$pass) {
global $cookie_uname,$cookie_token;
if (isset($user) && $user!='' && isset($pass) && $pass!='') {
   $query=mysqli_prepare($dbh,"
    SELECT idx
    FROM users
    WHERE uname = ? AND pass = ?
   ") or die("auth attempt: ".mysqli_error($dbh));

   mysqli_stmt_bind_param($query,'ss',$user,$pass);
   mysqli_stmt_execute($query) or die(mysqli_error($dbh));
   mysqli_stmt_bind_result($query,$uid);

   if (!mysqli_stmt_fetch($query)) die("Authentication failed ".mysqli_error($dbh));

   mysqli_stmt_close($query);

} else if (isset($_COOKIE[$cookie_uname]) && isset($_COOKIE[$cookie_token])) {
   $query=mysqli_prepare($dbh,"
    SELECT idx
    FROM users
    WHERE uname = ? AND logintoken = ?
   ") or die("auth attempt: ".mysqli_error($dbh));

   mysqli_stmt_bind_param($query,'ss',$_COOKIE[$cookie_uname],$_COOKIE[$cookie_token]);
   mysqli_stmt_execute($query) or die(mysqli_error($dbh));
   mysqli_stmt_bind_result($query,$uid);

   if (!mysqli_stmt_fetch($query)) die ("Authentication failed".mysqli_error($dbh));

   mysqli_stmt_close($query);

} else die("Authentication failed (incomplete data).");

return $uid;
}

// update the last updated timestamp for a post/thread
function update_post_time($dbh,$idx) {
   $query = mysqli_prepare($dbh,"
    UPDATE board
    SET lasttime = NOW()
    WHERE idx = ?
    LIMIT 1
   ") or die("update error: ".mysqli_error($dbh));

   mysqli_stmt_bind_param($query,'i',$idx);
   mysqli_stmt_execute($query) or die(mysqli_error($dbh));
   mysqli_stmt_close($query);
}


// ***************************** Top of code ********************************

require("dblogin.php");
require("dblogin_write.php");

$dbh = dblogin();

if (isset($_GET['login'])) {

// **** Display login form
pageheader();
?>
<form action="<?php echo $my_path;?>?login2" method="POST">
<table>
<tr><td>User Name<td><input type="text" name="uname" maxlength="31"></tr>
<tr><td>Password<td><input type="password" name="pass" maxlength="31"></tr>
<tr><td align="center" colspan="2"><input type="submit" value="Submit"></tr></table>
<?php

} else if (isset($_GET['login2'])) {

// **** Process login

$query=mysqli_prepare($dbh, "SELECT idx, lastlogin, UNIX_TIMESTAMP(lastlogin) AS llstamp  FROM users  WHERE uname=? AND pass=?");

mysqli_stmt_bind_param($query,'ss',$_POST['uname'],$_POST['pass']);

mysqli_stmt_execute($query) or die ("auth attempt: ".mysqli_error($dbh));
mysqli_stmt_store_result($query);

if (mysqli_stmt_num_rows($query) != 1) die("Authentication failed.");

mysqli_stmt_bind_result($query,$uid,$lastlogin,$llstamp);

mysqli_stmt_fetch($query) or die (mysqli_error($dbh));

mysqli_stmt_close($query);

if (!isset($_COOKIE['$cookie_uname']) || !isset($_COOKIE['$cookie_token'])) {
	$newid = md5(uniqid(rand(), true));
	setcookie($cookie_uname,$_POST['uname'],time()+$cookie_expire,$cookie_path,$cookie_site);
	setcookie($cookie_token,$newid,time()+$cookie_expire,$cookie_path,$cookie_site);
	
	pageheader();
	echo "cookie sent";

	// store cookie in db
    $dbh_write = dblogin_write();
    $query=mysqli_prepare($dbh_write,"UPDATE users SET logintoken=?, lastlogin=NOW(), prevlogin=? WHERE idx=?") or die (mysqli_error($dbh_write));

    mysqli_stmt_bind_param($query,'ssi',$newid,$lastlogin,$uid);
    mysqli_stmt_execute($query) or die(mysqli_error($dbh_write));
    mysqli_stmt_close($query);

    mysqli_close($dbh_write);
} else {
	// update cookie
	setcookie($cookie_uname,$_COOKIE['$cookie_uname'],time()+$cookie_expire,$cookie_path,$cookie_site);
	setcookie($cookie_token,$_COOKIE['$cookie_token'],time()+$cookie_expire,$cookie_path,$cookie_site);
	
	pageheader();
	echo "updated cookie sent";

	// store cookie in db

    $dbh_write = dblogin_write();
    $query=mysqli_prepare($dbh_write,"UPDATE users SET lastlogin = NOW(), prevlogin = ? WHERE idx = ?");

    mysqli_stmt_bind_param($query,'si',$lastlogin,$uid);
    mysqli_stmt_execute($query) or die(mysqli_error($dbh_write));
    mysqli_stmt_close($query);

    mysqli_close($dbh_write);
}
if ($llstamp > 0) echo "<br><br>welcome back $_POST[uname].<br>your last recorded activity was ".date($timefmt." ".$datefmt,$llstamp);
else echo "<br><br>Thanks for logging in, $_POST[uname].";
echo "<br><a href=\"$my_path?\">proceed to the forum</a>";

} else if (isset($_GET['logout'])) {

// Log Out

setcookie($cookie_uname, "", time() - 3600, $cookie_path,$cookie_site);
setcookie($cookie_token, "", time() - 3600, $cookie_path,$cookie_site);

if (isset($_COOKIE[$cookie_uname])) {
    $dbh_write = dblogin_write();

    $query=mysqli_prepare($dbh_write,"UPDATE users SET logintoken = NULL, lastlogin = NOW() WHERE uname = ? AND logintoken = ?");

   mysqli_stmt_bind_param($query,'ss',$_COOKIE[$cookie_uname],$_COOKIE[$cookie_token]);
   mysqli_stmt_execute($query) or die(mysqli_error($dbh_write));
   mysqli_stmt_close($query);

   mysqli_close($dbh_write);
   $deleted=1;
} else $deleted=0;

pageheader();

if ($deleted==1) echo "Cookies deleted.<br><br>";
else echo "Cookies not found, trying to delete anyway.";

} else if (isset($_GET['adduser'])) {
// **** Display form to add a user
pageheader();
?>
<form action="<?php echo $my_path;?>?adduser2" method="POST">
<table>
<tr><td>User Name<td><input type="text" name="uname" maxlength="31"></tr>
<tr><td>Password<td><input type="password" name="pass" maxlength="31"></tr>
<tr><td>Verify Password<td><input type="password" name="vpass" maxlength="31"></tr>
<tr><td align="center" colspan="2">Please note that passwords are stored and transmitted unencrypted,<br>so don't use anything sensitive.
<br><input type="submit" value="Submit"></tr></table>

</form>
<?php

} else if (isset($_GET['adduser2'])) {
// **** Add a user to the database

pageheader();

// check if user already exists
$query = mysqli_prepare($dbh,"SELECT idx FROM users WHERE uname = ?");
mysqli_stmt_bind_param($query,'s',$_POST['uname']);
mysqli_stmt_execute($query) or die (mysql_error($dbh));
mysqli_stmt_store_result($query);
$results = mysqli_stmt_num_rows($query);
mysqli_stmt_close($query);

if ($results == 0) {
   if ($_POST['pass'] == $_POST['vpass']) {
      if ($_POST['pass'] == '') die("no blank password");
      if ($_POST['pass'][strlen($_POST['pass'])-1] == '!') die("no blank password");

      $dbh_write = dblogin_write();
      $query = mysqli_prepare($dbh_write,"INSERT INTO users SET idx=NULL, joined=NOW(), uname=?, pass=?");
      mysqli_stmt_bind_param($query,'ss',$_POST['uname'],$_POST['pass']);
      mysqli_stmt_execute($query) or die (mysqli_error($dbh_write));
      mysqli_stmt_close($query);

      mysqli_close($dbh_write);

      echo "Welcome to <a href=\"$my_path?showthreads\">the forum</a>!";
   } else echo "the passwords did not match";
} else echo "User name $_POST[uname] already exists.";

} else if (isset($_GET['userinfo'])) {

// **** User info page

// get info from users database
$query= mysqli_prepare($dbh,"SELECT uname, UNIX_TIMESTAMP(joined) as joindate, logintoken, UNIX_TIMESTAMP(lastlogin) as login FROM users
                       WHERE idx = ?");
mysqli_stmt_bind_param($query,'i',$_GET['userinfo']);
mysqli_stmt_execute($query) or die(mysqli_error($dbh));
mysqli_stmt_store_result($query);
if (mysqli_stmt_num_rows($query) != 1) die("no such user");

mysqli_stmt_bind_result($query,$uname,$joindate,$logintoken,$login);
mysqli_stmt_fetch($query) or die(mysqli_error($dbh));
mysqli_stmt_close($query);

// get post count, last post

$query = mysqli_prepare($dbh,"SELECT COUNT(*) AS postcount, UNIX_TIMESTAMP(MAX(postedtime)) as lasttime FROM board WHERE author = ?");
mysqli_stmt_bind_param($query,'i',$_GET['userinfo']);
mysqli_stmt_execute($query) or die(mysqli_error($dbh));
mysqli_stmt_bind_result($query,$postcount,$lasttime);
mysqli_stmt_fetch($query) or die(mysqli_error($dbh));
mysqli_stmt_close($query);

pageheader($uname." user info");

echo "Info for user &quot;$uname&quot;:<br><br>";
echo "Joined: ".date($datefmt,$joindate)."<br>";
echo "Posts: $postcount";

if ($postcount > 0) echo ", last posted ".date("$datefmt $timefmt",$lasttime)."<br>";

if (isset($logintoken) && $logintoken != "") echo "Logged in ".date("$datefmt $timefmt",$login)."<br>";
else if ($login > 0) echo "Last logged in ".date("$datefmt $timefmt",$login)."<br>";
else echo "Never logged in.<br>";

echo "<br><a href=\"$my_path?userlist\">User List</a>";

} else if (isset($_GET['userlist'])) {

// **** User list

pageheader("User List");

$query = mysqli_prepare($dbh,"SELECT COUNT(*) AS postcount, users.uname AS uname, UNIX_TIMESTAMP(users.joined) AS joined, users.idx AS idx
 FROM board, users
 WHERE board.author = users.idx
 GROUP BY uname
 ORDER BY uname DESC");

/*$query = mysqli_prepare($dbh,"
 SELECT COUNT(*) AS postcount, users.uname AS uname, UNIX_TIMESTAMP(users.joined) AS joined, users.idx AS idx
 FROM board, users
 WHERE board.author = users.idx
 GROUP BY uname
 ORDER BY postcount DESC
");*/
mysqli_stmt_execute($query);
mysqli_stmt_store_result($query);

echo "User list:<br><br>".mysqli_stmt_num_rows($query)." users<br><table border=1 class=\"userlist\"><tr><td>Name</td><td>Post Count</td><td>Joined</td></tr>\n";

mysqli_stmt_bind_result($query,$postcount,$uname,$joined,$uid);
while (mysqli_stmt_fetch($query)) {
   echo "<tr><td class=\"name\"><a href=\"$my_path?userinfo=$uid\">$uname</a></td><td>$postcount</td><td>".date($datefmt,$joined)."</td></tr>\n";
}

mysqli_stmt_close($query);

echo "</table>\n";

} else if (isset($_GET['chpass'])) {
// **** Change password form

pageheader();
?>
Change Password:<br>
<form action="<?php echo $my_path;?>?chpass2" method="POST">
<table>
<tr><td>User Name<td><input type="text" name="uname" maxlength="31"></tr>
<tr><td>Old Password<td><input type="password" name="oldpass" maxlength="31"></tr>
<tr><td>New Password<td><input type="password" name="newpass" maxlength="31"></tr>
<tr><td>Verify New Password<td><input type="password" name="vnewpass" maxlength="31"></tr>
<tr><td align="center" colspan="2"><input type="submit" value="Submit"></tr>
</table>
</form>
<?php

} else if (isset($_GET['chpass2'])) {

// **** Set new password

pageheader();
if ($_POST['newpass'] != $_POST['vnewpass']) die ("password verification error");
if ($_POST['newpass'] == '') die ("no blank password");

$dbh_write = dblogin_write();

$query = mysqli_prepare($dbh_write,"UPDATE users SET pass=? WHERE uname=? AND pass=? LIMIT 1") or die(mysqli_error($dbh_write));

mysqli_stmt_bind_param($query,'sss',$_POST['newpass'],$_POST['uname'],$_POST['oldpass']);
mysqli_stmt_execute($query) or die(mysqli_error($dbh_write));

if (mysqli_stmt_affected_rows($query) != 1) die ("password change failed");

mysqli_stmt_close($query);
mysqli_close($dbh_write);

echo "Password Changed.";

} else if (isset($_GET['showthread'])) {

// **** Show a single thread

// put thread subject in title

$query = mysqli_prepare($dbh,"SELECT subject FROM board WHERE idx = ?") or die(mysqli_error());
mysqli_stmt_bind_param($query,'i',$_GET['showthread']);
mysqli_stmt_execute($query) or die(mysqli_error($dbh));
mysqli_stmt_bind_result($query,$subject);
mysqli_stmt_fetch($query) or die("no such post found".mysqli_error($dbh));
pageheader($subject);
mysqli_stmt_close($query);

// count posts in thread

$query = mysqli_prepare($dbh,"SELECT COUNT(*) FROM board WHERE board.replyto = ? OR board.idx = ?") or die(mysql_error());
mysqli_stmt_bind_param($query,'ii',$_GET['showthread'],$_GET['showthread']);
mysqli_stmt_execute($query) or die(mysqli_error($dbh));
mysqli_stmt_bind_result($query,$postcount);
mysqli_stmt_fetch($query) or die(mysqli_error($dbh));
mysqli_stmt_close($query);
error_reporting(E_ALL ^ E_NOTICE);
$pageno = $_GET['showpage'];
$lastpage = floor(($postcount-1)/$postsperpage);
if (isset($_GET['lastpage'])) $pageno = $lastpage;
$firstonpage = $pageno*$postsperpage;

// get user's last login time

if (isset($_COOKIE['$cookie_uname']) && isset($_COOKIE['$cookie_token'])) {
   $query = mysqli_prepare($dbh,"SELECT UNIX_TIMESTAMP(prevlogin) AS llstamp
                           FROM users WHERE uname = ? AND logintoken = ?") or die(mysqli_error($dbh));
   mysqli_stmt_bind_param($query,'ss',$_COOKIE['$cookie_uname'],$_COOKIE['$cookie_token']);
   mysqli_stmt_execute($query) or die(mysqli_error($dbh));
   mysqli_stmt_bind_result($query,$lastlogin);
   if (!mysqli_stmt_fetch($query)) $lastlogin=0;
   mysqli_stmt_close($query);
} else $lastlogin=0;

echo "<p>";
if ($pageno > 0) echo "<a href=\"$my_path?showthread=".$_GET['showthread']."&amp;showpage=".($pageno-1)."\">Previous Page</a>";
if ($pageno > 0 && $pageno < floor(($postcount-1)/$postsperpage)) echo " | ";
if ($pageno < floor(($postcount-1)/$postsperpage)) echo "<a href=\"$my_path?showthread=".$_GET[showthread]."&amp;showpage=".($pageno+1)."\">Next Page</a>";
echo "</p>";

$query = mysqli_prepare($dbh,"
 SELECT board.subject AS subject,
        board.message AS message,
        board.idx AS idx,
        UNIX_TIMESTAMP(board.postedtime) AS postedtime,
        UNIX_TIMESTAMP(board.lasttime) AS lasttime,
        users.uname AS uname,
        users.idx AS uidx
 FROM board, users
 WHERE board.author = users.idx AND (board.replyto = ? OR board.idx = ?)
 ORDER BY postedtime ASC
 LIMIT ?,?
") or die (mysqli_error($dbh));

mysqli_stmt_bind_param($query,'iiii',$_GET['showthread'],$_GET['showthread'],$firstonpage,$postsperpage);
mysqli_stmt_execute($query) or die(mysqli_error($dbh));
mysqli_stmt_bind_result($query,$subject,$message,$message_id,$postedtime,$updatetime,$uname,$uid);

echo "<dl class=\"postlist\">\n";

$firstpost=1;

while (mysqli_stmt_fetch($query)) {
   echo "<dt><span class=\"subject\">";
   if ($lastlogin > 0 && $updatetime > $lastlogin) echo "* ";
   echo "$subject</span> by <span class=\"name\"><a href=\"$my_path?userinfo=$uid\">$uname</a></span> at ".date($timefmt,$postedtime)." on ".date($datefmt,$postedtime)."</dt>\n";
   echo "<dd>$message";
   if (time()-$postedtime < $editexpire &&
((!isset($_COOKIE[$cookie_uname]) || !isset($_COOKIE[$cookie_token])) || !strcmp($uname,$_COOKIE['$cookie_uname'])))
      echo "<br><small><a href=\"$my_path?editpost=$message_id\">[edit]</a></small>";
   echo "</dd>\n";
}

mysqli_stmt_close($query);

echo "</dl>\n";

echo "<p>";
if ($pageno > 0) echo "<a href=\"$my_path?showthread=".$_GET['showthread']."&amp;showpage=".($pageno-1)."\">Previous Page</a>";
if ($pageno > 0 && $pageno < floor(($postcount-1)/$postsperpage)) echo " | ";
if ($pageno < floor(($postcount-1)/$postsperpage)) echo "<a href=\"$my_path?showthread=".$_GET['showthread']."&amp;showpage=".($pageno+1)."\">Next Page</a>";
echo "<br>";
echo "Go to Page ";
for ($i = 0; $i <= $lastpage; $i++)
{
   if ($pageno != $i)
      echo "<a href=\"$my_path?showthread=".$_GET['showthread']."&amp;showpage=$i\">";
   echo "$i";
   if ($pageno != $i)
      echo "</a>";
    echo " ";
}
echo "<br>";

echo "<a href=\"$my_path?searchmode&amp;threadid=".$_GET['showthread']."\">Search this thread</a>";
echo "<br>";

echo "<a href=\"$my_path?showthreads\">Show all threads</a><br><br>";
echo "Reply to this thread:<br>";
NewPostForm($_GET['showthread']);
echo "</p>";

} else if (isset($_GET['addpost'])) {

// **** Add a post

pageheader();

$uid = authenticate($dbh,$_POST['author'],$_POST['pass']);

if ($_POST['inresponseto']=="0" && (!isset($_POST['subject']) || $_POST['subject']=="" || ctype_space($_POST['subject']))) die("Cannot start thread with empty subject");

$dbh_write = dblogin_write();

$query = mysqli_prepare($dbh_write,"INSERT INTO board VALUES(NULL,NOW(),NOW(),?,?,?,?,?)") or die ("post error: ".mysqli_error($dbh_write));

mysqli_stmt_bind_param($query,'iisss',$uid,$_POST['inresponseto'],htmlspecialchars($_POST['subject'],ENT_QUOTES),preg_replace($tags_search,$tags_replace,htmlspecialchars($_POST['message'],ENT_QUOTES)),$_SERVER['REMOTE_ADDR']);
mysqli_stmt_execute($query) or die(mysqli_error($dbh_write));
mysqli_stmt_close($query);

echo "Added.<br>";

// update thread last updated time
if ($_POST['inresponseto'] != 0) {
   update_post_time($dbh_write,$_POST['inresponseto']);
   echo "<a href=\"$my_path?showthread=$_POST[inresponseto]&amp;lastpage\">Return to thread</a><br>";
}   echo "<a href=\"$my_path?showthreads\">Return to forum</a>";

mysqli_close($dbh_write);

} else if (isset($_GET['editpost'])) {

// **** Display post edit form

pageheader();

$query = mysqli_prepare($dbh,"
 SELECT subject,message
 FROM board
 WHERE idx = ?") or die(mysqli_error($dbh));

mysqli_stmt_bind_param($query,'i',$_GET['editpost']);
mysqli_stmt_execute($query) or die(mysqli_error($dbh));
mysqli_stmt_bind_result($query,$subject,$message);
if (!mysqli_stmt_fetch($query)) die("no such post ".mysqli_error($dbh));

mysqli_stmt_close($query);

EditPostForm($_GET['editpost'],preg_replace($tags_decode_search,$tags_decode_replace,$message),$subject);

} else if (isset($_GET['editpost2'])) {

// **** Commit an edited post

pageheader();

$posttoedit = intval($_POST['posttoupdate']);

// look up what post this reponds to and when it was first posted

$query = mysqli_prepare($dbh,"SELECT replyto, UNIX_TIMESTAMP(postedtime) FROM board WHERE idx = ? LIMIT 1") or die(mysqli_error($dbh));
mysqli_stmt_bind_param($query,'i',$_POST['posttoupdate']);
mysqli_stmt_execute($query) or die(mysqli_error($dbh));
mysqli_stmt_bind_result($query,$inresponseto,$postedtime);
mysqli_stmt_fetch($query) or die("couldn't find first post in thread".mysqli_error($dbh));
mysqli_stmt_close($query);

if (!in_array($posttoedit, $editable_whitelist) && time()-$postedtime >= $editexpire) die("edit time for this post has expired ($editexpire seconds)");

$uid = authenticate($dbh,$_POST['author'],$_POST['pass']);
   
if ($inresponseto=="0" && (!isset($_POST['subject']) || $_POST['subject']=="" || ctype_space($_POST['subject']))) die("Thread cannot have empty subject");

$dbh_write = dblogin_write();

$query = mysqli_prepare($dbh_write,"
 UPDATE board
 SET subject = ?,
     message = ?,
     ip = ?,
     lasttime = NOW()
 WHERE idx = ? AND author = ? 
 LIMIT 1
") or die (mysqli_error($dbh_write));

$newmessage = preg_replace($tags_search,$tags_replace,htmlspecialchars($_POST['message'],ENT_QUOTES))."<br><br><small><i>edited ".date($timefmt." ".$datefmt)."</i></small>";
mysqli_stmt_bind_param($query,'sssii',htmlspecialchars($_POST['subject'],ENT_QUOTES),$newmessage,$_SERVER[REMOTE_ADDR],$_POST['posttoupdate'],$uid);
mysqli_stmt_execute($query) or die(mysqli_error($dbh_write));

if (mysqli_stmt_affected_rows($query) != 1) die("no such post by you");

mysqli_stmt_close($query);

echo "Updated.<br>";

if ($inresponseto != 0) {
   update_post_time($dbh_write,$inresponseto);
   echo "<a href=\"$my_path?showthread=$inresponseto&amp;lastpage\">Return to thread</a><br>";

   echo "<a href=\"$my_path?showthreads\">Return to forum</a>";
}

mysqli_close($dbh_write);

} else if (isset($_GET['searchmode'])) {
// **** Searching (largely ripped off from Josh W)

pageheader();
 
$post_action = "$my_path?searchmode";
if(isset($_GET['threadid'])) {
    $post_action .= "&amp;threadid=${_GET['threadid']}";
}
echo "<form action=\"$post_action\" method=\"POST\">\n";

echo "<table border=0>\n";
error_reporting(E_ALL ^ E_NOTICE);
echo "<tr><td colspan=\"4\"><b>Search for:&nbsp;</b><input type=\"text\" name=\"query\" value=\"$_POST[query]\" size=50></td></tr>\n";
if(isset($_GET['threadid'])) {
    echo "<tr><td colspan=\"4\"><b>In thread:&nbsp;</b><input type=\"text\" name=\"threadid\" value=\"$_GET[threadid]\" size=10></td></tr>\n";
}

echo "<tr><td>Search where?</td>";

echo "<td><input type=\"radio\" name=\"searchwhere\" value=\"message\"";
if(!isset($_POST['searchwhere']) || $_POST['searchwhere'] == "message")
	echo " checked";
echo "> In Message Body</td>\n";

echo "<td><input type=\"radio\" value=\"subject\" name=\"searchwhere\"";
if($_POST['searchwhere'] == "subject")
	echo " checked";
echo "> In Subject</td>\n";

echo "<td><input type=\"radio\" value=\"uname\" name=\"searchwhere\"";
if($_POST['searchwhere'] == "uname")
	echo " checked";
echo "> by Author</td>\n";
echo "</tr>\n";

echo "<tr><td>Search how?</td>";
echo "<td><input type=\"radio\" name=\"searchhow\" value=\"phrase\"";
if($_POST['searchhow'] == "phrase")
	echo " checked";
echo "> Phrase</td>\n";

echo "<td><input type=\"radio\" value=\"anywords\" name=\"searchhow\"";
if(!isset($_POST['searchhow']) || $_POST['searchhow'] == "anywords")
	echo " checked";
echo "> Any Words</td>\n";

echo "<td><input type=\"radio\" value=\"allwords\" name=\"searchhow\"";
if($_POST['searchhow'] == "allwords")
	echo " checked";
echo "> All Words</td>\n";

echo "</tr>\n";
echo "<tr><td><input type=\"submit\" value=\"Search\"></td><td></td><td></td><td><input type=\"reset\" value=\"Reset Form\"></td>\n";
echo "</form></p>\n";
echo "</table>\n";

if(isset($_POST['query'])) {

	if(isset($_POST['page']))
		$lowr = ($_POST['page']-1)*$postsperpage;
	else
		$lowr = 0;
	$highr = $lowr + $postsperpage;

	switch ($_POST['searchwhere']) {
	case "message":
		$field = "message";
		break;
	case "subject":
		$field = "subject";
		break;
	case "uname":
		$field = "users.uname";
		break;
	default:
		$field = "message";
	}	

	switch ($_POST['searchhow']) {
	case "phrase":
		$query_sql = "$field LIKE ?";
		$query = "%" . mysqli_real_escape_string($dbh,htmlspecialchars($_POST['query'],ENT_QUOTES)) . "%";
		break;
	case "anywords":
		$word_count = substr_count($_POST['query']," ")+1;
		$query_sql = "$field LIKE ?" . str_repeat(" OR $field LIKE ?",$word_count-1);
		$query = explode(" ",
		"%" . str_replace(" ","% %",htmlspecialchars($_POST['query'],ENT_QUOTES)) . "%"
		);
		break;
	case "allwords":
		$word_count = substr_count($_POST['query']," ")+1;
		$query_sql = "$field LIKE ?" . str_repeat(" AND $field LIKE ?",$word_count-1);
		$query = explode(" ",
		"%" . str_replace(" ","% %",htmlspecialchars($_POST['query'],ENT_QUOTES)) . "%"
		);
		break;
	default:
		$query = "'%mothballs%'";
		break;
	}

    $full_query = "SELECT
	    board.idx AS idx
		FROM board";
        
    if ($field == "users.uname")
    {
        $full_query .= ",users";
    }

    $full_query .= "
		WHERE ( $query_sql )";
        
    if ($field == "users.uname")
    {
        $full_query .= " AND board.author = users.idx";
    }

    if (isset($_GET['threadid']))
    {
        $threadid = intval($_GET['threadid']);
        $full_query .= " AND ( board.idx = $threadid OR board.replyto = $threadid )";
    }

    $full_query .= "
		ORDER BY board.postedtime ASC
        ";

    $stmt=mysqli_prepare($dbh,$full_query) or die (mysqli_error($dbh));

	if ($_POST['searchhow'] == "anywords" || $_POST['searchhow'] == "allwords")
	{
		$i = 2;
		$query2[0] = $stmt;
		$query2[1] = str_repeat('s',$word_count);
		foreach ($query as $q)
		{
			$query2[$i] = &$query[$i-2];
			$i++;
		}
		call_user_func_array(mysqli_stmt_bind_param,$query2);
	} else {
		mysqli_stmt_bind_param($stmt,'s',$query) or die (mysqli_error($dbh));
	}

	mysqli_stmt_execute($stmt) or die(mysqli_error($dbh));
	mysqli_stmt_bind_result($stmt,$message_idx) or die(mysqli_error($dbh));
	mysqli_stmt_store_result($stmt);
	$nresults = mysqli_stmt_num_rows($stmt);

	for($i = 0; $i < $nresults && $i < $lowr; $i++)
		if (!mysqli_stmt_fetch($stmt)) die(mysqli_error($dbh));

	for($i = $lowr; $i < $nresults && $i < $highr && mysqli_stmt_fetch($stmt); $i++)
	{
		$indexes[$i] = $message_idx;
	}

	mysqli_stmt_close($stmt);

	$npage = (int)(($highr+$postsperpage) / $postsperpage);
	$ppage = $npage - 2;

	$highr3 = $highr;
	if($highr > $nresults) $highr = $nresults;
	$test2 = preg_replace(array("[ ]"),array("+") ,$_POST['query']);

	echo "<hr>\n";

	PrevNext($post_action,$ppage,$npage,$highr3,$nresults);

	echo "<p align=\"left\">Showing " . ($lowr + 1) . " - $highr out of $nresults posts<br><br><dl class=\"postlist\">\n";

	for($i = $lowr; $i < $highr; $i++) {
	    $post_index = $indexes[$i];

	    // fetch the thread index
	    $stmt = mysqli_prepare($dbh,"SELECT replyto FROM board WHERE idx = ?") or die (mysqli_error($dbh));
	    mysqli_stmt_bind_param($stmt,'i',$post_index) or die(mysqli_error($dbh));
	    mysqli_stmt_execute($stmt) or die(mysqli_error($dbh));
	    mysqli_stmt_bind_result($stmt,$thread_index) or die(mysqli_error($dbh));
	    mysqli_stmt_fetch($stmt) or die ("error fetching a post".mysqli_error($dbh));
	    mysqli_stmt_close($stmt);

	    if ($thread_index != 0)
	    {
		    // fetch the thread title and index
		    $stmt = mysqli_prepare($dbh,"SELECT subject FROM board WHERE idx = ?") or die(mysqli_error($dbh));
		    mysqli_stmt_bind_param($stmt,'i',$thread_index) or die(mysqli_error($dbh));
		    mysqli_stmt_execute($stmt) or die (mysqli_error($dbh));
            mysqli_stmt_store_result($stmt) or die (mysqli_error($dbh));

            if (mysqli_stmt_num_rows($stmt) != 0)
            {
                mysqli_stmt_bind_result($stmt,$thread_subject) or die(mysqli_error($dbh));
                mysqli_stmt_fetch($stmt) or die ("error fetching thread title and index $thread_index ".mysqli_error($dbh));
            }
            else
            {
                $thread_index = 0;
            }
            mysqli_stmt_close($stmt);
        }

        if ($thread_index != 0)
        {

		    // count how many posts come before this one
		    $stmt = mysqli_prepare($dbh,"SELECT COUNT(*) FROM board WHERE (replyto = ? OR idx = ?) AND board.idx < ?") or die(mysqli_error($dbh));
		    mysqli_stmt_bind_param($stmt,'iii',$thread_index,$thread_index,$post_index) or die (mysqli_error($dbh));
		    mysqli_stmt_execute($stmt) or die (mysqli_error($dbh));
		    mysqli_stmt_bind_result($stmt,$postcount) or die(mysqli_error($dbh));
		    mysqli_stmt_fetch($stmt) or die(mysqli_error($dbh));
		    mysqli_stmt_close($stmt);
		
		    $pageno = floor(($postcount) / $postsperpage);
	    }

	    // retrieve details of this post
	    $stmt = mysqli_prepare($dbh,"SELECT board.subject, board.message, UNIX_TIMESTAMP(board.postedtime), users.idx, users.uname
	    	FROM board, users
		WHERE board.idx = ? AND users.idx = board.author") or die(mysqli_error($dbh));
	    mysqli_stmt_bind_param($stmt,'i',$post_index);
	    mysqli_stmt_execute($stmt) or die (mysqli_error($dbh));
	    mysqli_stmt_bind_result($stmt,$subject,$message,$postedtime,$uid,$uname);
	    mysqli_stmt_fetch($stmt) or die ("error getting post details ".mysqli_error($dbh));
	    mysqli_stmt_close($stmt);

	    if($thread_index != 0)
		    echo "<span class=\"subject\">Thread: <a href=\"$my_path?showthread=$thread_index&amp;showpage=$pageno\">$thread_subject</a></span><br>";
	    else
		    echo "<span class=\"subject\">Thread: <a href=\"$my_path?showthread=$post_index\">$subject</a></span><br>";

	    echo "<dt><span class=\"subject\">$subject</span> by <span class=\"name\"><a href=\"$my_path?userinfo=$uid\">$uname</a></span> at " .
	    	date($timefmt,$postedtime)." on ".date($datefmt,$postedtime);
	    echo "<dd>$message</dd>";
	}

	echo "</p></dl>";

	PrevNext($post_action,$ppage,$npage,$highr3,$nresults);

} // end if query

} else if (isset($_GET['rss'])) {

// **** RSS feed */
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n<rss version=\"2.0\"><channel>\n";

echo "<title>Mini Forum</title>\n<link>$full_path</link>\n";
echo "<description>10 most recently active threads in the Mini Boards</description>\n";

$stmt=mysqli_prepare($dbh,
		"SELECT board.idx AS threadid,board.subject,board.lasttime,
		(SELECT COUNT(*) FROM board WHERE replyto = threadid) AS replycount
		FROM board,users
		WHERE board.author = users.idx AND board.replyto = '0'
		ORDER BY lasttime DESC
		LIMIT 10") or die (mysqli_error($dbh));
mysqli_stmt_execute($stmt) or die(mysqli_error($dbh));
mysqli_stmt_bind_result($stmt, $index, $subject, $lasttime, $replies);

while (mysqli_stmt_fetch($stmt)) {
	echo "<item>";
	echo "<title>$subject ($replies replies)</title>\n";
	echo "<description>$subject ($replies replies)</description>\n";
	echo "<link>$full_path?showthread=$index&amp;lastpage</link>\n";
	echo "</item>\n";
}

mysqli_stmt_close($stmt);

echo "</channel></rss>";

mysqli_close($dbh);

exit; // avoid outputting the html footer

} else {

// **** Display Threads

pageheader();
error_reporting(E_ALL ^ E_NOTICE);
$firstonpage=$_GET['showpage']*$threadsperpage;
// get user's last login time

if (isset($_COOKIE[$cookie_uname]) && isset($_COOKIE[$cookie_token])) {
   // login time
   $query = mysqli_prepare($dbh,"SELECT UNIX_TIMESTAMP(prevlogin) AS llstamp FROM users WHERE uname = ? AND logintoken = ?");
   mysqli_stmt_bind_param($query,'ss',$_COOKIE[$cookie_uname],$_COOKIE[$cookie_token]);

   mysqli_stmt_execute($query) or die(mysqli_error($dbh));
   mysqli_stmt_bind_result($query,$lastlogin);
   if (!mysqli_stmt_fetch($query)) $lastlogin=0;
   mysqli_stmt_close($query);
}
else
{
   $lastlogin=0;
}

// thread list
$query = mysqli_prepare($dbh,"SELECT board.idx AS threadid,
        board.subject AS subject,
        UNIX_TIMESTAMP(board.postedtime) AS postedtime,
	  UNIX_TIMESTAMP(board.lasttime) AS lasttime,
        users.uname AS uname,
        users.idx AS idx,
	(SELECT COUNT(*) FROM board WHERE idx = threadid) + (SELECT COUNT(*) FROM board WHERE replyto = threadid) AS postcount
 FROM board, users
 WHERE board.author = users.idx AND board.replyto = '0'
 ORDER BY lasttime DESC
 LIMIT ?, ?") or die(mysqli_error($dbh));
 mysqli_stmt_bind_param($query,'ii',$firstonpage,$threadsperpage) or die(mysqli_error($dbh));

// get thread list
mysqli_execute($query) or die(mysqli_error($dbh));
mysqli_stmt_bind_result($query,$threadid,$subject,$postedtime,$thread_lasttime,$thread_uname,$thread_uid,$postcount);

echo "<table border=\"1\" class=\"threadlist\">
<tr><th colspan=5>Threadses</th></tr>
<tr><td>Subject</td><td>Started by</td><td>Started at</td><td>Last Update</td><td>Posts</td></tr>";

while (mysqli_stmt_fetch($query)) {
echo "<tr>\n<td><span class=\"subject\">";
if ($lastlogin > 0 && $thread_lasttime > $lastlogin) echo "* ";
echo "<a href=\"$my_path?showthread=$threadid\">$subject</a>";
if ($postcount > $postsperpage) echo " <small><a href=\"$my_path?showthread=$threadid&amp;lastpage\">(last page)</a></small>";
echo "</span></td>
<td><span class=\"name\"><a href=\"$my_path?userinfo=$thread_uid\">$thread_uname</a></span></td>
<td>".date($timefmt." ".$datefmt,$postedtime)."</td>
<td>".date($timefmt." ".$datefmt,$thread_lasttime)."</td>
<td>$postcount</td>
</tr>\n";
}

echo "</table>\n";

mysqli_stmt_close($query);

$query = mysqli_prepare($dbh,"SELECT COUNT(*) FROM board WHERE replyto = '0'") or die(mysqli_error($dbh));
mysqli_execute($query) or die (mysqli_error($dbh));
mysqli_stmt_bind_result($query,$count) or die(mysqli_error($dbh));
mysqli_stmt_fetch($query) or die("error fetching thread list ".mysqli_error($dbh));
mysqli_stmt_close($query) or die(mysqli_error($dbh));

echo "<p>";
error_reporting(E_ALL ^ E_NOTICE);
if ($_GET['showpage'] > 0) echo "<a href=\"$my_path?showthreads&amp;showpage=".($_GET['showpage']-1)."\">Previous Page</a>";
if ($_GET['showpage'] > 0 && $showpage < floor(($count-1)/$threadsperpage)) echo " | ";
if ($_GET['showpage'] < floor(($count-1)/$threadsperpage)) echo "<a href=\"$my_path?showthreads&amp;showpage=".($_GET['showpage']+1)."\">Next Page</a>";
echo "<br><br><a href=\"$my_path?searchmode\">Search</a> | <a href=\"$my_path?adduser\">Create an account</a> | <a href=\"$my_path?chpass\">Change Password</a> | ";
if (!isset($_COOKIE[$cookie_uname]) || !isset($_COOKIE[$cookie_token]))
   echo "<a href=\"$my_path?login\">Log In</a>";
else
   echo "<a href=\"$my_path?logout\">Log Out</a>";
echo "<br><a href=\"$my_path?userlist\">User List</a>";
echo "</p>\n";
echo "<p>Create a new thread:<br>";
NewPostForm(0);
echo "</p>\n";

}

mysqli_close($dbh);

?>

<center><a href="<?php echo $my_path; ?>">Forum Index</a><br>
<small><a href="http://glitchery.jp">Glitch</a> & <a href="http://hcs64.com">HCS64</a>
<!--<br><small><a href="http://www.hcs64.com/mboard/forum.phps">forum source</a>-->
<br>
Generated in <?php echo round(microtime(true)-$script_start_time,4) . "s;" ?>
</small></center>

</body></html>
