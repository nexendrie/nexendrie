<?php
declare(strict_types=1);

namespace Nexendrie\Database;

use Nextras\Dbal\Connection,
    Nextras\Dbal\Utils\FileImporter;

/**
 * BaseDatabaseImporter
 *
 * @author Jakub Konečný
 * @property string $folder
 * @property string[] $files
 * @property string $finalMessage
 */
final class DatabaseImporter {
  use \Nette\SmartObject;
  
  /** @var Connection */
  protected $connection;
  /** @var string */
  protected $folder;
  /** @var string */
  protected $extension;
  /** @var string[] File extensions for drivers */
  protected $driversExtensions = [
    "mysqli" => "mysql",
    "pgsql" => "pgsql",
  ];
  /** @var string[] */
  protected $files = [];
  /** @var string[] */
  protected $allowedFiles = ["structure", "data_basic", "data_test",];
  /** @var string */
  protected $finalMessage;
  
  public function __construct(Connection $connection, string $driver) {
    $this->connection = $connection;
    $this->setExtension($driver);
    if($this->extension === "pgsql") {
      $this->allowedFiles[] = "final";
    }
  }
  
  /**
   * @throws \OutOfRangeException
   */
  protected function setExtension(string $driver): void {
    if(!array_key_exists($driver, $this->driversExtensions)) {
      throw new \OutOfRangeException("Invalid driver $driver.");
    }
    $this->extension = $this->driversExtensions[$driver];
  }
  
  public function getFolder(): string {
    return $this->folder;
  }
  
  /**
   * @throws \RuntimeException
   */
  public function setFolder(string $folder): void {
    if(!is_dir($folder)) {
      throw new \RuntimeException("Folder $folder does not exist.");
    }
    $this->folder = $folder;
  }
  
  /**
   * @return string[]
   */
  public function getFiles(): array {
    return $this->files;
  }
  
  /**
   * @param string[] $files
   * @throws \OutOfRangeException
   */
  public function setFiles(array $files): void {
    $this->files = [];
    foreach($files as $file) {
      if(!in_array($file, $this->allowedFiles, true)) {
        throw new \OutOfRangeException("File type $file not recognized.");
      }
      $this->files[] = $file;
    }
  }
  
  public function useBasicData(): void {
    $files = ["structure", "data_basic",];
    if($this->extension === "pgsql") {
      $files[] = "final";
    }
    $this->setFiles($files);
  }
  
  public function useBasicAndTestData(): void {
    $files = ["structure", "data_basic", "data_test",];
    if($this->extension === "pgsql") {
      $files[] = "final";
    }
    $this->setFiles($files);
  }
  
  public function getFinalMessage(): string {
    return $this->finalMessage;
  }
  
  public function setFinalMessage(string $finalMessage): void {
    $this->finalMessage = $finalMessage;
  }
  
  /**
   * @throws \RuntimeException
   */
  public function run(): void {
    if(!isset($this->folder)) {
      throw new \RuntimeException("Folder for sql query files is not set.");
    }
    if(count($this->files) === 0) {
      throw new \RuntimeException("No files for import set.");
    }
    \Tracy\Debugger::timer("setup_db");
    echo "Setting up database ...\n\n";
    foreach($this->files as $file) {
      $path = "$file.$this->extension";
      if(!is_file("$this->folder/$path")) {
        throw new \RuntimeException("File $path not found in specified folder.");
      }
      echo "Executing file: $path ... ";
      \Tracy\Debugger::timer($file);
      FileImporter::executeFile($this->connection, "$this->folder/$path");
      $time = round(\Tracy\Debugger::timer($file), 2);
      echo "Done in $time second(s)\n";
    }
    if(isset($this->finalMessage)) {
      echo $this->finalMessage . "\n";
    }
    $time = round(\Tracy\Debugger::timer("setup_db"), 2);
    echo "\nTotal time: $time second(s)\n";
  }
}
?>