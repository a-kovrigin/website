<?php

namespace Drupal\druki_content\Sync\SourceContent;

use Drupal\Component\FrontMatter\Exception\FrontMatterParseException;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\druki_content\Parser\ContentSourceFileParser;
use Drupal\druki_content\Queue\ContentSyncQueueItemInterface;
use Drupal\druki_content\Queue\ContentSyncQueueProcessorInterface;

/**
 * Provides source content list queue processor.
 *
 * @todo Refactor to ContentSourceDocumentListQueueProcessor.
 */
final class SourceContentListQueueProcessor implements ContentSyncQueueProcessorInterface {

  /**
   * The state storage.
   */
  protected StateInterface $state;

  /**
   * The source content parser.
   */
  protected ContentSourceFileParser $parser;

  /**
   * The parsed source content loader.
   */
  protected ParsedSourceContentLoader $loader;

  /**
   * The logger channel.
   */
  protected LoggerChannelInterface $logger;

  /**
   * SourceContentListQueueProcessor constructor.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state storage.
   * @param \Drupal\druki_content\Parser\ContentSourceFileParser $parser
   *   The source content parser.
   * @param \Drupal\druki_content\Sync\SourceContent\ParsedSourceContentLoader $loader
   *   The parsed source content loader.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   The logger.
   */
  public function __construct(StateInterface $state, ContentSourceFileParser $parser, ParsedSourceContentLoader $loader, LoggerChannelInterface $logger) {
    $this->state = $state;
    $this->parser = $parser;
    $this->loader = $loader;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public function process(ContentSyncQueueItemInterface $item): void {
    $is_force_update = $this->state->get('druki_content.settings.force_update', FALSE);
    /** @var \Drupal\druki_content\Data\ContentSourceFileList $source_content_list */
    $source_content_list = $item->getPayload();
    foreach ($source_content_list as $source_content) {
      try {
        $parsed_source_content = $this->parser->parse($source_content);
      }
      catch (FrontMatterParseException $e) {
        $this->logger->warning(new TranslatableMarkup('File "@path" skipped. Error parsing front matter block. Text error: "@message"', [
          '@path' => $source_content->getRelativePathname(),
          '@message' => $e->getMessage(),
        ]));
        continue;
      }

      // Do nothing if it's invalid.
      // @todo Maybe it's better to return NULL during parse.
      if (!$parsed_source_content->getParsedSource()->valid()) {
        continue;
      }
      $this->loader->process($parsed_source_content, $is_force_update);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(ContentSyncQueueItemInterface $item): bool {
    return $item instanceof SourceContentListContentSyncQueueItem;
  }

}
