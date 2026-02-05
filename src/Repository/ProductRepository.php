<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Collection;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

   public function searchProducts(string $search, int $category): array
   {
       if($category === 0) {
           return $this->createQueryBuilder('p')
               ->where('p.title LIKE :search')
               ->setParameter('search', '%'.$search.'%')
               ->getQuery()
               ->getResult();
       }

       return $this->createQueryBuilder('p')
           ->where('p.category = :category')
           ->setParameter('category', $category)
           ->andwhere('p.title LIKE :search')
           ->setParameter('search', '%'.$search.'%')
           ->getQuery()
           ->getResult();
   }
}
