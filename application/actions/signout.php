<?php
/* @var $this toolbox */

try {
    $this->reset_session();

} catch (Exception $exception) {
}

$this->redirect();
