<?php
namespace bdrem;

/**
 * Fetch data from an SQL database
 */
class Source_Sql
{
    protected $dsn;
    protected $user;
    protected $password;
    protected $table;
    protected $fields ;

    public function __construct($config)
    {
        $this->dsn      = $config['dsn'];
        $this->user     = $config['user'];
        $this->password = $config['password'];
        $this->table    = $config['table'];
        $this->fields   = $config['fields'];
    }

    /**
     * @param string $strDate Date the events shall be found for, YYYY-MM-DD
     */
    public function getEvents($strDate, $nDaysBefore, $nDaysAfter)
    {
        $dbh = new \PDO($this->dsn, $this->user, $this->password);
        $arDays = $this->getDates($strDate, $nDaysBefore, $nDaysAfter);
        $arEvents = array();

        foreach ($this->fields['date'] as $field => $typeName) {
            $fieldSql = 'CONCAT('
                . 'EXTRACT(MONTH FROM ' . $field . '),'
                . '"-",'
                . 'EXTRACT(DAY FROM ' . $field . ')'
                . ') = ';

            $parts = array();
            foreach ($arDays as $day) {
                $parts[] = $fieldSql . $dbh->quote($day);
            }
            $sql = 'SELECT ' . $field . ' AS e_date'
                . ', ' . $this->fields['name'] . ' AS e_name'
                . ' FROM ' . $this->table
                . ' WHERE '
                . implode(' OR ', $parts);

            $res = $dbh->query($sql);
            if ($res === false) {
                $errorInfo = $dbh->errorInfo();
                throw new \Exception(
                    'SQL error #' . $errorInfo[0]
                    . ': ' . $errorInfo[1]
                    . ': ' . $errorInfo[2],
                    (int) $errorInfo[1]
                );
            }
            while ($row = $res->fetchObject()) {
                $event = new Event(
                    $row->e_name, $typeName, 
                    str_replace('0000', '????', $row->e_date)
                );
                if ($event->isWithin($strDate, $nDaysBefore, $nDaysAfter)) {
                    $arEvents[] = $event;
                }
            }
        }
        return $arEvents;
    }

    protected function getDates($strDate, $nDaysBefore, $nDaysAfter)
    {
        $ts = strtotime($strDate) - 86400 * $nDaysBefore;
        $numDays = $nDaysBefore + $nDaysAfter;

        $arDays = array();
        do {
            $arDays[] = date('n-j', $ts);
            $ts += 86400;
        } while (--$numDays >= 0);
        return $arDays;
    }
}
?>
