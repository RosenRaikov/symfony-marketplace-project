<?php
/**
 * Created by PhpStorm.
 * User: Rosen
 * Date: 09-May-17
 * Time: 10:56
 */

namespace MarketplaceBundle\Service;

use MarketplaceBundle\Entity\Product;
use MarketplaceBundle\Entity\User;
use PDO;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class ownedItems
{
    private function connection()
    {
        return new PDO('mysql:host=localhost;dbname=symfony_marketplace', 'root', '');
    }

    public function fetchCount(User $user, Product $product)
    {
        $userId = $user->getId();
        $productId = $product->getId();
        $db = $this->connection();
        $query = $db->prepare("SELECT count FROM user_products WHERE user_id = :user AND product_id = :product");
        $query->bindParam(':user', $userId);
        $query->bindParam(':product', $productId);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        return $data['count'];
    }

    public function isForSale(User $user, Product $product){
        $userId = $user->getId();
        $productId = $product->getId();
        $db = $this->connection();
        $query = $db->prepare("SELECT for_sale FROM user_products WHERE user_id = :user AND product_id = :product");
        $query->bindParam(':user', $userId);
        $query->bindParam(':product', $productId);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        return $data['for_sale'];
    }

}
