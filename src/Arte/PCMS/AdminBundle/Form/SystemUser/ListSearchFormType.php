<?php
/**
 * リスト検索用タイプ
 */

namespace Arte\PCMS\AdminBundle\Form\SystemUser;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ListSearchFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('LoginId', 'text', array(
                'label' => 'ログインID',
            ))
            ->add('DisplayName', 'text', array(
                'label' => '名前',
            ))
            ->add('MailAddress', 'text', array(
                'label' => 'メール',
            ))
            ->add('Department', 'entity', array(
                'label' => '部署',
                'required' => false,
                'empty_value' => '--会社なし--',
                'class' => 'ArtePCMSBizlogicBundle:TBDepartment',
                'property' => 'Name',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->andWhere('d.DeleteFlug = :DeleteFlug')
                        ->orderBy('d.SortNo', 'ASC')
                        ->setParameter('DeleteFlug', false);
                },
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arte\PCMS\AdminBundle\Form\SystemUser\ListSearchFormModel'
        ));
    }

    public function getName()
    {
        return 'listsearch';
    }

}