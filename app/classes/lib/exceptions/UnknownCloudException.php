<?php
class UnknownCloudException extends Exception{
	 public function __toString() {
        return "Unknown cloud passed";
    }
}