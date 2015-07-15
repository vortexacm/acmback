<?php
session_destroy();

exit(header("location:http://vortex.{$this->vconf['domain']}/#wellcome") );
$this->vForceStop("");
?>