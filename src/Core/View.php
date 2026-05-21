<?php

declare(strict_types=1);

namespace App\Core;

use Smarty\Smarty;

final class View
{
    private Smarty $smarty;

    public function __construct(array $paths)
    {
        foreach ([$paths['compiled'], $paths['cache']] as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
        }

        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir($paths['templates']);
        $this->smarty->setCompileDir($paths['compiled']);
        $this->smarty->setCacheDir($paths['cache']);

        $this->registerModifiers();
    }

    private function registerModifiers(): void
    {
        $this->smarty->registerPlugin('modifier', 'rudate', static function (
            mixed $value,
            string $format = 'd.m.Y',
        ): string {
            $timestamp = is_numeric($value) ? (int) $value : (int) strtotime((string) $value);

            return $timestamp > 0 ? date($format, $timestamp) : (string) $value;
        });
    }

    public function render(string $template, array $data = []): string
    {
        $this->smarty->assign($data);

        return $this->smarty->fetch($template);
    }
}
