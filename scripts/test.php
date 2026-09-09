<?php
// Isolate persistent paths so these checks never touch a server's saved theme.
$directory = sys_get_temp_dir() . '/theme-studio-test-' . bin2hex(random_bytes(8));
mkdir($directory);
$source = file_get_contents(__DIR__ . '/../source/theme.studio/usr/local/emhttp/plugins/theme.studio/include/theme.php');
$source = preg_replace_callback("/const (THEME_STUDIO_\w+) = '[^']+';/", function ($match) use ($directory) {
    return 'const ' . $match[1] . ' = ' . var_export($directory . '/' . $match[1], true) . ';';
}, $source);
eval(substr($source, 5));
set_error_handler(function ($severity, $message) { throw new RuntimeException($message); });
function check($condition, $message) { if (!$condition) throw new RuntimeException($message); }
try {
    $theme = theme_studio_validate(['name' => str_repeat('é', 60), 'radius' => 999, 'accent' => ['bad'], 'surface' => new stdClass()]);
    check(preg_match('//u', $theme['name']) === 1, 'Name must remain valid UTF-8');
    check(strlen($theme['name']) === 96, 'Name must be limited to 48 characters');
    check($theme['radius'] === 24, 'Radius must be clamped');
    check($theme['accent'] === strtoupper(theme_studio_defaults()['accent']), 'Invalid color must fall back');
    check(theme_studio_validate(['name' => "\xff"] )['name'] === theme_studio_defaults()['name'], 'Invalid UTF-8 must fall back');
    check(theme_studio_save($theme), 'Save must succeed');
    check(theme_studio_load() === $theme, 'Saved theme must round trip');
    check(file_get_contents(THEME_STUDIO_LIVE_CSS) === file_get_contents(THEME_STUDIO_PERSISTENT_CSS), 'CSS copies must match');
    check(theme_studio_save(['enabled' => false]), 'Disabling must succeed');
    check(strpos(file_get_contents(THEME_STUDIO_LIVE_CSS), '{') === false, 'Disabled CSS must not contain rules');
    check(theme_studio_validate(['animationSpeed'=>9,'animationTimingVersion'=>1])['animationSpeed'] === 45, 'Legacy speed migration');
    echo "Passed PHP validation, UTF-8, persistence, disabled themes, and migration checks.\n";
} finally {
    restore_error_handler();
    foreach (glob($directory . '/*') as $file) unlink($file);
    rmdir($directory);
}
