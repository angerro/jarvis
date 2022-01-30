<?php

namespace Jarvis\Vendor\Output;

class Browser extends AbstractOutput
{
    const COLORS = [
        'error'   => '#dc3545',
        'warning' => '#ffc107',
        'info'    => '#17a2b8',
        'success' => '#28a745'
    ];

    public function write(string $str, string $color = 'info')
    {
        $string = '<pre style="color:' . self::COLORS[$color] . '">';
        $string .= htmlspecialchars($str);
        $string .= '</pre>';
        echo $string;
    }
}