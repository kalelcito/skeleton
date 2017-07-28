<?php

namespace FrontendBundle\Controller;

use CoreBundle\Entity\Clientes;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        /*return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));*/
        $form = $this->createForm(LoginType::class,null,array(
            'method' => 'POST',
        ));

        /*$form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()){
                // data is an array with "name", "email"
                $data = $form->getData();
                return $this->redirectToRoute('homepage');
            }else{

            }
        }*/
        return $this->render('@Frontend/Login/index.html.twig', array(
            'form' => $form->createView(),
            'error'         => $error,
        ));

    }

    /**
     * @Route("/registro", name="registro")
     */
    public function registroAction(Request $request)
    {
        //$authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        //$error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        //$lastUsername = $authenticationUtils->getLastUsername();

        /*return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));*/
        $registrado=0;
        $existe=0;
        $form = $this->createForm(RegistroType::class,null,array(
            'method' => 'POST',
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()){
                // data is an array with "name", "email"
                $data = $form->getData();
                $em = $this->getDoctrine()->getManager();

                $busr = $em->getRepository('CoreBundle:Clientes')->findOneByEmail($data['email']);
                if(!$busr){
                    $nivel = $em->getRepository('CoreBundle:Niveles')->find($data["niveles"]);
                    $perfil = $em->getRepository('CoreBundle:Perfil')->find(1);
                    $usuario=new Clientes();
                    $usuario->setNiveles($nivel);
                    $usuario->setPerfil($perfil);
                    $usuario->setNombre($data['nombre']);
                    $usuario->setApellidos($data['apellidos']);
                    $usuario->setEmail($data['email']);
                    $usuario->setUsername($data['email']);
                //$usuario->setTelefono($data['telefono']);
                    $usuario->setNomEmpresa($data['empresa']);
                    $usuario->setPuesto($data['puesto']);
                    $usuario->setPass($data['password']);
                  //  $usuario->setCreado(new \DateTime());
                //$usuario->setCelular($data['celular']);
                    $usuario->setVerificado(0);
                    $usuario->setIsActive(0);
                    $usuario->setSaldo(0);
                    $em->persist($usuario);
                    $em->flush();
                    $registrado=1;

                    $this->get('send_mail')->send('info@plataforma-er.com',array('info@empresaresponsable.org','cricardez@red-erac.com'),'Empresa Responsable - Nuevo Registro',$this->renderView('@Frontend/Default/mail/registro.html.twig',array('registro'=>$usuario)),null);
                    if($nivel->getId()==2){
                        return $this->redirectToRoute('frontend_pagos_membresia', array('id' => $usuario->getId()));
                    }else{
                        return $this->redirectToRoute('homepage');
                    }

                }else{
                    $existe=1;
                }
                //return $this->redirectToRoute('homepage');
            }else{

            }
        }
        return $this->render('@Frontend/Login/registro.html.twig', array(
            'form' => $form->createView(),
            'registrado'=>$registrado,
            'existe'=>$existe
        ));
    }

    /**
     * @Route("/activar", name="activar")
     */
    public function activarAction(Request $request)
    {
        //$authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        //$error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        //$lastUsername = $authenticationUtils->getLastUsername();

        /*return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));*/
        $registrado=0;
        $form = $this->createForm(ActivarType::class,null,array(
            'method' => 'POST',
        ));
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()){
                // data is an array with "name", "email"
                $data = $form->getData();
                //return $this->redirectToRoute('homepage');
                $registrado=1;
            }else{

            }
        }
        return $this->render('@Frontend/Login/recuperar.html.twig', array(
            'form' => $form->createView(),
            'registrado'=>$registrado
        ));
    }

    /**
     * @Route("/recuperar", name="recuperar")
     */
    public function recuperarAction(Request $request)
    {
        $registrado=0;
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
                $em = $this->getDoctrine()->getManager();
                $search = $em->getRepository('CoreBundle:Clientes')->findOneByEmail($data['email']);
                if(!$search){
                    $registrado=2;
                }else{
                    $this->get('send_mail')->send('info@plataforma-er.com',$search->getEmail(),'Empresa Responsable - Recuperar Contraseña',$this->renderView('@Frontend/Default/mail/recovery.html.twig',array('email'=>$search->getEmail(),'pass'=>$search->getPass())),null);
                    $registrado=1;
                }
        }
        return $this->render('@Frontend/Login/recuperar.html.twig', array(
            'registrado'=>$registrado
        ));
    }

    /**
     * @Route("/Terminos-Condiciones", name="terminos")
     */
    public function terminosAction(Request $request)
    {
        return $this->render('@Frontend/Login/terminos.html.twig');
    }
}
