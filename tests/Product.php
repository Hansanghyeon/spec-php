<?php

declare(strict_types=1);

namespace Hansanghyeon\Spec;

final class Product
{
    public $isNew;
    public $name;
    public $price;
    public $color;

    public function __construct(array $data)
    {
        $this->isNew = $data['isNew'];
        $this->name = $data['name'];
        $this->price = $data['price'];
        $this->color = $data['color'];
    }
}
