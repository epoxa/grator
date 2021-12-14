<?php

namespace App\Model;

class OfferModel implements Offer
{

    public function __construct(
        private string $text,
        private Iterable $methods,
    )
    {
    }

    function getText(): string
    {
        return $this->text;
    }

    function getMethods(): Iterable
    {
        return $this->methods;
    }
}