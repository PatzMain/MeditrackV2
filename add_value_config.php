<?php
// Script to add 'value' => $data to all column configurations
$config_file = 'config/table_config.php';
$content = file_get_contents($config_file);

// Pattern to match column definitions and add 'value' => $data
$pattern = '/(\s+\'[^\']+\' => \[\s*\n\s*\'label\' => [^\n]+\n\s*\'type\' => [^\n]+\n\s*\'sortable\' => [^\n]+\n\s*\'searchable\' => [^\n]+\n\s*\'visible\' => [^\n]+\n\s*\'editable\' => [^\n]+(?:\n\s*\'[^\']+\' => [^\n]+)*\n\s*)(\])/';

$replacement = '$1\'value\' => $data$2';

$new_content = preg_replace($pattern, $replacement, $content);

// Write the updated content back to the file
file_put_contents($config_file, $new_content);

echo "Successfully added 'value' => \$data to all column configurations in $config_file\n";
?>
