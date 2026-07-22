<?php
/**
 * AI categorization service.
 *
 * Returns: array('category_id' => int|null, 'tags' => string)
 *
 * Default mode 'heuristic' uses zero-cost keyword scoring.
 */
class AiCategorizer
{
    // Class constant: category slug => list of keywords.
    const RULES = array(
        'instruments'  => array('guitar', 'piano', 'keyboard', 'drum', 'bass', 'violin', 'amp', 'strat'),
        'dj-equipment' => array('dj', 'controller', 'turntable', 'mixer', 'cdj', 'djm', 'rekordbox', 'serato'),
        'studio-gear'  => array('monitor', 'interface', 'microphone', 'mic', 'preamp', 'studio', 'mixing', 'mastering'),
        'vinyl'        => array('vinyl', 'lp', 'record', '180g', 'pressing', '45 rpm'),
    );

    public static function categorize($name, $description = '', $image = null)
    {
        return self::heuristic($name, $description);
    }

    private static function heuristic($name, $description)
    {
        $haystack = strtolower($name . ' ' . $description);
        $bestSlug = null;
        $bestScore = 0;
        $tags = array();

        foreach (self::RULES as $slug => $keywords) {
            $score = 0;
            foreach ($keywords as $kw) {
                if (strpos($haystack, $kw) !== false) {
                    $score++;
                    $tags[] = $kw;
                }
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestSlug  = $slug;
            }
        }

        // Deduplicate tags manually (avoid array_unique).
        $uniqueTags = array();
        foreach ($tags as $t) {
            if (!in_array($t, $uniqueTags)) {
                $uniqueTags[] = $t;
            }
        }

        $categoryId = null;
        if ($bestSlug) {
            $stmt = Database::connect()->prepare('SELECT id FROM categories WHERE slug = ?');
            $stmt->execute(array($bestSlug));
            $row = $stmt->fetch();
            if ($row) {
                $categoryId = (int)$row['id'];
            }
        }

        return array(
            'category_id' => $categoryId,
            'tags'        => implode(',', $uniqueTags),
        );
    }
}
