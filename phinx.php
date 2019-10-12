<?php
declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

function getEnvironmentConfig(string $filename): array {
  $neon = file_get_contents($filename);
  if($neon === false) {
    throw new RuntimeException("File $filename does not exist or cannot be read.");
  }
  $neon = \Nette\Neon\Neon::decode($neon);

  $driver = $neon["dbal"]["driver"];
  switch($driver) {
    case "mysqli":
      $adapter = "mysql";
      break;
    case "pgsql":
    case "sqlsrv":
      $adapter = $driver;
      break;
    default:
      throw new RuntimeException("Unsupported database driver $driver.");
  }

  return [
    "adapter" => $adapter,
    "host" => $neon["dbal"]["host"],
    "name" => $neon["dbal"]["database"],
    "user" => $neon["dbal"]["username"],
    "pass" => $neon["dbal"]["password"],
    "charset" => "utf8",
    "collation" => "utf8_general_ci",
  ];
}

$config = [
  "paths" => [
    "migrations" => __DIR__ . "/migrations",
    "seeds" => __DIR__ . "/migrations",
  ],
  "environments" => [],
];
$productionConfigFile = __DIR__ . "/app/config/local.neon";
if(is_file($productionConfigFile) && is_readable($productionConfigFile)) {
  $config["environments"]["production"] = getEnvironmentConfig($productionConfigFile);
}
$testingConfigFile = __DIR__ . "/tests/local.neon";
if(is_file($testingConfigFile) && is_readable($testingConfigFile)) {
  $config["environments"]["testing"] = getEnvironmentConfig($testingConfigFile);
}
return $config;
?>