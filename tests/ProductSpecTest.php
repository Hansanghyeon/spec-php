<?php

declare(strict_types=1);

use Hansanghyeon\Spec\Spec;
use Hansanghyeon\Spec\Product;
use PHPUnit\Framework\TestCase;

function pipe(...$functions)
{
    return function ($data) use ($functions) {
        return array_reduce($functions, function ($carry, $function) {
            return $function($carry);
        }, $data);
    };
}

class ProductSpec
{
    public static function getSpecs(): array
    {
        // 새롭게 추가된 상품인지 확인하는 스펙
        $isNewSpec = new Spec(function ($candidate) {
            return $candidate->isNew === true;
        });

        $isHighPriceSpec = new Spec(function ($candidate) {
            return $candidate->price >= 200_000;
        });

        // 새롭게 추가된 상품이 아니고 기존 상품이면서 상품의 가격이 200,000원 이상인지 확인하는 스펙
        $isOriginalAndHighPriceSpec = $isNewSpec->not()->and($isHighPriceSpec);

        return [
            'isNewSpec' => $isNewSpec,
            'isHighPriceSpec' => $isHighPriceSpec,
            'isOriginalAndHighPriceSpec' => $isOriginalAndHighPriceSpec,
        ];
    }
}

final class ProductSpecTest extends TestCase
{
    private $products;
    private $specs;

    protected function setUp(): void
    {
        parent::setUp();

        // 스펙 객체들을 스태틱 메서드를 통해 가져옴
        $this->specs = ProductSpec::getSpecs();

        $this->products = array(
            new Product([
                'isNew' => true,
                'name' => '피카츄',
                'price' => 100_000,
                'color' => 'black'
            ]),
            new Product([
                'isNew' => false,
                'name' => '라이츄',
                'price' => 150_000,
                'color' => 'red'
            ]),
            new Product([
                'isNew' => false,
                'name' => '파이리',
                'price' => 200_000,
                'color' => 'white'
            ]),
            new Product([
                'isNew' => true,
                'name' => '꼬북이',
                'price' => 250_000,
                'color' => 'black'
            ]),
        );
    }

    public function testIsNewSpecFiltering()
    {
        $isNewSpec = $this->specs['isNewSpec'];

        $filteredProducts = pipe(
            function ($products) use ($isNewSpec) {
                return array_filter($products, function ($product) use ($isNewSpec) {
                    return $isNewSpec->isSatisfiedBy($product);
                });
            },
            'array_values'
        )($this->products);

        $this->assertCount(2, $filteredProducts);
        // 피카츄와 꼬북이만이 isNew 스펙을 만족해야 합니다.
        $this->assertEquals('피카츄', $filteredProducts[0]->name);
        // $this->assertEquals('꼬북이', $filteredProducts[1]->name);
    }

    public function testIsHighPriceSpecFiltering()
    {
        $isHighPriceSpec = $this->specs['isHighPriceSpec'];

        $filteredProducts = pipe(
            function ($products) use ($isHighPriceSpec) {
                return array_filter($products, function ($product) use ($isHighPriceSpec) {
                    return $isHighPriceSpec->isSatisfiedBy($product);
                });
            },
            'array_values'
        )($this->products);

        $this->assertCount(2, $filteredProducts);
        // 파이리와 꼬북이만이 가격이 200,000원 이상을 만족해야 합니다.
        $this->assertEquals('파이리', $filteredProducts[0]->name);
        $this->assertEquals('꼬북이', $filteredProducts[1]->name);
    }

    public function testIsOriginalAndHighPriceSpecFiltering()
    {
        $isOriginalAndHighPriceSpec = $this->specs['isOriginalAndHighPriceSpec'];

        $filteredProducts = pipe(
            function ($products) use ($isOriginalAndHighPriceSpec) {
                return array_filter($products, function ($product) use ($isOriginalAndHighPriceSpec) {
                    return $isOriginalAndHighPriceSpec->isSatisfiedBy($product);
                });
            },
            'array_values'
        )($this->products);

        $this->assertCount(1, $filteredProducts);
        // 라이츄만이 새로운 상품이 아니고 가격이 200,000원 이상을 만족해야 합니다.
        $this->assertEquals('파이리', $filteredProducts[0]->name);
    }
}
