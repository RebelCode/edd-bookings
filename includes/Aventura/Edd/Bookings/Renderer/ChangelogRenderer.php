<?php

namespace Aventura\Edd\Bookings\Renderer;

/**
 * Renders a changelog file.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class ChangelogRenderer extends RendererAbstract
{
    
    /**
     * {@inheritdoc}
     */
    public function render(array $data = array())
    {
        $filename = $this->getObject();
        $raw = file_get_contents($filename);
        $sections = explode("\n\n", $raw);
        ob_start();
        ?>
        <div class="changelog">
            <?php
            foreach ($sections as $section) :
                $lines = explode("\n", $section);
                ?>
                <h4><?php echo array_shift($lines); ?></h4>
                <ul>
                    <?php
                    foreach ($lines as $line) :
                        if (strpos($line, '- ') !== 0) {
                            continue;
                        }
                        $trimmedLine = trim(substr($line, 2));
                        $formattedLine = static::formatChangelogEntry($trimmedLine);
                        ?>
                        <li><?php echo $formattedLine; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php
            endforeach; ?>
        </div>
        <?php
        return apply_filters('edd_bk_changelog_output', ob_get_clean());
    }
    
    /**
     * Formats a changelog entry.
     * 
     * @param string $line The changelog entry line string.
     * @return string The formatted line.
     */
    public static function formatChangelogEntry($line)
    {
        $monospaced = preg_replace('/`([^`]*)`/', '<code>$1</code>', $line);
        $bolded = preg_replace('/\*\*([^\*]*)\*\*/', '<strong>$1</strong>', $monospaced);
        $italic = preg_replace('/\*([^\*]*)\*/', '<em>$1</em>', $bolded);
        return $italic;
    }

}
