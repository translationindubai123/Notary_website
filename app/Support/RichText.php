<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use RuntimeException;

class RichText
{
    private const ALLOWED_TAGS = ['a', 'b', 'br', 'div', 'em', 'i', 'li', 'ol', 'p', 'span', 'strong', 'u', 'ul'];

    public static function sanitize(?string $content): string
    {
        if ($content === null || $content === '') {
            return '';
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previousErrorSetting = libxml_use_internal_errors(true);

        try {
            $loaded = $document->loadHTML(
                '<?xml encoding="UTF-8"><div id="rich-text-root">'.$content.'</div>',
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET
            );
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousErrorSetting);
        }

        if (! $loaded) {
            throw new RuntimeException('Unable to parse rich-text content.');
        }

        $root = $document->getElementById('rich-text-root');
        if (! $root) {
            throw new RuntimeException('Unable to find the rich-text content root.');
        }

        return self::sanitizeChildren($root);
    }

    private static function sanitizeChildren(DOMNode $node): string
    {
        $html = '';
        foreach (iterator_to_array($node->childNodes) as $child) {
            $html .= self::sanitizeNode($child);
        }

        return $html;
    }

    private static function sanitizeNode(DOMNode $node): string
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            return nl2br(htmlspecialchars($node->nodeValue ?? '', ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8'), false);
        }

        if (! $node instanceof DOMElement) {
            return '';
        }

        $tag = strtolower($node->tagName);
        if (in_array($tag, ['script', 'style', 'iframe', 'object', 'svg', 'math', 'template'], true)) {
            return '';
        }

        $children = self::sanitizeChildren($node);

        if ($tag === 'font') {
            $sizeClass = match ($node->getAttribute('size')) {
                '1', '2' => 'rich-text-size-small',
                '5', '6', '7' => 'rich-text-size-large',
                default => null,
            };

            return $sizeClass ? '<span class="'.$sizeClass.'">'.$children.'</span>' : $children;
        }

        if (! in_array($tag, self::ALLOWED_TAGS, true)) {
            return $children;
        }

        if ($tag === 'a') {
            $href = self::safeHref($node->getAttribute('href'));

            return $href
                ? '<a href="'.htmlspecialchars($href, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8').'">'.$children.'</a>'
                : $children;
        }

        if ($tag === 'span') {
            $class = $node->getAttribute('class');
            if (in_array($class, ['rich-text-size-small', 'rich-text-size-large'], true)) {
                return '<span class="'.$class.'">'.$children.'</span>';
            }

            return $children;
        }

        if ($tag === 'br') {
            return '<br>';
        }

        return '<'.$tag.'>'.$children.'</'.$tag.'>';
    }

    private static function safeHref(string $href): ?string
    {
        $href = trim($href);
        if ($href === '' || preg_match('/[\x00-\x20]/', $href)) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $href)) {
            $parts = parse_url($href);

            return is_array($parts) && isset($parts['host']) ? $href : null;
        }

        return preg_match('/^(mailto|tel):[^:\s]+$/i', $href) ? $href : null;
    }
}
