<?php
/**
 * Created by PhpStorm.
 * User: Nicolas LF
 */

namespace AppBundle\Controller\Front;

use AppBundle\Controller\Controller;
use AppBundle\Entity\FileOrder;
use AppBundle\Entity\FileUpload;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UploadController extends Controller
{

    /**
     * @var Event
     */
    private $event;

    /**
     * UploadController constructor.
     */
    public function __construct()
    {
        $this->event = new Event();
    }

    /**
     * @Route("/cart/", name="app_cart")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm('AppBundle\Form\Type\FileFormType', null, ['user' => $this->getUser()]);
        $form->handleRequest($request);

        // Ajouter un ancien fichier à la commande
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            foreach ($data['Files'] as $file) {
                $this->addFileInOrder($file);
            }
        }
        // La variable $cart correspond au panier en cours (object). (J'ai supprimé les méthodes relatives
        // à cela par souci de confidentialité)
        // Il faut donc remplacer la valeur ci-dessous pour que ça marche
        $cart = 1;
        $files = $em->getRepository('AppBundle:FileOrder')->findByCart($cart);
        return $this->render('front/main/cart.html.twig', array(
            'form' => $form->createView(),
            'files' => $files
        ));
    }

    /**
     * Ajout de fichiers en Ajax et vérification si déjà existant avec le nom du fichier
     *
     * @param Request $request
     * @return JsonResponse
     * @Route("/upload-file/", name="ajax_file_send_action")
     */
    public function ajaxFileSendAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $uploadFile = new FileUpload();
            $media = $request->files->get('file');

            $uploadFile->setFile($media);
            $uploadFile->setUser($this->getUser());

            $uploadFile->setPath($media->getPathName());
            $uploadFile->setName($media);
            $file = $em->getRepository('AppBundle:FileUpload')->findByName($uploadFile->getName());
            if ($file == null) {
                $uploadFile->upload();
                $em->persist($uploadFile);
                $em->flush();
                $this->addFileInOrder($uploadFile);
                return new JsonResponse(array('success' => true));
            }
        }
    }

    /**
     * Lie le fichier à la commande et vérifie si il n'est pas déjà lié
     *
     * @param $file
     * @return boolean
     */
    public function addFileInOrder($file)
    {
        $em = $this->getDoctrine()->getManager();
        // La variable $cart correspond au panier en cours (object). (J'ai supprimé les méthodes relatives
        // à cela par souci de confidentialité)
        // Il faut donc remplacer la valeur ci-dessous pour que ça marche
        $cart = 1;
        $fileExist = $em->getRepository('AppBundle:FileOrder')->findByFileUploads($file);
        if ($fileExist == null) {
            $fileOrder = new FileOrder();
            $fileOrder->setFileUploads($file);
            $fileOrder->setCarts($cart);
            $em->persist($fileOrder);
            $em->flush();
            return true;
        }
        return false;
    }
}