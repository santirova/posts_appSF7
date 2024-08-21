<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

   

    public function findAllPosts(){
        return $this->getEntityManager()
            ->createQuery('
                SELECT post.id,post.title,post.description,post.file,post.creation_date,post.url
                FROM App\Entity\Post post
                ORDER BY post.id DESC
            ');
            
    }

    public function findUserPosts($id){
        return $this->getEntityManager()
            ->createQuery('
                SELECT post.id,post.title,post.description,post.file,post.creation_date,post.url
                FROM App\Entity\Post post
                WHERE post.user = :id
                ORDER BY post.id DESC
            ')
            ->setParameter('id', $id);
    }


}
