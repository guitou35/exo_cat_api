<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(HttpClientInterface $client): Response
    {
        // appel de l'API pour recuperer la liste des catégories
        $response = $client->request('GET', 'https://api.thecatapi.com/v1/categories', [
            'headers' => [
                'x-api-key' => '97f2205b-7fd1-4493-b1f2-b8a3f0d2e9cc',
            ],
        ]);

        // je test le statut de la réponse ainsi que s'il y a la réponse est vide
        if ($response->getStatusCode() === Response::HTTP_OK && $response->getContent()) {
            $error = false;
            $categories = $response->toArray();
        } else {
            $error = true;
        }

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'error' => $error
        ]);
    }

    /**
     * @Route("/cat/{id}", name="cat_by_categorie")
     */
    public function listChatByCategorie(HttpClientInterface $client, $id): Response
    {
        // appel de l'API pour recuperer la liste des chats pour une une catégorie
        $response = $client->request('GET', 'https://api.thecatapi.com/v1/images/search', [
            'headers' => [
                'x-api-key' => '97f2205b-7fd1-4493-b1f2-b8a3f0d2e9cc',
            ],
            'query' => [
                'category_ids' => $id,
                'limit' => 100,
            ],
        ]);

        // je test le statut de la réponse ainsi que s'il y a la réponse est vide
        if ($response->getStatusCode() === Response::HTTP_OK && $response->toArray()) {
            $error = false;
            $catImages = $response->toArray();
        } else {
            $error = true;
        }


        return $this->render('home/cat.html.twig', [
            'catImages' => $catImages,
            'error' => $error
        ]);
    }

    /**
     * @Route("/votes", name="my_vote")
     */
    public function myVote(HttpClientInterface $client): Response
    {
        // je fais un appel API pour récupérer la liste des votes pour l'utilisateur User_cat_007
        $response = $client->request('GET', 'https://api.thecatapi.com/v1/votes', [
            'headers' => [
                'x-api-key' => '97f2205b-7fd1-4493-b1f2-b8a3f0d2e9cc',
            ],
            'query' => [
                'sub_id' => 'User_cat_007',
            ],
        ]);

        // initialisation de la variable votes si aucun vote
        $votes = false;

        // je test le statut de la réponse ainsi que s'il y a la réponse est vide
        if ($response->getStatusCode() === Response::HTTP_OK && $response->toArray()) {
            $error = false;
            $votes = $response->toArray();
        } elseif ($response->getStatusCode() === Response::HTTP_OK) {
            $error = false;
            // je n'affiche pas le message d'erreur car pas de vote pour cet utilisateur
        } else {
            $error = true;
        }


        return $this->render('home/my_vote.html.twig', [
            'votes' => $votes,
            'error' => $error
        ]);
    }

    /**
     * @Route("/vote/add/{id}-{value}", name="add_vote")
     */
    public function addVote(HttpClientInterface $client, $id, $value): Response
    {

        //  je fais un appel API pour ajouter un vote positif ou négatif sur une image pour l'utilisateur User_cat_007
        $response = $client->request('POST', 'https://api.thecatapi.com/v1/votes', [
            'headers' => [
                'x-api-key' => '97f2205b-7fd1-4493-b1f2-b8a3f0d2e9cc',
                'content-type' => 'application/json',
            ],
            'json' => [
                'image_id' => $id,
                'sub_id' => 'User_cat_007',
                'value' => $value,
            ],
        ]);

        // je test le statut de la réponse
        if ($response->getStatusCode() === Response::HTTP_OK) {
            $error = false;
        } else {
            $error = true;
        }

        return $this->redirectToRoute('my_vote');
    }

    /**
     * @Route("/vote/delete/{id}", name="delete_vote")
     */
    public function deleteVote(HttpClientInterface $client, $id): Response
    {
        //  je fais un appel API pour supprimer un vote positif ou négatif sur une image pour l'utilisateur User_cat_007
        $response = $client->request('DELETE', 'https://api.thecatapi.com/v1/votes/' . $id, [
            'headers' => [
                'x-api-key' => '97f2205b-7fd1-4493-b1f2-b8a3f0d2e9cc',
            ],
        ]);

        // je test le statut de la réponse
        if ($response->getStatusCode() === Response::HTTP_OK) {
            $error = false;
            return $this->redirectToRoute('my_vote');
        } else {
            $error = true;

        }

        return $this->render('home/error.html.twig', [
            'error' => $error,
        ]);
    }

}
