<?php

namespace Armezit\GetCandy\PurchaseLimit\Rules;

use Armezit\GetCandy\PurchaseLimit\Exceptions\ProductQuantityLimitException;
use Armezit\GetCandy\PurchaseLimit\Exceptions\ProductTotalLimitException;
use GetCandy\Models\Cart;
use GetCandy\Models\CartLine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * check general purchase limit on a specific product
 */
class ProductLimit implements CartRuleInterface
{

    private function getProductIds(Cart $cart): Collection
    {
        return $cart->lines()
                    ->with('purchasable')
                    ->get()
                    ->pluck('purchasable')
                    ->flatten()
                    ->pluck('product_id')
                    ->unique();
    }

    /**
     * @inheritDoc
     */
    public function query(Builder $builder, Cart $cart): Builder
    {
        return $builder
            ->where([
                'product_variant_id' => 0,
                'customer_id' => 0,
                'customer_group_id' => 0,
            ])
            ->whereIn('product_id', $this->getProductIds($cart));
    }

    /**
     * @inheritDoc
     */
    public function filter(Collection $purchaseLimits, Cart $cart): Collection
    {
        return $purchaseLimits->filter(function ($limit) use ($cart) {
            return
                $this->getProductIds($cart)->contains($limit->product_id) &&
                $limit->product_variant_id === 0 &&
                $limit->customer_id === 0 &&
                $limit->customer_group_id === 0;
        });
    }

    /**
     * @inheritDoc
     */
    public function execute(Collection $purchaseLimits, Cart $cart): void
    {
        $limits = $this->filter($purchaseLimits, $cart);

        foreach ($limits as $limit) {

            // calculate total quantity of product
            $quantity = $cart->lines->filter(function (CartLine $cartLine) use ($limit) {
                return $cartLine->purchasable->product_id === $limit->product_id;
            })->sum('quantity');

            if ($limit->max_quantity !== null && $limit->max_quantity < $quantity) {
                throw new ProductQuantityLimitException;
            }

            // calculate total price of product
            $subTotal = $cart->lines->filter(function (CartLine $cartLine) use ($limit) {
                return $cartLine->purchasable->product_id === $limit->product_id;
            })->sum('subTotal.value');

            if ($limit->max_total !== null && $limit->max_total < $subTotal) {
                throw new ProductTotalLimitException;
            }
        }
    }
}
