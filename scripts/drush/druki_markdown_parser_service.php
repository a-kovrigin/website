<?php

/**
 * @file
 * Testing code for druki parser service.
 */

/** @var \Drupal\druki_parser\Service\DrukiMarkdownParser $druki_parser */
$druki_parser = \Drupal::service('druki_parser.markdown');

$markdown_content = <<<'Markdown'
---meta
id: code-of-conduct
title: Нормы поведения
---

"quotes"

[[installation]]

[[faq|FAQ]]

Проверка внутренних ссылок внутри [[test|текста]].

## Title
Markdown;

$result_html = $druki_parser->parse($markdown_content);

dump($result_html);