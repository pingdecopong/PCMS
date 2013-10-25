<?php

namespace Arte\PCMS\AdminBundle\Form;



use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WorkerEditRoleFormType extends AbstractType
{
    private $choices;

    function __construct()
    {
        $this->choices = array();
    }

    /**
     * @param mixed $choices
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;
    }

    /**
     * @return mixed
     */
    public function getChoices()
    {
        return $this->choices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Id', 'hidden', array(
                'label'     => 'id',
                'required'  => false,
            ))
            ->add('Role', 'choice', array(
                'label'     => '権限',
                'required'  => true,
                'choices'   => array(
                    '1'   => '管理者',
                    '2' => '一般',
                    '3'   => '表示のみ',
                ),
                'expanded' => true,
                'multiple'  => false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arte\PCMS\AdminBundle\Form\WorkerEditRoleFormModel',
//            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'WorkerEditRoleForm';
    }

}