<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Modal')]
final class Modal
{
    public string $id;
    public string $title;
}
