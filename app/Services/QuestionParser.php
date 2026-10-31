<?php

class QuestionParser {
    /**
     * Parses a raw question_text into structured components:
     * - source_tag: extracted prefix like "Innovature Labs · Logic PYQ" (null if none)
     * - setup: setup context sentences (null if short / no split)
     * - setup_sentences: array of setup sentences for rendering
     * - prompt: the core question prompt (or full text if no split)
     * - full_text: clean question text without bracket prefix
     * - is_split: boolean
     */
    public static function parse(string $rawText): array {
        $sourceTag = null;
        $text = trim($rawText);

        // 1. Extract leading bracket prefix e.g. [Innovature Labs Reasoning Style Practice]
        if (preg_match('/^\[([^\]]+)\]\s*/u', $text, $matches)) {
            $rawTag = trim($matches[1]);
            // Format "Innovature Labs Reasoning Style Practice" -> "Innovature Labs · Reasoning Style Practice"
            if (preg_match('/^(Innovature\s+Labs)\s+(.*)$/i', $rawTag, $tagParts)) {
                $sourceTag = $tagParts[1] . ' · ' . $tagParts[2];
            } else {
                $sourceTag = $rawTag;
            }
            $text = trim(substr($text, strlen($matches[0])));
        }

        // Full clean question text is ALWAYS preserved completely without losing premise or puzzle rules
        return [
            'source_tag' => $sourceTag,
            'sourceTag' => $sourceTag,
            'setup' => null,
            'setup_sentences' => [],
            'setupSentences' => [],
            'prompt' => $text,
            'full_text' => $text,
            'fullText' => $text,
            'is_split' => false,
            'isSplit' => false
        ];
    }

    /**
     * Checks if a code snippet is trivial (e.g. only comments or repeats the question stem).
     */
    public static function isTrivialCodeSnippet(?string $code, ?string $questionText = null): bool {
        if ($code === null) return true;
        $trimmed = trim($code);
        if ($trimmed === '') return true;

        $lines = array_filter(array_map('trim', explode("\n", $trimmed)), function($l) { return $l !== ''; });
        if (empty($lines)) return true;

        // Check if every line is a comment
        $allComments = true;
        foreach ($lines as $line) {
            if (!preg_match('/^(\/\/|\/\*|\*|#|--)/', $line)) {
                $allComments = false;
                break;
            }
        }
        if ($allComments) return true;

        // Check if code snippet merely repeats question text
        if ($questionText !== null) {
            $cleanQ = strtolower(strip_tags($questionText));
            $cleanCode = strtolower($trimmed);
            if (strlen($cleanCode) > 10 && strpos($cleanQ, $cleanCode) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Safe markdown & inline code formatter:
     * Escapes HTML first to prevent XSS, then converts **bold**, *italic*, and `code`.
     */
    public static function formatInlineCode(?string $text): string {
        return self::renderMarkdown($text);
    }

    public static function renderMarkdown(?string $text): string {
        if ($text === null || $text === '') return '';

        $codeBlocks = [];
        // 1. Extract fenced code blocks: ```lang\ncode\n```
        $text = preg_replace_callback('/```([a-zA-Z0-9_\-+]*)\r?\n(.*?)```/s', function($matches) use (&$codeBlocks) {
            $lang = trim($matches[1]);
            $code = rtrim($matches[2], "\r\n");
            $escapedCode = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
            $langClass = $lang !== '' ? ' class="language-' . htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') . '"' : '';
            $html = '<pre class="overflow-x-auto my-2 p-2.5 rounded bg-surface border text-start" style="max-height: 350px; overflow-x: auto; white-space: pre;" tabindex="0"><code' . $langClass . '>' . $escapedCode . '</code></pre>';
            $token = '%%CODEBLOCK_' . count($codeBlocks) . '%%';
            $codeBlocks[$token] = $html;
            return $token;
        }, $text);

        // 2. Escape HTML for the non-code text
        $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        // 3. Convert `inline code`
        $rendered = preg_replace('/`([^`]+)`/', '<code class="inline-code">$1</code>', $escaped);

        // 4. Convert **bold**
        $rendered = preg_replace('/\*\*([^*]+)\*\*/', '<strong class="md-bold">$1</strong>', $rendered);

        // 5. Convert *italic*
        $rendered = preg_replace('/(?<!\*)\*([^*]+)\*(?!\*)/', '<em class="md-italic">$1</em>', $rendered);

        // 6. Restore code blocks
        foreach ($codeBlocks as $token => $blockHtml) {
            $rendered = str_replace($token, $blockHtml, $rendered);
        }

        return $rendered;
    }
}
