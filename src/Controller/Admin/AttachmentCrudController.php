<?php

namespace App\Controller\Admin;

use App\Entity\Attachment;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AttachmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Attachment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();
//        yield ImageField::new('image')
//            ->setBasePath('uploads/avatars')
//            ->setUploadDir('public/uploads/avatars')
//            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]');
        yield TextField::new('attachment');
        yield AssociationField::new('question');
    }


}