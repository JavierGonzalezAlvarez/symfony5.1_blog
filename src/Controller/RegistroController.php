<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

//Importamos el formulario UserType y la entidad
use App\Form\UserType;
use App\Entity\User;
//importar la libreria request
use Symfony\Component\HttpFoundation\Request;

//importamos la interface para la contraseña
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class RegistroController extends AbstractController
{
    /**
     * @Route("/registro", name="registro")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User;   //objeto de la clase user                     
        
        //tengo un constructor en user.php, así que quito estas dos lineas de abajo
        //$user->setRoles(['ROLE_USER']);   //tipo array
        //$user->setBaneado(False);  //asigno el valor false en la grabacion. Campo obligatorio  
        
        $form = $this->createForm(UserType::class, $user);                

        $form->handleRequest($request);        
        if ($form->isSubmitted() && $form->isValid()) {                
            //guardar los datos en la base de datos
            //---------------------------------------------
            //recoger los datos del forumlario
            $form->getData();
            //pasar los datos del formulario a una variable
            $grabar = $form->getData();        

            //$password_form = $form->get("password")->getData();
            $password_form = $form['password']->getData();

            $user->setPassword($passwordEncoder->encodePassword(
                //pasamos dos parametros
                             $user,
                             $password_form
            ));

            //$user->setPasswrod->($this-> $passwordEncoder('password'));

            //Guardar con Doctrine
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($grabar);
            $entityManager->flush();            
            //$this->addFlash('mensaje', 'Tus cambios se han guardado!');
            //mejor meterlo en una constante
            $this->addFlash('mensaje', User::REGISTRO_EXITOSO);
            //Esta ruta está en LoginformAutheticator.php
            //return $this->redirectToRoute('app_login');

            //Esta ruta está en Dashboardcontrollert.php
            return $this->redirectToRoute('dashboard');

            //ir a otra pagina
            //return $this->redirectToRoute('task_success');
        }
        
        //renderizar un formulario, despues del createview()
        return $this->render('registro/index.html.twig', [                        
            'formulario' => $form->createview()
        ]);             
    }

}
