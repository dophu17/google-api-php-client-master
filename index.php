<?php
session_start();

//Google API PHP Library includes
require_once 'src/Google/autoload.php';
require_once 'src/Google/Client.php';
require_once 'src/Google/Auth/OAuth2.php';

// Fill CLIENT ID, CLIENT SECRET ID, REDIRECT URI from Google Developer Console
 $client_id = '735617090296-60prce4issddplr01e2m4mu846of1l55.apps.googleusercontent.com';
 $client_secret = 'uvq6DI9NKYVjij4SqhIG-X3i';
 $redirect_uri = 'http://localhost/dhtpfashion/public/plugin/google-api-php-client-master';
 $simple_api_key = 'AIzaSyAievRKKBd5voZ6kb2VgnSCdgK0itTJbl0';
 
//Create Client Request to access Google API
$client = new Google_Client();
$client->setApplicationName("PHP Google OAuth Login Example");
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->setDeveloperKey($simple_api_key);
$client->addScope("https://www.googleapis.com/auth/userinfo.email");

//Send Client Request
$objOAuthService = new Google_Service_Oauth2($client);

//Logout
if (isset($_REQUEST['logout'])) {
  unset($_SESSION['access_token']);
  $client->revokeToken();
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL)); //redirect user back to page
}

//Authenticate code from Google OAuth Flow
//Add Access Token to Session
if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

//Set Access Token to make Request
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
}

//Get User Data from Google Plus
//If New, Insert to Database
if ($client->getAccessToken()) {
  $userData = $objOAuthService->userinfo->get();
  $_SESSION['access_token'] = $client->getAccessToken();
} else {
  $authUrl = $client->createAuthUrl();
}

if (isset($authUrl)){
      echo '<a href="'.$authUrl.'">Login</a>';
}
else{
	echo '<pre>';
	  print_r($userData);
	  echo '</pre>';
	  echo '<br/>name:'.$userData['name'];
	  echo '<br/>email:'.$userData['email'];
	  echo '<pre>';
	  print_r($_SESSION['access_token']);
	  echo '</pre>';
	  echo '<br/><a href="?logout">Logout</a>';
}

require_once("index.php")
?>