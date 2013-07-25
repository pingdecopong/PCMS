<?php

namespace Arte\PCMS\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TBSystemUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('LoginId', 'text', array(
                'label'     => 'LoginId',
                'required'  => false,
            ))
            ->add('Salt', 'text', array(
                'label'     => 'Salt',
                'required'  => false,
            ))
            ->add('Password', 'text', array(
                'label'     => 'Password',
                'required'  => false,
            ))
            ->add('Active', 'checkbox', array(
                'label'     => 'Active',
                'required'  => false,
            ))
            ->add('SystemRoleId', 'text', array(
                'label'     => 'SystemRoleId',
                'required'  => false,
            ))
            ->add('DisplayName', 'text', array(
                'label'     => 'DisplayName',
                'required'  => false,
            ))
            ->add('DisplayNameKana', 'text', array(
                'label'     => 'DisplayNameKana',
                'required'  => false,
            ))
            ->add('NickName', 'text', array(
                'label'     => 'NickName',
                'required'  => false,
            ))
            ->add('MailAddress', 'text', array(
                'label'     => 'MailAddress',
                'required'  => false,
            ))
            ->add('DepartmentId', 'text', array(
                'label'     => 'DepartmentId',
                'required'  => false,
            ))
                ->add('LastLoginDatetime', 'datetime', array(
                'label'     => 'LastLoginDatetime',
                'required'  => false,
                ))
            ->add('DeleteFlag', 'checkbox', array(
                'label'     => 'DeleteFlag',
                'required'  => false,
            ))
            ->add('CreatedUserId', 'text', array(
                'label'     => 'CreatedUserId',
                'required'  => false,
            ))
                ->add('CreatedDatetime', 'datetime', array(
                'label'     => 'CreatedDatetime',
                'required'  => false,
                ))
            ->add('UpdatedUserId', 'text', array(
                'label'     => 'UpdatedUserId',
                'required'  => false,
            ))
                ->add('UpdatedDatetime', 'datetime', array(
                'label'     => 'UpdatedDatetime',
                'required'  => false,
                ))
        
            ->add('TBDepartmentDepartmentId')
            ->add('TBSystemUserUpdatedUserId')
            ->add('TBSystemUserCreatedUserId')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arte\PCMS\AdminBundle\Entity\TBSystemUser'
        ));
    }

    public function getName()
    {
        return 'TBSystemUser';
    }
}
