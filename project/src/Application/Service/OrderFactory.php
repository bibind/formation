<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Dto\OrderInput;
use App\Domain\Order;
use App\Domain\OrderLine;
use App\Domain\Product;
use App\Domain\User;

final class OrderFactory
{
    /**
     * @param array<string, Product> $productsById
     */
    public function fromInput(OrderInput $input, User $buyer, array $productsById): Order
    {
        $order = new Order(id: $this->generateId(), buyer: $buyer);
        foreach ($input->lines as $line) {
            $product = $productsById[$line['productId']];
            $order->addLine(
                new OrderLine(
                    id: $this->generateId(),
                    order: $order,
                    product: $product,
                    quantity: $line['quantity'],
                    unitPrice: $product->price(),
                )
            );
        }
        return $order;
    }

    private function generateId(): string
    {
        return bin2hex(random_bytes(16));
    }
}
