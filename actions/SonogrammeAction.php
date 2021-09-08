<?php

namespace YesWiki\Contrib;

use YesWiki\Core\YesWikiAction;

if (!class_exists('attach')) {
  include('tools/attach/libs/attach.lib.php');
}

class SonogrammeAction extends YesWikiAction {
  public function formatArguments($args) {
    return ([
      'color' => $args['color'] ?: '',
      'file' => $args['file'] ?: '',
      'height' => 200,
    ]);
  }

  public function run () {
    $att = new \attach($this->wiki);
    $att->CheckParams();

    $fullFilename = $att->GetFullFilename();
    $attachmentExists = $this->attachmentExists($att);

    return $this->render('@contrib/sonogramme.twig', [
      'args' => $this->arguments,
      'audioplayer' => $attachmentExists
        ? $this->wiki->format('{{player url="'.$this->wiki->getBaseUrl().'/'.$fullFilename.'" type="audio"}}')
        : '',
      'fileExists' => $attachmentExists,
      'fullFilename' => $fullFilename,
    ]);
  }

  private function attachmentExists (\attach $att) {
    $fullFilename = $att->GetFullFilename();

    return (!file_exists($fullFilename) || ($fullFilename == '')) === false;
  }
}
