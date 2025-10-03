<?php

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrCodeGenerator
{
    public function generateSvg(string $content, int $scale = 6): string
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel' => QRCode::ECC_L,
            'scale' => $scale,
            'svgUseFillAttributes' => false,
        ]);

        $result = (new QRCode($options))->render($content);

        if (str_starts_with($result, 'data:image/svg+xml;base64,')) {
            $base64 = substr($result, strlen('data:image/svg+xml;base64,'));
            $decoded = base64_decode($base64, true);

            return $decoded !== false ? $decoded : $result;
        }

        return $result;
    }

    public function generateDataUri(string $content, int $scale = 6): string
    {
        $svg = $this->generateSvg($content, $scale);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
