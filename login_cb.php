<?php
  require_once __DIR__ . '/vendor/autoload.php';
  
  // make sure we have a session
  session_start();
  
  # login callback page
  $fb = new Facebook\Facebook([
      'app_id' => '',
      'app_secret' => '',
      'default_graph_version' => '2.5',
  ]);
  
  $helper = $fb->getRedirectLoginHelper();
  try {
      $accessToken = $helper->getAccessToken();
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
  } catch(Facebook\Exceptions\FacebookSDKException $e) {
      // When validation fails or other local issues
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
  }
  
  if (isset($accessToken)) {
      // logged in!
      $_SESSION['facebook_access_token'] = (string) $accessToken;
      // redirect back
      header('Location: '.$_SERVER['SERVER_PROTOCOL'].'://'.$_SERVER['SERVER_NAME'].'/index.php');
      die();
  } else {
      echo 'User denied access to Facebook app';
      exit;
  }
?>