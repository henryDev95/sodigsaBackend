<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Usuario;
#[Route('/api', name: 'api_usuario')]
class UsuarioController extends AbstractController
{
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }


    #[Route('/postUsuario', name: 'app_usuarioPost', methods:['post'] )]
    public function addUsuario(Request $request): JsonResponse
    {
        $entityManager = $this->doctrine->getManager();
        $usuario = new Usuario();
        $requestContent = json_decode($request->getContent(), true);
        $usuarioExist = $this->doctrine->getRepository(Usuario::class)->find( $requestContent['dni']);
        if($usuarioExist){
            $usuario->setName($requestContent['name']);
            $usuario->setLastname($requestContent['lastname']);
            $usuario->setDni($requestContent['dni']);
            $usuario->setEmail($requestContent['email']);
            $usuario->setAddress($requestContent['address']);
            $usuario->setPhone($requestContent['phone']);
            $usuario->setNote($requestContent['note']);
    
            $entityManager->persist($usuario);
            $entityManager->flush();
    
            $data =  [
                'name' => $usuario->getName(),
                'lastname'=>$usuario->getLastname(),
                'dni'=>$usuario->getDni(),
                'email'=>$usuario->getEmail(),
                'address'=>$usuario->getAddress(),
                'phone'=>$usuario->getPhone(),
                'note'=>$usuario->getNote()
            ];
    
            return $this->json([
                'usuario' => $data,
                'status'=>'OK'
            ]);

        }else{
            return $this->json(['result'=> 'Existe Usuario con el dni ingresado', 'error'=> 404]);
        }
      
    }


    #[Route('/updateUsuario/{id}', name: 'usuario_update', methods:['put', 'patch'] )]
    public function update(Request $request, int $id): JsonResponse
    {
        $entityManager = $this->doctrine->getManager();
        $usuario = $entityManager->getRepository(Usuario::class)->find($id);
          
        if (!$usuario) {
            return $this->json('No user found for id ' . $id, 404);
        }
        $requestContent = json_decode($request->getContent(), true);
        $usuario->setName($requestContent['name']);
        $usuario->setLastname($requestContent['lastname']);
        $usuario->setDni($requestContent['dni']);
        $usuario->setEmail($requestContent['email']);
        $usuario->setAddress($requestContent['address']);
        $usuario->setPhone($requestContent['phone']);
        $usuario->setNote($requestContent['note']);
        $entityManager->flush();

        $data =  [
            'name' => $usuario->getName(),
            'lastname'=>$usuario->getLastname(),
            'dni'=>$usuario->getDni(),
            'email'=>$usuario->getEmail(),
            'address'=>$usuario->getAddress(),
            'phone'=>$usuario->getPhone(),
            'note'=>$usuario->getNote()
        ];
         return $this->json($data);
    }


    #[Route('/deleteUsuario/{id}', name: 'usuario_delete', methods:['delete'] )]
    public function delete(int $id): JsonResponse
    {
        $entityManager = $this->doctrine->getManager();
        $usuario = $entityManager->getRepository(Usuario::class)->find($id);
        if (!$usuario) {
            return $this->json('No se encontro el usuario con el id' . $id, 404);
        }
        $entityManager->remove($usuario);
        $entityManager->flush();
        return $this->json('Deleted a project successfully with id ' . $id);
    }
}
