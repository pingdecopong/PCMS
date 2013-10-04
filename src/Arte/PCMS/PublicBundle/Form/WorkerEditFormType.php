<?php

namespace Arte\PCMS\PublicBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WorkerEditFormType extends AbstractType
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
            ->add('Users', 'choice', array(
                'label'     => '所属ユーザー',
                'required'  => false,
                'choices'   => $this->choices,
                'expanded' => false,
                'multiple'  => true,
                'attr'      => array(
                    'size' => 10,
                ),
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arte\PCMS\PublicBundle\Form\WorkerEditFormModel',
//            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'WorkerEditForm';
    }

}