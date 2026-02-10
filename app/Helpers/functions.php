<?php
declare(strict_types=1);
/**
 * Global helper functions
 */

use App\Helpers\LanguageSwitcher;
use App\Helpers\LocalizationHelper;

/**
 * Global translation helper function
 * 
 * Shorthand for LocalizationHelper::translate()
 * Can be used in views: <?= __('key') ?> or <?php echo __('key', ['param' => 'value']); ?>
 * 
 * @param string $key Translation key
 * @param array $params Parameters to replace in translation
 * @return string Translated string
 */
if (!function_exists('__')) {
    function __(string $key, array $params = []): string {
        return LocalizationHelper::translate($key, $params);
    }
}
