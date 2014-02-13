<?php
namespace bdrem;

/**
 * Fetch data from an LDAP server.
 * Works fine with evolutionPerson schema.
 */
class Source_Ldap
{
    protected $config;

    /**
     * Create new ldap source
     *
     * @param array $config Array of Net_LDAP2 configuration parameters.
     *                      Some of those you might want to use:
     *                      - host   - LDAP server host name
     *                      - basedn - root DN that gets searched
     *                      - binddn - Username to authenticate with
     *                      - bindpw - Password for username
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param string $strDate Date the events shall be found for, YYYY-MM-DD
     */
    public function getEvents($strDate, $nDaysPrevious, $nDaysNext)
    {
        //Net_LDAP2 is not E_STRICT compatible
        error_reporting(error_reporting() & ~E_STRICT);

        $ldap = \Net_LDAP2::connect($this->config);
        if (\PEAR::isError($ldap)) {
            throw new \Exception(
                'Could not connect to LDAP-server: ' . $ldap->getMessage()
            );
        }

        $dateAttributes = array(
            'birthDate'   => 'Birthday',
            'anniversary' => 'Anniversary',
        );

        $arDays   = $this->getDates($strDate, $nDaysPrevious, $nDaysNext);
        $arEvents = array();

        foreach ($dateAttributes as $dateAttribute => $attributeTitle) {
            $filters = array();
            foreach ($arDays as $day) {
                $filters[] = \Net_LDAP2_Filter::create($dateAttribute, 'ends', $day);
            }

            if (count($filters) < 2) {
                $filter = $filters[0];
            } else {
                $filter = \Net_LDAP2_Filter::combine('or', $filters);
            }
            $options = array(
                'scope'      => 'sub',
                'attributes' => array(
                    'displayName', 'givenName', 'sn', 'cn', $dateAttribute
                )
            );

            $search = $ldap->search(null, $filter, $options);
            if (!$search instanceof \Net_LDAP2_Search) {
                throw new \Exception(
                    'Error searching LDAP: ' . $search->getMessage()
                );
            } else if ($search->count() == 0) {
                continue;
            }

            while ($entry = $search->shiftEntry()) {
                $event = new Event(
                    $this->getNameFromEntry($entry),
                    $attributeTitle,
                    $entry->getValue($dateAttribute, 'single')
                );
                if ($event->isWithin($strDate, $nDaysPrevious, $nDaysNext)) {
                    $arEvents[] = $event;
                }
            }
        }

        return $arEvents;
    }

    protected function getNameFromEntry(\Net_LDAP2_Entry $entry)
    {
        $arEntry = $entry->getValues();
        if (isset($arEntry['displayName'])) {
            return $arEntry['displayName'];
        } else if (isset($arEntry['sn']) && isset($arEntry['givenName'])) {
            return $arEntry['givenName'] . ' ' . $arEntry['sn'];
        } else if (isset($arEntry['cn'])) {
            return $arEntry['cn'];
        }
        return null;
    }

    /**
     * @return array Values like "-01-24" ("-$month-$day")
     */
    protected function getDates($strDate, $nDaysPrevious, $nDaysNext)
    {
        $ts = strtotime($strDate) - 86400 * $nDaysPrevious;
        $numDays = $nDaysPrevious + $nDaysNext;

        $arDays = array();
        do {
            $arDays[] = date('-m-d', $ts);
            $ts += 86400;
        } while (--$numDays >= 0);
        return $arDays;
    }

}
?>
