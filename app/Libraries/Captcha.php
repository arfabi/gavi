<?php

namespace App\Libraries;

class Captcha
{
    private int $length   = 5;
    private int $width    = 150;
    private int $height   = 50;
    private string $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public function generate(): string
    {
        $code = '';
        for ($i = 0; $i < $this->length; $i++) {
            $code .= $this->chars[random_int(0, strlen($this->chars) - 1)];
        }

        session()->set('captcha_code', strtoupper($code));

        return $this->renderImage($code);
    }

    public function verify(string $input): bool
    {
        $session = session();
        $stored  = $session->get('captcha_code');
        $session->remove('captcha_code');

        if (empty($stored) || empty($input)) {
            return false;
        }

        return strtoupper(trim($input)) === strtoupper($stored);
    }

    private function renderImage(string $code): string
    {
        $image = imagecreatetruecolor($this->width, $this->height);

        $bg    = imagecolorallocate($image, 240, 248, 255);
        $noise = imagecolorallocate($image, 200, 210, 220);
        $text  = imagecolorallocate($image, 15, 50, 100);

        imagefilledrectangle($image, 0, 0, $this->width, $this->height, $bg);

        // noise lines
        for ($i = 0; $i < 6; $i++) {
            imageline(
                $image,
                random_int(0, $this->width),
                random_int(0, $this->height),
                random_int(0, $this->width),
                random_int(0, $this->height),
                $noise
            );
        }

        // noise dots
        for ($i = 0; $i < 60; $i++) {
            imagesetpixel($image, random_int(0, $this->width), random_int(0, $this->height), $noise);
        }

        // draw each character with slight random offset
        $x = 12;
        for ($i = 0; $i < strlen($code); $i++) {
            $charColor = imagecolorallocate(
                $image,
                random_int(0, 80),
                random_int(0, 80),
                random_int(80, 180)
            );
            $y = random_int(10, 25);
            imagechar($image, 5, $x, $y, $code[$i], $charColor);
            $x += random_int(22, 27);
        }

        ob_start();
        imagepng($image);
        $imgData = ob_get_clean();
        imagedestroy($image);

        return '<img src="data:image/png;base64,' . base64_encode($imgData) . '" alt="CAPTCHA" id="captcha-img" style="cursor:pointer;" title="Klik untuk refresh">';
    }
}
