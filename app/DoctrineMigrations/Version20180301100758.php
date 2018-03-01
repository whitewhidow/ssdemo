<?php

namespace Application\Migrations;

use AppBundle\Entity\User;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180301100758 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sampleUser = new User();
        $sampleUser->setName("Bill Gates");
        $sampleUser->setEmail("bill@microsoft.com");
        $sampleUser->setCompany("Microsoft");
        $this->container->get('doctrine')->getManager()->persist($sampleUser);

        $sampleUser = new User();
        $sampleUser->setName("Steve Jobs");
        $sampleUser->setEmail("steve@apple.com");
        $sampleUser->setCompany("Apple");
        $this->container->get('doctrine')->getManager()->persist($sampleUser);

        $sampleUser = new User();
        $sampleUser->setName("William Hewlit");
        $sampleUser->setEmail("william@hp.com");
        $sampleUser->setCompany("HP");
        $this->container->get('doctrine')->getManager()->persist($sampleUser);

        $sampleUser = new User();
        $sampleUser->setName("David Packard");
        $sampleUser->setEmail("david@hp.com");
        $sampleUser->setCompany("HP");
        $this->container->get('doctrine')->getManager()->persist($sampleUser);

        $sampleUser = new User();
        $sampleUser->setName("Elon Musk");
        $sampleUser->setEmail("elon@tesla.com");
        $sampleUser->setCompany("Tesla");
        $this->container->get('doctrine')->getManager()->persist($sampleUser);

        $this->container->get('doctrine')->getManager()->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
