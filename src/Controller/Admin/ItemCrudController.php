<?php

namespace App\Controller\Admin;

use App\Entity\Item;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Twig\Profiler\Dumper\HtmlDumper;

class ItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Item::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'ASC'])
            ->showEntityActionsInlined();
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            AvatarField::new('image')
                ->formatValue(static function ($value, ?Item $item) {
                    return $item?->getImageUrl();
                }),
            BooleanField::new('is_active'),
            TextField::new('code'),
            TextField::new('name'),
            TextEditorField::new('details'),
            ImageField::new('image')
                ->setBasePath('uploads/items')
                ->setUploadDir('public/uploads/items')
                ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
                ->onlyOnForms(),
            TextField::new('code')
                ->setTemplatePath('bot/chart.html.twig')
                ->setLabel(false)
                ->onlyOnDetail()
        ];
    }
}
