<?php

namespace App\Controller;

use App\Entity\Compagnie;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CompagnieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class CompagnieController extends AbstractController
{
    #[Route('/api/compagnie/insert', name: 'app_compagnie_insert',methods:['POST'])]

    public function insert(EntityManagerInterface $entity_manager, Request $request): Response
    {
        $data = json_decode($request->getContent(),true);
        try{
            $compagnie = new Compagnie();
            $compagnie->setNomCompagnie($data['nomCompagnie']);
            $compagnie->setAttribut($data['attribut']);
            $compagnie->setEmail($data['email']);

            $entity_manager->persist($compagnie);
            $entity_manager->flush();

            return $this->json(
                [
                    'status'=>200,
                    'message'=>'Compagne ajoutee avec succes'
                ],Response::HTTP_CREATED);      
        }
        catch(\Exception $e){
            return $this->json([
                'status'=>500,
                'message'=> 'Erreur lors de l\'insertion de la compagnie'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/compagnie/getAll',name:'app_compagnie_getAll',methods:['GET'])]
    public function getAll(CompagnieRepository $compagnieRepository):Response
    {
        try{
            $compagnie = $compagnieRepository->findAll();
            return $this->json(
                $compagnie,200,
            );
        }
        catch(\Exception $e){
            return $this->json([
                'status'=>500,
                'message'=>'Erreur lors de la recuperation des compagnies'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('api/compagnie/get/{id}',name:'app_compagnie_get',methods:['GET'])]
    public function getByI(CompagnieRepository $compagnieRepository,int $id):Response{
        try{
            $compagnie = $compagnieRepository->find($id);
            if(!$compagnie){
                return $this->json([
                    'status'=>404,
                    'message'=>'Compagnie non trouvee'
                ],Response::HTTP_NOT_FOUND);
            }
            return $this->json(
                [
                    'status'=>200,
                    'data'=>$compagnie                
                ],Response::HTTP_OK
            );
        }
        catch(\Exception $e){
            return $this->json([
                'status'=>500,
                'message'=>'Erreur lors de la recuperation de la compagnie'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/compagnie/update/{id}', name:'app_etudiant_update', methods:['PATCH'])]
    public function update(EntityManagerInterface $entity_manager, Request $request, CompagnieRepository $compagnieRepository, int $id): Response
    {
        try{
            $data = json_decode($request->getContent(), true);
            $compagnie = $compagnieRepository->find($id);
            if (!$compagnie) {
                return $this->json([
                    'status' => 404,
                    'message' => 'Compagnie non trouvee'
                ], Response::HTTP_NOT_FOUND);
            }
            if (isset($data['nomCompagnie'])) {
                $compagnie->setNomCompagnie($data['nomCompagnie']);
            }
            if (isset($data['attribut'])) {
                $compagnie->setAttribut($data['attribut']);
            }
            if (isset($data['email'])) {
                $compagnie->setEmail($data['email']);
            }

            $entity_manager->flush();

            return $this->json([
                'status' => 200,
                'message' => 'Compagnie modifiee avec succes'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 500,
                'message' => 'Erreur lors de la modification de la compagnie'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/api/compagnie/delete/{id}', name:'app_compagnie_delete',methods:['DELETE'])]
    public function delete(EntityManagerInterface $entity_manager, CompagnieRepository $compagnieRepository, int $id): Response
    {
        try{
            $compagnie = $compagnieRepository->find($id);
            if (!$compagnie) {
                return $this->json([
                    'status' => 404,
                    'message' => 'Compagnie non trouvee'
                ], Response::HTTP_NOT_FOUND);
            }
            $entity_manager->remove($compagnie);
            $entity_manager->flush();

            return $this->json([
                'status' => 200,
                'message' => 'Compagnie supprimee avec succes'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 500,
                'message' => 'Erreur lors de la suppression de la compagnie'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
