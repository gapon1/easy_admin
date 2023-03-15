<?php

namespace App\Controller\Admin;

use App\Entity\Attachment;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\File;
use Symfony\UX\Dropzone\Form\DropzoneType;


class AttachmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Attachment::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $approveAction = Action::new('dropzone')
            ->addCssClass('btn btn-success')
            ->setIcon('fa fa-check-circle')
            ->displayAsButton()
            ->setTemplatePath('admin/approve_action.html.twig')
            ->linkToCrudAction('dropzone');

        return parent::configureActions($actions)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->add(Crud::PAGE_DETAIL, $approveAction);

    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();
        yield ImageField::new('imageFile')
            ->setBasePath('uploads/dropzone')
            ->setUploadDir('public/uploads/dropzone')
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]');
        yield AssociationField::new('question');
        yield TextField::new('attachment');
    }

    public function dropzone(AdminContext $adminContext, Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator): Response
    {
        $attachment = $adminContext->getEntity()->getInstance();

        $form = $this->createFormBuilder()
            ->add('imageFile', DropzoneType::class, [
                'label' => 'files',
                'multiple' => true,
                'constraints' => [
                    new Count(['max' => 5]),
                    new All([
                        new File([
                            'maxSize' => '2048k',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png'
                            ],
                        ])
                    ])
                ],
                'attr' => [
                    'data-controller' => 'mydropzone',
                    'placeholder' => 'Drag and drop or browse',
                    'accept' => '.jpg, .jpeg, .png'
                ],
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('imageFile')->getData();
            // this condition is needed because the 'brochure' field is not required
            if ($brochureFile) {

                foreach ($brochureFile as $item) {
                    $originalFilename = pathinfo($item->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $item->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $item->move(
                            $this->getParameter('dropzone_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $attachment->setImageFile($newFilename);
                    $attachment->setImage('testppp');
                    $attachment->setAttachment('lolll');
                    $entityManager->flush();
                }
            }

            $targetUrl = $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Crud::PAGE_DETAIL)
                ->setEntityId($attachment->getId())
                ->generateUrl();
            return $this->redirect($targetUrl);
        }

        return $this->render('admin/drop_zone.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}