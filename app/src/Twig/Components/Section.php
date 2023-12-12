<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Section')]
final class Section
{
    public string $id="";
    public string $title="";
    public bool $full=false;
}
