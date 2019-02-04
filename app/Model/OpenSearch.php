<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Application\LinkGenerator;

final class OpenSearch {
  use \Nette\SmartObject;

  /** @var SettingsRepository */
  protected $sr;
  /** @var LinkGenerator */
  protected $lg;

  public function __construct(SettingsRepository $sr, LinkGenerator $lg) {
    $this->sr = $sr;
    $this->lg = $lg;
  }

  public function createDescription(string $shortName, string $longName, string $description, string $tags, string $searchType): string {
    /** @var \SimpleXMLElement $xml */
    $xml = simplexml_load_file(__DIR__ . "/openSearchDescription.xml");
    $xml->ShortName[0][0] .= $shortName;
    $versionSuffix = $this->sr->settings["site"]["versionSuffix"];
    $xml->LongName[0][0] .= ($versionSuffix !== "") ? $versionSuffix . " " : "" . $longName;
    $xml->Tags[0][0] .= $tags;
    $xml->Description[0][0] = $description;
    $url = $this->lg->link("Front:Search:default", [
      "text" => "searchTerms", "type" => $searchType, "do" => "siteSearchForm-submit",
    ]);
    $xml->Url["template"] = str_replace("text=searchTerms", "text={searchTerms}", $url);
    return (string) $xml->asXML();
  }
}
?>