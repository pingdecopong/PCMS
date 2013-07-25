<?php

namespace Arte\PCMS\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TBSystemUserSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('LoginId', 'text', array(
                'label'     => 'ログインID',
                'required'  => false,
                'max_length' => 50,
            ))
//            ->add('Salt', 'text', array(
//                'label'     => 'Salt',
//                'required'  => false,
//            ))
//            ->add('Password', 'text', array(
//                'label'     => 'Password',
//                'required'  => false,
//            ))
            ->add('Active', 'choice', array(
                'label'     => '状態',
                'choices'   => array('1' => '有効', '0' => '無効'),
                'required'  => false,
                'expanded'  => false,
                'multiple'  => false,
            ))
//            ->add('SystemRoleId', 'text', array(
//                'label'     => 'SystemRoleId',
//                'required'  => false,
//            ))
            ->add('DisplayName', 'text', array(
                'label'     => 'ユーザ名',
                'required'  => false,
                'max_length' => 50,
            ))
//            ->add('DisplayNameKana', 'text', array(
//                'label'     => 'DisplayNameKana',
//                'required'  => false,
//            ))
//            ->add('NickName', 'text', array(
//                'label'     => 'NickName',
//                'required'  => false,
//            ))
            ->add('MailAddress', 'text', array(
                'label'     => 'メール',
                'required'  => false,
                'max_length' => 100,
            ))
//            ->add('DepartmentId', 'text', array(
//                'label'     => 'DepartmentId',
//                'required'  => false,
//            ))
//                ->add('LastLoginDatetime', 'datetime', array(
//                'label'     => 'LastLoginDatetime',
//                'required'  => false,
//                ))
//            ->add('DeleteFlag', 'choice', array(
//                'label'     => 'DeleteFlag',
//                'choices'   => array('1' => '有効', '0' => '無効'),
//                'required'  => false,
//                'expanded'  => false,
//                'multiple'  => false,
//            ))
//            ->add('CreatedUserId', 'text', array(
//                'label'     => 'CreatedUserId',
//                'required'  => false,
//            ))
//                ->add('CreatedDatetime', 'datetime', array(
//                'label'     => 'CreatedDatetime',
//                'required'  => false,
//                ))
//            ->add('UpdatedUserId', 'text', array(
//                'label'     => 'UpdatedUserId',
//                'required'  => false,
//            ))
//                ->add('UpdatedDatetime', 'datetime', array(
//                'label'     => 'UpdatedDatetime',
//                'required'  => false,
//                ))
        
//            ->add('TBDepartmentDepartmentId')
            ->add('TBDepartmentDepartmentId', 'entity', array(
                'label' => '部署',
                'required' => false,
                'empty_value' => '----',
                'class' => 'ArtePCMSBizlogicBundle:TBDepartment',
                'property' => 'Name',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->andWhere('d.DeleteFlag = :DeleteFlag')
                        ->orderBy('d.SortNo', 'ASC')
                        ->setParameter('DeleteFlag', false);
                },
            ))
//            ->add('TBSystemUserUpdatedUserId')
//            ->add('TBSystemUserCreatedUserId')
        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arte\PCMS\AdminBundle\Form\TBSystemUserSearchModel'
        ));
    }

    public function getName()
    {
        return 'TBSystemUser';
    }
}
