<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Link')]
final class Link
{
    public string $title;
    public string $type="default";
    public string $icon="";
    public string $href="";
    public string $target="";
}
