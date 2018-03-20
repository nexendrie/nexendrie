<?php
declare(strict_types=1);

namespace Nexendrie\Chat\DI;

require __DIR__ . "/../../../bootstrap.php";

use Tester\Assert,
    Nexendrie\Chat\InvalidChatControlFactoryException,
    Nexendrie\Chat\InvalidMessageProcessorException,
    Nexendrie\Chat\InvalidDatabaseAdapterException;

final class ChatExtensionTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  public function testChats() {
    $config = [
      "chat" => [
        "chats" => [
          "abc" => "abc"
        ]
      ]
    ];
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidChatControlFactoryException::class);
    $config["chat"]["chats"]["abc"] = \stdClass::class;
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidChatControlFactoryException::class);
    $config["chat"]["chats"]["abc"] = \Countable::class;
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidChatControlFactoryException::class);
    $config["chat"]["chats"]["abc"] = \Nexendrie\Components\IAcademyControlFactory::class;
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidChatControlFactoryException::class);
  }
  
  public function testMessageProcessors() {
    $config = [
      "chat" => [
        "messageProcessors" => [
          "abc" => "abc"
        ]
      ]
    ];
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidMessageProcessorException::class);
    $config["chat"]["messageProcessors"]["abc"] = \stdClass::class;
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidMessageProcessorException::class);
  }
  
  public function testDatabaseAdapter() {
    $config = [
      "chat" => [
        "databaseAdapter" => "abc"
      ]
    ];
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidDatabaseAdapterException::class);
    $config["chat"]["databaseAdapter"] = \stdClass::class;
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidDatabaseAdapterException::class);
  }
}

$test = new ChatExtensionTest();
$test->run();
?>