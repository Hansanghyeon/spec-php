[Specification pattern - Wikipedia](https://en.wikipedia.org/wiki/Specification_pattern#TypeScript) 

## How to use

```sh
composer require hansanghyeon/spec
```

## Example

```php
<?php

class Product {
    public $isNew;
    public $qty;

    public function __construct($isNew, $qty) {
        $this->isNew = $isNew;
        $this->qty = $qty;
    }
}

// 새롭게 추가된 상품인지 확인하는 스펙
$isNewSpec = new Spec(function($candidate) {
    return $candidate->isNew === true;
});

// 상품의 갯수가 0개 이상인지 확인하는 스펙
$isQtyChangedSpec = new Spec(function($candidate) {
    return $candidate->qty > 0;
});

// 새롭게 추가된 상품이 아니고 기존 상품이면서 갯수가 변경되고 1개 이상일때
$isOriginalAndQtyChangedSpec = $isNewSpec->not()->and($isQtyChangedSpec);

$product = new Product(); // Product 객체를 생성합니다.

if ($isNewSpec->isSatisfiedBy($product)) {
    return 'A';
}
if ($isOriginalAndQtyChangedSpec->isSatisfiedBy($product)) {
    return 'B';
}
```
