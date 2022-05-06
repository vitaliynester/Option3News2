<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\News;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@mail.ru');
        $admin->setName('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, '123321'));
        $admin->setApiToken('adminToken');
        $manager->persist($admin);

        $user = new User();
        $user->setEmail('developer@mail.ru');
        $user->setName('developer');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, '123321'));
        $user->setApiToken('userToken');
        $manager->persist($user);

        for ($i = 1; $i < 69; ++$i) {
            $news = new News();
            $news->setOwner($admin);
            $news->setTitle("Название новости $i");
            $news->setAnnotation("Аннотация $i");
            $news->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus mollis, odio sit amet rutrum pharetra, mauris lacus ullamcorper ante, in pellentesque erat leo eget erat. Morbi pulvinar justo ut massa gravida, sed posuere arcu accumsan. Integer pellentesque vulputate interdum. Morbi bibendum congue ligula, tempor semper risus luctus eleifend. Vestibulum tempus elit et nunc rhoncus malesuada. Pellentesque at hendrerit sapien, et consequat dolor. Donec vel nulla posuere, ultricies nulla quis, pretium lectus. Vestibulum bibendum, odio sit amet hendrerit commodo, ante sem scelerisque felis, non fermentum tortor metus at lectus.');
            $news->setViewCount(rand(1, 10));

            $commentCnt = rand(4, 12);
            for ($j = 1; $j < $commentCnt; ++$j) {
                $comment = new Comment();
                $comment->setOwner($user);
                $comment->setBody("Классная новость $i, прочитал $j раз");
                $comment->setActive(rand(0 ,1) == 0);
                $manager->persist($comment);
                $news->addComment($comment);
            }

            $manager->persist($news);
        }

        $manager->flush();
    }
}
