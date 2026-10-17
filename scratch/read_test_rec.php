<?php
$f = fopen('C:/Users/PC/.gemini/antigravity-ide/brain/ec534a66-faae-403a-aa87-d5a933f381b7/.system_generated/logs/transcript.jsonl', 'r');
while ($line = fgets($f)) {
    $json = json_decode($line, true);
    $idx = $json['step_index'] ?? 0;
    if ($idx >= 1 && $idx <= 25) {
        echo "Step $idx: " . ($json['type'] ?? '') . " | " . substr($json['content'] ?? '', 0, 150) . "\n";
        if (!empty($json['tool_calls'])) {
            foreach ($json['tool_calls'] as $tc) {
                echo "   Tool: " . $tc['name'] . " - " . json_encode($tc['args']) . "\n";
            }
        }
    }
}
