<?php

namespace DnD\Bundle\MagentoConnectorBundle\Reader\ORM;

use Pim\Bundle\BaseConnectorBundle\Reader\Doctrine\Reader;
use Pim\Bundle\CatalogBundle\Entity\Repository\AttributeRepository;

/**
 *
 * @author    DnD Mimosa <mimosa@dnd.fr>
 * @copyright Agence Dn'D (http://www.dnd.fr)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AttributeReader extends Reader
{


    /**
     * @var string
     */
    protected $excludedAttributes;

    /**
     * @param AttributeRepository $attributeRepository
     * @param UserContext $userContext
     */
    public function __construct(AttributeRepository $attributeRepository, UserContext $userContext)
    {
        $this->attributeRepository = $attributeRepository;
        $this->userContext = $userContext;
    }

    /**
     * get excludedAttributes
     *
     * @return string excludedAttributes
     */
    public function getExcludedAttributes()
    {
        return $this->excludedAttributes;
    }

    /**
     * Set excludedAttributes
     *
     * @param string $excludedAttributes excludedAttributes
     *
     * @return AbstractProcessor
     */
    public function setExcludedAttributes($excludedAttributes)
    {
        $this->excludedAttributes = $excludedAttributes;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        if (!$this->query) {
            $qb = $this->attributeRepository->createQueryBuilder('a');

            if($this->getExcludedAttributes() != ''){
                $attributes = explode(',', $this->getExcludedAttributes());
                $i = 0;
                foreach($attributes as $attr){
                    if($i == 0){
                        $qb->where(
                            $qb->expr()->orX(
                                $qb->expr()->neq('a.code', ':code'.$i)
                            )
                        );
                        $qb->setParameter('code'.$i, $attr);
                    }else{
                        $qb->andWhere(
                            $qb->expr()->orX(
                                $qb->expr()->neq('a.code', ':code'.$i)
                            )
                        );
                        $qb->setParameter('code'.$i, $attr);
                    }
                    $i++;
                }

            }

            $this->query = $qb->getQuery();
        }

        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFields()
    {
        return array(
            'excludedAttributes' => array(
                'options' => array(
                    'required' => false,
                    'label'    => 'dnd_magento_connector.export.excludedAttributes.label',
                    'help'     => 'dnd_magento_connector.export.excludedAttributes.help'
                )
            )
        );
    }
}
