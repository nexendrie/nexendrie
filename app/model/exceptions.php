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

class ImprisonedException extends AccessDeniedException {
  
}

class InsufficientFundsException extends AccessDeniedException {
  
}

class NameInUseException extends \RuntimeException {
  
}

class InsufficientLevelException extends AccessDeniedException {
  
}

class CannotPromoteMemberException extends AccessDeniedException {
  
}

class CannotDemoteMemberException extends AccessDeniedException {
  
}

class CannotKickMemberException extends AccessDeniedException {
  
}
?>