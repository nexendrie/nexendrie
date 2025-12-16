<?php
declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

function getEnvironmentConfig(string $filename): array
{
    $neon = file_get_contents($filename);
    if ($neon === false) {
        throw new RuntimeException("File $filename does not exist or cannot be read.");
    }
    $neon = \Nette\Neon\Neon::decode($neon);

    $driver = $neon["dbal"]["driver"];
    $adapter = match ($driver) {
        "mysqli" => "mysql",
        "pgsql", "sqlsrv" => $driver,
        default => throw new RuntimeException("Unsupported database driver $driver."),
    };

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

$configFiles = [
    "production" => __DIR__ . "/app/config/local.neon",
    "testing" => __DIR__ . "/tests/local.neon",
];
foreach ($configFiles as $environment => $filename) {
    if (is_file($filename) && is_readable($filename)) {
        $config["environments"][$environment] = getEnvironmentConfig($filename);
    }
}

return $config;
