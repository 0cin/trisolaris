<?php

function generate_hash($password, $salt) {
  return hash("sha256", $password.$salt);
}
$salt = '$2y$11$' . "fJt0\\qVwy<aGWdkC";


?>