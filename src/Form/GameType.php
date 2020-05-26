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
     * @param array<mixed>         $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($user): void {
            /** @var Game $game */
            $game = $event->getData();

            if ($user instanceof UserInterface) {
                $game->setCreator($user);
            }
            $game
                ->setOptions([
                    'penalty' => 20,
                    'weapon' => true,
                    'bonus' => true,
                ])
                ->setSize($game->getMaxPlayer());
        });
    }

    /**
     * Configure
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
