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

// This call will always work since we are fetching public data.
$naitik = $facebook->api('/naitik');

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

    <!-- <h3>Session</h3> -->
    <?php if ($me): ?>
    <pre><?php //print_r($session); ?></pre>

    <h3>You</h3>
    <img src="https://graph.facebook.com/<?php echo $uid; ?>/picture">
    <?php //echo $me['name']; ?>
<!--
    <p>your news feed?</p>
    <pre><?php //print_r($newsfeed); ?></pre>

    <h3>Your User Object</h3>
    <pre><?php print_r($me); ?></pre>
    
-->
	<?php 
		echo "<h3>Your friends, in order of most mutual friends.</h3>";
		//get your friends
		$friendnames = $friends['data'];
		//we're going to store the results in a matrix aka a 2d array
		$matrix = array();
		$sums = array();
		//keys are uids, values are arrays / friendbool
		foreach($friendnames as $xcoord) {
			//get the user id and throw it in the matrix
			$friend1 = $xcoord['id'];
			$matrix[$friend1] = array();
			$friends1 = $matrix[$friend1];
			foreach($friendnames as $ycoord){
				$friend2 = $ycoord['id'];
				$param = array(
					'method' => 'friends.arefriends',
					'uids1' => $friend1,
					'uids2' => $friend2,
					'callback' => '');
				$mutuals = array_values($facebook->api($param));
				$friendbool = $mutuals[0][are_friends];
				$matrix[$friend1][friend2]=$friendbool;
				$sums[$friend1] += $friendbool;
			}
		}
		//reverse sort so most friends at the top
		arsort($sums);
		foreach($sums as $key => $value){
			$names = $facebook->api("/$key");
			$name = $names['name'];
			print_r("<p><img src='https://graph.facebook.com/$key/picture'> $name : $value mutual friends.</p>");
		}
		
	?>	
	
	
<!--
    <h3>Some more stuff that I can pull out of the graph api, yay!</h3>
    <h5>your wall?</h5>
    <pre><?php //print_r($wall); ?></pre>
    <hr />
    <h5>your statuses?</h5>
    <pre><?php //print_r($statuses); ?></pre>
    <hr />
    <h5>your inbox?</h5>
    <pre><?php //print_r($inbox); ?></pre>
-->
  
  <?php else: ?>
  <p><strong><em>You are not Connected.</em></strong></p>
  <p>Please sign in above.</p>
  <?php endif ?>
  </body>
</html>