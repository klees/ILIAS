<?php declare(strict_types=1);
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class ilTermsOfServiceDataGatewayFactory
 * @author Michael Jansen <mjansen@databay.de>
 */
class ilTermsOfServiceDataGatewayFactory
{
    protected ?ilDBInterface $db = null;

    public function setDatabaseAdapter(?ilDBInterface $db) : void
    {
        $this->db = $db;
    }

    public function getDatabaseAdapter() : ?ilDBInterface
    {
        return $this->db;
    }

    /**
     * @param string $name
     * @return ilTermsOfServiceAcceptanceDatabaseGateway
     * @throws InvalidArgumentException
     * @throws ilTermsOfServiceMissingDatabaseAdapterException
     */
    public function getByName(string $name) : ilTermsOfServiceAcceptanceDataGateway
    {
        if (null === $this->db) {
            throw new ilTermsOfServiceMissingDatabaseAdapterException('Incomplete factory configuration. Please inject a database adapter.');
        }

        switch (strtolower($name)) {
            case 'iltermsofserviceacceptancedatabasegateway':
                return new ilTermsOfServiceAcceptanceDatabaseGateway($this->db);

            default:
                throw new InvalidArgumentException('Data gateway not supported');
        }
    }
}
