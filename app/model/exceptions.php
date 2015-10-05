<?php
namespace Nexendrie\Model;

class AccessDeniedException extends \RuntimeException {
  
}

class AuthenticationNeededException extends AccessDeniedException {
  
}

class MissingPermissionsException extends AccessDeniedException {
  
}

class RecordNotFoundException extends \RuntimeException {
  
}
?>