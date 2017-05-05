<?php
/**
 * Created by PhpStorm.
 * User: Rosen
 * Date: 27-Apr-17
 * Time: 14:55
 */

namespace MarketplaceBundle\Service;


use MarketplaceBundle\Entity\Product;

class PriceCalculator
{
    /** @var  PromotionManager */
    protected $manager;

    public function __construct(PromotionManager $manager) {
        $this->manager= $manager;
    }


    /**
     * @param Product $product
     *
     * @return float
     */
    public function calculate($product)
    {
        $category = $product->getCategory();
        $category_id = $category->getId();
        $promotionCat = 0;
        $promotion = $this->manager->getGeneralPromotion();
        if($this->manager->hasCategoryPromotion($category)){
            $promotionCat = $this->manager->getCategoryPromotion($category);
        }

        if(isset($this->category_promotions[$category_id])){
            $promotionCat = $this->category_promotions[$category_id];
        }

        if ($promotionCat >= $promotion){
            $promotion = $promotionCat;
        }

        return $product->getPrice() - $product->getPrice() * ($promotion / 100);
    }
}