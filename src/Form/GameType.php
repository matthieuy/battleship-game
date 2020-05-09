<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class GameType
 * @package App\Form
 */
class GameType extends AbstractType
{
    /** @var User $user */
    protected $user;

    /**
     * GameType constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    /**
     * Build
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Create form
        $builder
            ->add('name', FormType\TextType::class, [
                'attr' => [
                    'placeholder' => 'Name of the game',
                    'maxlength' => 128,
                ],
            ])
            ->add('maxPlayer', FormType\HiddenType::class, [
                'attr' => [
                    'class' => 'hidden-maxplayer',
                ],
            ]);

        // Default value on post submit
        $user = $this->user;
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($user) {
            /** @var Game $game */
            $game = $event->getData();
            dump($user);

            if ($user instanceof UserInterface) {
                $game->setCreator($user);
            }
            $game->getSizeByPlayerNb($game->getMaxPlayer(), true);
        });
    }

    /**
     * Configure
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
