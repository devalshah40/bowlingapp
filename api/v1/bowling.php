<?php
$app->post('/create-game', function () use ($app) {
  $r = json_decode($app->request->getBody());
//  echo "<pre>";
//  print_r($r);
//  exit;

  $db = new DbHandler();

  $gameDetails = $db->getOneRecord("select id from game order by id ");
  $gameName = 'Game ' . ($gameDetails['id'] + 1);
  $table_name = "game";
  $details = [
    'game_name' => $gameName
  ];
  $column_names = array('game_name');
  $gameID = $db->insertIntoTable($details, $column_names, $table_name);
  if ($gameID != NULL) {
    $table_name = "bowlers";
    $column_names = array('game_id', 'bowler_name');

    foreach ($r->players as $player) {
      $details = [
        'game_id' => $gameID,
        'bowler_name' => $player
      ];

      $db->insertIntoTable($details, $column_names, $table_name);
    }

    $response["status"] = "success";
    $response["message"] = $gameName . " is created successfully";
    $response["gameID"] = $gameID;

    echoResponse(200, $response);
  } else {
    $response["status"] = "error";
    $response["message"] = "Failed to create Game. Please try again";
    echoResponse(201, $response);
  }
});

$app->post('/login', function () use ($app) {
  $r = json_decode($app->request->getBody());
  verifyRequiredParams(array('email', 'password'), $r->customer);
  $response = array();
  $db = new DbHandler();
  $password = $r->customer->password;
  $email = $r->customer->email;
  $user = $db->getOneRecord("select uid,name,password,email,created from customers_auth where phone='$email' or email='$email'");
  if ($user != NULL) {
    if (passwordHash::check_password($user['password'], $password)) {
      $response['status'] = "success";
      $response['message'] = 'Logged in successfully.';
      $response['name'] = $user['name'];
      $response['uid'] = $user['uid'];
      $response['email'] = $user['email'];
      $response['createdAt'] = $user['created'];
      if (!isset($_SESSION)) {
        session_start();
      }
      $_SESSION['uid'] = $user['uid'];
      $_SESSION['email'] = $email;
      $_SESSION['name'] = $user['name'];
    } else {
      $response['status'] = "error";
      $response['message'] = 'Login failed. Incorrect credentials';
    }
  } else {
    $response['status'] = "error";
    $response['message'] = 'No such user is registered';
  }
  echoResponse(200, $response);
});
$app->post('/signUp', function () use ($app) {
  $response = array();
  $r = json_decode($app->request->getBody());
  verifyRequiredParams(array('email', 'name', 'password'), $r->customer);
  $db = new DbHandler();
  $phone = $r->customer->phone;
  $name = $r->customer->name;
  $email = $r->customer->email;
  $address = $r->customer->address;
  $password = $r->customer->password;
  $isUserExists = $db->getOneRecord("select 1 from customers_auth where phone='$phone' or email='$email'");
  if (!$isUserExists) {
    $r->customer->password = passwordHash::hash($password);
    $tabble_name = "customers_auth";
    $column_names = array('phone', 'name', 'email', 'password', 'city', 'address');
    $result = $db->insertIntoTable($r->customer, $column_names, $tabble_name);
    if ($result != NULL) {
      $response["status"] = "success";
      $response["message"] = "User account created successfully";
      $response["uid"] = $result;
      if (!isset($_SESSION)) {
        session_start();
      }
      $_SESSION['uid'] = $response["uid"];
      $_SESSION['phone'] = $phone;
      $_SESSION['name'] = $name;
      $_SESSION['email'] = $email;
      echoResponse(200, $response);
    } else {
      $response["status"] = "error";
      $response["message"] = "Failed to create customer. Please try again";
      echoResponse(201, $response);
    }
  } else {
    $response["status"] = "error";
    $response["message"] = "An user with the provided phone or email exists!";
    echoResponse(201, $response);
  }
});
$app->get('/logout', function () {
  $db = new DbHandler();
  $session = $db->destroySession();
  $response["status"] = "info";
  $response["message"] = "Logged out successfully";
  echoResponse(200, $response);
});
?>