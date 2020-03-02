<?php
namespace App\Admin;

use App\Entity\User;
use Sonata\UserBundle\Admin\Model\UserAdmin as SonataUserAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Filter\NumberFilter;
use Sonata\DoctrineORMAdminBundle\Filter\StringFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class UserAdmin extends SonataUserAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filterMapper): void
    {
        $filterMapper
            ->add('id', NumberFilter::class)
            ->add('username', StringFilter::class)
            ->add('email', StringFilter::class)
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        parent::configureFormFields($formMapper);

        $formMapper
            ->tab('User')
                ->with('Profile')
                    ->add('middlename', null, [
                        'required' => false,
                        'label' => 'middlename'
                    ])
                    ->add('employmentDate', DateType::class, [
                        'required' => false,
                        'label' => 'Employment date'
                    ])
                    ->add('employeeStatus', ChoiceType::class, [
                        'label' => 'Employee Status',
                        'choices' => User::getEmployeeStatusChoices()
                    ])
                ->end()
            ->end()
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->add('fullname');
        parent::configureListFields($listMapper);
    }
}