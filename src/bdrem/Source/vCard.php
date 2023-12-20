<?php
/**
 * Part of bdrem
 *
 * PHP version 7
 *
 * @category  Tools
 * @package   Bdrem
 * @author    Christian Weiske <cweiske@cweiske.de>
 * @copyright 2023 Christian Weiske
 * @license   http://www.gnu.org/licenses/agpl.html GNU AGPL v3
 * @link      http://cweiske.de/bdrem.htm
 */
namespace bdrem;

use Sabre\VObject;
use Sabre\VObject\Component\VCard;

/**
 * Read a folder of vcard files
 * 2 subfolder levels are supported.
 *
 * @category  Tools
 * @package   Bdrem
 * @author    Christian Weiske <cweiske@cweiske.de>
 * @copyright 2023 Christian Weiske
 * @license   http://www.gnu.org/licenses/agpl.html GNU AGPL v3
 * @link      http://cweiske.de/bdrem.htm
 */
class Source_vCard
{
    /**
     * Full path to a folder with .vcf files
     */
    protected string $folder;

    /**
     * Set the VCard folder path
     */
    public function __construct(array $config)
    {
        $this->folder = $config['folder'];
        if (!is_dir($this->folder)) {
            throw new \Exception(
                'VCard folder does not exist: ' . $this->folder
            );
        }
    }

    /**
     * Return all events for the given date range
     *
     * @param string  $strDate       Date the events shall be found for,
     *                               YYYY-MM-DD
     * @param integer $nDaysPrevious Include number of days before $strDate
     * @param integer $nDaysNext     Include number of days after $strDate
     *
     * @return Event[] Array of matching event objects
     */
    public function getEvents($strDate, $nDaysPrevious, $nDaysNext)
    {
        $vcfFiles = glob($this->folder . '/{*,*/*,*/*/*}.vcf', GLOB_BRACE);
        if (count($vcfFiles) == 0) {
            throw new \Exception('No .vcf files found in folder');
        }

        $arEvents = [];
        foreach ($vcfFiles as $vcfFile) {
            $vcard = VObject\Reader::read(file_get_contents($vcfFile));

            if (isset($vcard->BDAY)) {
                $event = new Event(
                    $this->getName($vcard),
                    'Birthday',
                    $vcard->BDAY->getDateTime()->format('Y-m-d')
                );
                if ($event->isWithin($strDate, $nDaysPrevious, $nDaysNext)) {
                    $arEvents[] = $event;
                }
            }

            if (isset($vcard->{'X-ANNIVERSARY'})) {
                $event = new Event(
                    $this->getName($vcard),
                    'Anniversary',
                    $vcard->{'X-ANNIVERSARY'}->getDateTime()->format('Y-m-d')
                );
                if ($event->isWithin($strDate, $nDaysPrevious, $nDaysNext)) {
                    $arEvents[] = $event;
                }
            }
        }

        return $arEvents;
    }

    protected function getName(VCard $vcard)
    {
        return (string) $vcard->FN;
    }
}
?>
