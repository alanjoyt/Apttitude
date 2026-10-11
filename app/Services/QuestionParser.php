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

        // 1. Extract leading bracket prefix e.g. [Innovature Labs Logic PYQ]
        if (preg_match('/^\[([^\]]+)\]\s*/u', $text, $matches)) {
            $rawTag = trim($matches[1]);
            // Format "Innovature Labs Logic PYQ" -> "Innovature Labs · Logic PYQ"
            if (preg_match('/^(Innovature\s+Labs)\s+(.*)$/i', $rawTag, $tagParts)) {
                $sourceTag = $tagParts[1] . ' · ' . $tagParts[2];
            } else {
                $sourceTag = $rawTag;
            }
            $text = trim(substr($text, strlen($matches[0])));
        }

        // 2. Check length and sentence count
        $len = mb_strlen($text);

        // Temporarily protect abbreviations and decimals
        $protected = $text;
        $protected = preg_replace('/(\d+)\.(\d+)/', '$1__DOT__$2', $protected);
        $protected = preg_replace('/\b(e\.g|i\.e|vs|mr|ms|dr|fig|approx|no|p|q|r|s|t)\./i', '$1__ABBR__', $protected);

        // Split on punctuation followed by space and capital letter or quote or backtick
        $rawSentences = preg_split('/(?<=[.?!])\s+(?=[A-Z0-9"\'`])/u', $protected);
        $sentences = [];
        if (is_array($rawSentences)) {
            foreach ($rawSentences as $s) {
                $s = str_replace('__DOT__', '.', $s);
                $s = str_replace('__ABBR__', '.', $s);
                $s = trim($s);
                if ($s !== '') {
                    $sentences[] = $s;
                }
            }
        }

        $sentenceCount = count($sentences);

        // If longer than ~200 characters or >=3 sentences, split into setup and prompt
        if (($len > 200 || $sentenceCount >= 3) && $sentenceCount >= 2) {
            $lastSentence = $sentences[$sentenceCount - 1];
            $isPrompt = false;

            if (preg_match('/\?$/u', $lastSentence)) {
                $isPrompt = true;
            } elseif (preg_match('/^(Which|What|How|Find|Determine|Calculate|Identify|Select|Choose)\b/i', $lastSentence)) {
                $isPrompt = true;
            }

            if ($isPrompt) {
                $setupSentences = array_slice($sentences, 0, $sentenceCount - 1);
                return [
                    'source_tag' => $sourceTag,
                    'setup' => implode(" ", $setupSentences),
                    'setup_sentences' => $setupSentences,
                    'prompt' => $lastSentence,
                    'full_text' => $text,
                    'is_split' => true
                ];
            }
        }

        // Fallback: show text unchanged as one paragraph
        return [
            'source_tag' => $sourceTag,
            'setup' => null,
            'setup_sentences' => [],
            'prompt' => $text,
            'full_text' => $text,
            'is_split' => false
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
     * Safe inline code formatter: escapes HTML first, then converts `backticks` to <code>.
     */
    public static function formatInlineCode(?string $text): string {
        if ($text === null || $text === '') return '';
        $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        return preg_replace('/`([^`]+)`/', '<code class="inline-code">$1</code>', $escaped);
    }
}
