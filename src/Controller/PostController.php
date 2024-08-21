<?php

namespace App\Controller;

use App\Entity\Interaction;
use App\Entity\Post;
use App\Entity\User;
use App\Form\InteractionType;
use App\Form\PostType;
use App\Service\FileUploader;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostController extends AbstractController
{
    private $em;
    /**
     * @param $em
     */
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    #[Route('/post/{id}', name: 'postDetail')]
    public function index($id, Security $security, Request $request): Response
    {
        $post = $this->em->getRepository(Post::class)->find($id);
        
        if(!$post ){
            throw $this->createNotFoundException('El post no fue encontrado.');
        }

        $interactions = $post->getInteractions();

        //crear una nueva interaccion

        $interaction = new Interaction();
        $user = $security -> getUser();
        $form = null;
        
        if ($user) {
            $form = $this->createForm(InteractionType::class,$interaction);
            $form -> handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $interaction->setUser($user);
                $interaction->setPost($post);
                $interaction->setUserFavorite(false);
                $this->em->persist($interaction);
                $this->em->flush();
    
                return $this->redirectToRoute('postDetail', ['id' => $post->getId()]);
            }
        }
        
        return $this->render('post/postDetail.html.twig', [
            'post' => $post,
            'interactions' => $interactions,
            'form' => $form ? $form->createView() : null,
        ]);
    }

    #[Route('/post', name: 'posts_home')]
    public function create(Request $request, FileUploader $fileUploader, Security $security, PaginatorInterface $paginator): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class,$post);
        $form -> handleRequest($request);
        $user = $security->getUser();
        
        if($form->isSubmitted() && $form->isValid() && $user){
            $post->setUser($user);
            $file = $form-> get('file')->getData();

            if ($file) {
                $FileName = $fileUploader->upload($file);
                $post->setFile($FileName);
            }

            $url = str_replace(" ", "-",$form->get('title')->getData());
            $post->setUrl($url);
            $this->em->persist($post);
            $this->em->flush();

            return $this->redirectToRoute('posts_home');
        }

        $query = $this->em->getRepository(Post::class)->findAllPosts();

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            4 /*limit per page*/
        );
    
        return $this->render('post/index.html.twig', [
            'form' => $form->createView(),
            'posts'=> $pagination
        ]);
    }

    #[Route('/post/update/one', name: 'update_post')]
    public function update(){

        $post = $this-> em->getRepository(Post::class)->find(3);
        $post ->setTitle("el insertado actualizado");
        $this->em->flush();
        return new JsonResponse(['success' => true]);

    }

    #[Route('/post/remove/one', name: 'remove_post')]
    public function remove(){

        $post = $this-> em->getRepository(Post::class)->find(4);
        $this->em->remove($post);
        $this->em->flush();
        return new JsonResponse(['success' => true]);

    }
}
