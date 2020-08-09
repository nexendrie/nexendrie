<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Application\LinkGenerator;

final class OpenSearch {
  use \Nette\SmartObject;

  protected SettingsRepository $sr;
  protected LinkGenerator $lg;

  public function __construct(SettingsRepository $sr, LinkGenerator $lg) {
    $this->sr = $sr;
    $this->lg = $lg;
  }

  public function createDescription(string $shortName, string $longName, string $description, string $tags, string $searchType): string {
    /** @var \SimpleXMLElement $xml */
    $xml = simplexml_load_file(__DIR__ . "/openSearchDescription.xml");
    $xml->ShortName .= $shortName;
    $versionSuffix = $this->sr->settings["site"]["versionSuffix"];
    $xml->LongName .= ($versionSuffix !== "") ? $versionSuffix . " " : "" . $longName;
    $xml->Tags .= $tags;
    $xml->Description = $description;
    $url = $this->lg->link("Front:Search:default", [
      "text" => "searchTerms", "type" => $searchType, "do" => "siteSearchForm-submit",
    ]);
    $xml->Url["template"] = str_replace("text=searchTerms", "text={searchTerms}", $url);
    return (string) $xml->asXML();
  }
}
?>