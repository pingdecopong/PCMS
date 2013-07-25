<?php
/**
 * システムユーザー　登録画面用Type
 */

namespace Arte\PCMS\AdminBundle\Form\SystemUser;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SystemUserFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'hidden', array(
                'label' => 'id',
            ))
            ->add('loginId', 'text', array(
                'label' => 'ログインID',
                'required' => true,
                'max_length' => 50,
            ))
            ->add('displayName', 'text', array(
                'label' => 'ユーザ名',
                'required' => true,
                'max_length' => 50,
            ))
            ->add('displayNameKana', 'text', array(
                'label' => 'ユーザ名（カナ）',
                'required' => true,
                'max_length' => 50,
            ))
            ->add('nickName', 'text', array(
                'label' => '略称',
                'required' => true,
                'max_length' => 5,
            ))
            ->add('department', 'entity', array(
                'label' => '部署',
                'required' => false,
                'empty_value' => '--部署なし--',
                'class' => 'ArtePCMSBizlogicBundle:TBDepartment',
                'property' => 'Name',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->andWhere('d.DeleteFlag = :DeleteFlag')
                        ->orderBy('d.SortNo', 'ASC')
                        ->setParameter('DeleteFlag', false);
                },
            ))
            ->add('mailAddress', 'text', array(
                'label' => 'メールアドレス',
                'required' => true,
                'max_length' => 100,
            ))
            ->add('systemRoleId', 'text', array(
                'label' => '権限番号',
                'required' => true,
            ))
            ->add('buttonAction', 'hidden', array(
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'freeze' => false,
            'data_class' => 'Arte\PCMS\AdminBundle\Form\SystemUser\SystemUserFormModel',
        ));
    }

    public function getName()
    {
        return 'systemuser';
    }
}