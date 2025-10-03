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
            'svgUsePath' => true,
            'svgUseFillAttributes' => true,
        ]);

        return (new QRCode($options))->render($content);
    }

    public function generateDataUri(string $content, int $scale = 6): string
    {
        $svg = $this->generateSvg($content, $scale);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
