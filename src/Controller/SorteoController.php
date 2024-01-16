<?php

namespace App\Controller;

use App\Entity\Cupon;
use App\Form\SorteoType;
use App\Repository\CuponRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class SorteoController extends AbstractController
{
    private $cuponRepository;

    public function __construct(CuponRepository $cuponRepository) {
        $this->cuponRepository = $cuponRepository;
    }


    #[Route('/sorteo', name: 'app_sorteo')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {

        //Inicializamos el formulario
        $form = $this->createForm(SorteoType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Obtenemos los datos introducidos en el formulario
            $data = $form->getData();

            // dump($data['fecha']);

            $fecha = \DateTime::createFromFormat('j-m-Y', $data['fecha']);

            //Realizamos la busqueda en la BD
            $sorteo = $this->cuponRepository->findOneBy(['fecha' => $fecha]);

            if ($data['numero'] == $sorteo->getNumero())
                $premiado = true;
            else
                $premiado = false;

            return $this->render('sorteo/resultado.html.twig', [
                'premiado' => $premiado,
                'numero' => $data['numero'],
                'premio' => $sorteo->getPremio(),
                'numero_premiado' => $sorteo->getNumero(),
                'fecha' => $sorteo->getFecha(),

            ]);

            
         
        }

        return $this->render('sorteo/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
