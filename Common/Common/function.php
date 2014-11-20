<?php
function pwdHash($password, $type = 'md5') {
	return hash ( $type, $password );
}
?>