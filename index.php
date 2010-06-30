<?php

require './facebook-php-sdk/src/facebook.php';

// Create our Application instance.
$facebook = new Facebook(array(
  'appId'  => '119230204757007',
  'secret' => 'c72de154162a24bb11e6ebeee10857f1',
  'cookie' => true,
));

// We may or may not have this data based on a $_GET or $_COOKIE based session.
//
// If we get a session here, it means we found a correctly signed session using
// the Application Secret only Facebook and the Application know. We dont know
// if it is still valid until we make an API call using the session. A session
// can become invalid if it has already expired (should not be getting the
// session back in this case) or if the user logged out of Facebook.
$session = $facebook->getSession();

$me = null;
// Session based API call.
if ($session) {
  try {
    $uid = $facebook->getUser();
    $me = $facebook->api('/me');
    //$newsfeed = $facebook->api('/me/home');
    //$wall = $facebook->api('/me/feed'); 
    //$tagged = $facebook->api('/me/tagged');
    //$posts = $facebook->api('/me/posts');
    //$picture = $facebook->api('/me/picture');
    $friends = $facebook->api('/me/friends');
    //$activities = $facebook->api('/me/activities');
    //$interests = $facebook->api('/me/interests');
    //$music = $facebook->api('/me/music');
    //$books = $facebook->api('/me/books');
    //$movies = $facebook->api('/me/movies');
    //$television = $facebook->api('/me/television');
    //$likes = $facebook->api('/me/likes');
    //$photos = $facebook->api('/me/photos');
    //$albums = $facebook->api('/me/albums');
    //$videos = $facebook->api('/me/videos');
    //$groups = $facebook->api('/me/groups');
    //$statuses = $facebook->api('/me/statuses');
    //$links = $facebook->api('/me/links');
    //$notes = $facebook->api('/me/notes');
    //$events = $facebook->api('/me/events');
    //$inbox = $facebook->api('/me/inbox');
    //$outbox = $facebook->api('/me/outbox');
    //$updates = $facebook->api('/me/updates');
  } catch (FacebookApiException $e) {
    error_log($e);
  }
}

// login or logout url will be needed depending on current user state.
if ($me) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl();
}


?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>Constructing Tie Strength on Facebook</title>
    <style>
      body {
        font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
      }
      h1 a {
        text-decoration: none;
        color: #3b5998;
      }
      h1 a:hover {
        text-decoration: underline;
      }
    </style>
  </head>
  <body>
    <!--
      We use the JS SDK to provide a richer user experience. For more info,
      look here: http://github.com/facebook/connect-js
    -->
    <div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId   : '<?php echo $facebook->getAppId(); ?>',
          session : <?php echo json_encode($session); ?>, // don't refetch the session when PHP already has it
          status  : true, // check login status
          cookie  : true, // enable cookies to allow the server to access the session
          xfbml   : true // parse XFBML
        });

        // whenever the user logs in, we refresh the page
        FB.Event.subscribe('auth.login', function() {
          window.location.reload();
        });
      };

      (function() {
        var e = document.createElement('script');
        e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
        e.async = true;
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>


    <h1><a href="index.php">Constructing Tie Strength on Facebook</a></h1>

    <?php if ($me): ?>
    <a href="<?php echo $logoutUrl; ?>">
      <img src="http://static.ak.fbcdn.net/rsrc.php/z2Y31/hash/cxrz4k7j.gif">
    </a>
    <?php else: ?>
    <!-- <div>
      Using JavaScript &amp; XFBML: <fb:login-button></fb:login-button>
    </div>-->
    <div>
      Please log in here:
      <a href="<?php echo $loginUrl; ?>">
        <img src="http://static.ak.fbcdn.net/rsrc.php/zB6N8/hash/4li2k73z.gif">
      </a>
    </div>
    <?php endif ?>
<!-- ==================================================================================================================== -->
    <?php if ($me): ?>

    <h3>You</h3>
    <img src="https://graph.facebook.com/<?php echo $uid; ?>/picture">

	<?php 
		// our friends' names
		$friendnames = $friends['data'];
		// make an array for all this
		$matrix = array();
		// and for the sizes
		$sizes = array();
		// iterate over all that crap
		foreach ($friendnames as $crap){
			// make an entry in the array for them
			$name = $crap['id']; 
			$param = array(
					'method' => 'friends.getMutualFriends',
					'target_uid' => $name,
					'callback' => '',
					'source_uid' => '');
			$matrix[$name] = $facebook->api($param);
			$sizes[$name] = count($matrix[$name]);
		}

		// i guess now we want to sort it by count()
		arsort($sizes);
		// and now we print it all!
		echo "<h3>Your friends, in order of most mutual friends.</h3>";
		foreach ($sizes as $key => $value) {
			// get their real name
			$names = $facebook->api("/$key");
			$name = $names['name'];
			// and their friends
			$friends = $matrix[$key];
			// print their photo and name in bold
			print_r("<p><img src='https://graph.facebook.com/$key/picture'> <b>$name :</b> $value mutual friends.<br/>");
			// print all of the mutual friends' names
			//foreach ($friends as $uid){
			//	$fnames = $facebook->api("/$uid");
			//	$fname = $fnames['name'];
			//	echo "$fname, ";
			//}
			echo "\n";
		}	
	?>	
 
  <?php else: ?>
  <p><strong><em>You are not Connected.</em></strong></p>
  <p>Please sign in above.</p>
  <?php endif ?>
  </body>
</html>
