<?php

namespace Arte\PCMS\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TBSystemUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('LoginId', 'text', array(
                'label'     => 'ログインID',
                'required'  => false,
            ))
            ->add('Active', 'checkbox', array(
                'label'     => '有効状態',
                'required'  => false,
            ))
            ->add('SystemRoleId', 'text', array(
                'label'     => '権限',
                'required'  => false,
            ))
            ->add('DisplayName', 'text', array(
                'label'     => 'ユーザー名',
                'required'  => false,
            ))
            ->add('DisplayNameKana', 'text', array(
                'label'     => 'ユーザ名（カナ）',
                'required'  => false,
            ))
            ->add('NickName', 'text', array(
                'label'     => '略称',
                'required'  => false,
            ))
            ->add('MailAddress', 'text', array(
                'label'     => 'メールアドレス',
                'required'  => false,
            ))
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
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arte\PCMS\BizlogicBundle\Entity\TBSystemUser'
        ));
    }

    public function getName()
    {
        return 'TBSystemUser';
    }
}
