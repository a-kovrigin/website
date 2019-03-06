<?php

namespace Drupal\druki_parser\CommonMark\Block\Parser;

use Drupal\druki_parser\CommonMark\Block\Element\MetaInformationElement;
use League\CommonMark\Block\Parser\AbstractBlockParser;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

/**
 * Class MetaInformationParser
 *
 * @package Drupal\druki_parser\CommonMark\Block\Parser
 */
class MetaInformationParser extends AbstractBlockParser {

  /**
   * {@inheritdoc}
   */
  public function parse(ContextInterface $context, Cursor $cursor): bool {
    if ($cursor->isIndented()) {
      return FALSE;
    }

    $meta_information = $cursor->match("/^\-{3}meta/");
    if (!$meta_information) {
      return FALSE;
    }

    $context->addBlock(new MetaInformationElement());

    return TRUE;
  }

}
