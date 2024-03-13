<?php
error_reporting(0);

include 'simple_html_dom.php';

class ImageDownloader {
    private $targetDirectory;
    private $context;

    public function __construct($targetDirectory) {
        $this->targetDirectory = $targetDirectory;

        $opts = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
            "http" => [
                "user_agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36 OPR/107.0.0.0",
            ],
        ];
        $this->context = stream_context_create($opts);
    }

    public function downloadImages($url): int
    {
        $dir = $this->targetDirectory;
        $html = file_get_html($url, false, $this->context);
        if (!$html) {
            echo "������ ��� �������� ��������: $url<br>";
            return 0;
        }

        $imageUrls = [];

        foreach($html->find('img') as $element) {
            $imageUrl = $element->src;
            $imageUrls[] = $imageUrl;
        }

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $count = 0;
        foreach ($imageUrls as $imageUrl) {
            $imageContent = file_get_contents($imageUrl);
            $imageName = basename($imageUrl);
            $savePath = $dir . DIRECTORY_SEPARATOR . $imageName;
            file_put_contents($savePath, $imageContent);
            $count++;
        }

        return $count;
    }
}

$temp = new ImageDownloader("C:/task1");
echo "Downloaded {$temp->downloadImages("https://wallpapercave.com/categories/anime")} ";