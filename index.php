<?php
  require_once __DIR__ . '/vendor/autoload.php';

// make sure we have a session
session_start();

/* Create our Application instance (reaplce this with your appId and secret). */
$fb = new Facebook\Facebook([
  'app_id' => '816040145173459',
  'app_secret' => '393177944a8691f1574e8b5f4513a476',
  'default_graph_version' => 'v2.5',
  'pseudo_random_string_generator' => 'openssl',
]);

/* App logic here */

// Get Page Tab Helper
$page_helper = $fb->getPageTabHelper();
try {
    $access_token = $page_helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    // when Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // when validation fails or other local issues
    echo 'fb SDK returned an error: ' . $e->getMessage();
    exit;
}

if (! isset($access_token)) {
    echo 'Either the user has not authorized your app yet or you are trying to access this FB app outside of the FB page.';
    exit;
} else {
    $fb->setDefaultAccessToken($access_token);
}

// Getting User ID
try {
    $res = $fb->get('/me');
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

$user = $res->getGraphUser();

// Login or logout url will be needed depending on current user state.
$login_helper = $fb->getRedirectLoginHelper();

if ($user) {
  $logoutUrl = $login_helper->getLogoutUrl();
} else {
  $loginUrl = $login_helper->getLoginUrl($_SERVER['SERVER_PROTOCOL'] . '://' . $_SERVER['SERVER_NAME'] . '/login_cb.php');
}


?>
<!doctype html>
<html>
  <head>
    <title>php-sdk</title>
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
    <link rel="stylesheet" href="styles.css" type="text/css">
  </head>
  <body>
    <script>
        window.fbAsyncInit = function() {
            FB.init({
                appId      : '816040145173459',
                xfbml      : true,
                version    : 'v2.5'
            });

            // ADD ADDITIONAL FACEBOOK CODE HERE
            function onLogin(response) {
                if (response.status == 'connected') {
                    FB.api('/me?fields=first_name', function(data) {
                        var welcomeBlock = document.getElementById('fb-welcome');
                        welcomeBlock.innerHTML = 'Hello, ' + data.first_name + '!';
                    });
                }
            }

            FB.getLoginStatus(function(response) {
                // Check login status on load, and if the user is
                // already logged in, go directly to the welcome message.
                if (response.status == 'connected') {
                    onLogin(response);
                } else {
                    // Otherwise, show Login dialog first.
                    FB.login(function(response) {
                        onLogin(response);
                    }, {scope: 'user_friends, email'});
                }
            });
        };

        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>
    <form class="searchform">
        <input type="text" placeholder="Search Archives...">
        <button type="submit">Search</button>
    </form>
    
    <div class="contents">
        <h2>Search results</h2>
        <div id="search_results" style="width:100%;overflow:auto;height:500px;">
            <?php 
            for ($i = 1; $i <= 10; $i++) {
                echo "<b>Post #{$i} Title</b> / <i>Author: {$user->getName()}</i><p>\n";
                echo "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a tortor purus. Aliquam non turpis velit. Pellentesque euismod odio id consequat mattis. Nunc a dictum metus. Nulla tincidunt nibh est, nec pellentesque purus tincidunt in. Aliquam at pulvinar risus, vitae molestie nibh. Nunc bibendum varius enim, ornare gravida turpis consectetur mattis. Ut sit amet augue id augue scelerisque finibus non in dolor. Pellentesque lobortis lorem lorem, eget aliquet nunc lobortis et. Etiam fermentum cursus sagittis. Maecenas at finibus mauris, sit amet tincidunt velit. Ut ultricies ante ex, condimentum volutpat magna facilisis a.\n";
                echo "<hr />\n\n"; 
            } 
            ?>
        </div>
    </div>
  </body>
</html>
