<?php

namespace App\EasyAdmin;

use App\Entity\Attachment;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\UX\Dropzone\Form\DropzoneType;


class DropZoneField implements FieldInterface
{
    use FieldTrait;
    public static function new(string $propertyName, ?string $label = null)
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplatePath('admin/field/drop_zone.html.twig')
            ->setFormType(DropzoneType::class)
            ->setFormTypeOptions([  'data_class' => Attachment::class,])
            ->setDefaultColumns('col-md-4 col-xxl-3');
    }


}