<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Button')]
final class Button
{
    public string $title;
    public string $type="primary";
    public string $icon="";
    public string $onclick="";
    public string $submit="false";
    public string $classes="";
}
