<?php namespace ILIAS\GlobalScreen\Identification\Serializer;

use ILIAS\GlobalScreen\Identification\IdentificationInterface;
use ILIAS\GlobalScreen\Identification\Map\IdentificationMap;
use ILIAS\GlobalScreen\Provider\ProviderFactory;

/**
 * Interface SerializerInterface
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
interface SerializerInterface
{
    public const MAX_LENGTH = 255;
    
    /**
     * The string MUST be shorter than 64 characters
     * @param IdentificationInterface $identification
     * @return string
     * @throws \LogicException whn longer than 64 characters
     */
    public function serialize(IdentificationInterface $identification) : string;
    
    /**
     * @param string            $serialized_string
     * @param IdentificationMap $map
     * @param ProviderFactory   $provider_factory
     * @return IdentificationInterface
     */
    public function unserialize(string $serialized_string, IdentificationMap $map, ProviderFactory $provider_factory) : IdentificationInterface;
    
    /**
     * @param string $serialized_identification
     * @return bool
     */
    public function canHandle(string $serialized_identification) : bool;
}
