<?php

namespace AFUP\HaphpyBirthdayBundle\ParamConverter;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * PublicContributionParamConverter
 *
 * Retrieve a contribution based on authProvider id and identifier
 */
class PublicContributionParamConverter implements ParamConverterInterface
{
    /**
     * List of Authentification Providers
     *
     * @var []
     */
    private $authProviders = [
        'f' => 'facebook',
        'g' => 'github',
        't' => 'twitter',
    ];

    /**
     * activate ParamConverter for project
     *
     * @param ParamConverter $configuration
     *
     * @return boolean
     */
    public function supports(ParamConverter $configuration)
    {
        return true;
    }

    /**
     * ParamConverter itself
     *
     * @param Request        $request
     * @param ParamConverter $configuration
     *
     * @throws NotFoundHttpException if Project not found
     *
     * @return boolean
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $identifier   = $request->attributes->get('identifier');
        $authProvider = $request->attributes->get('authProvider');
        $authProvider = $this->authProviders[$authProvider];

        $contribution = $this->entityManager
                        ->getRepository('AFUP\HaphpyBirthdayBundle\Entity\Contribution')
                        ->findOneContributionByContributor(
                            $authProvider,
                            $identifier
                        );

        if (null === $contribution) {
            throw new NotFoundHttpException(sprintf('contribution from %s [%s] not found.', $identifier, $authProvider));
        }

        $request->attributes->set('contribution', $contribution);

        return true;
    }

    /**
     * set the entity manager depending on doctrine for now :(
     *
     * @param Registry $doctrine ORM service
     */
    public function setEntityManager(Registry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
    }
}
