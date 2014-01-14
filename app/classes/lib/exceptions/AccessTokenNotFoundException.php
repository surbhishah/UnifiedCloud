<?php
class AccessTokenNotFoundException extends Exception {
	 public function __toString() {
        return "Access token not found";
    }

}