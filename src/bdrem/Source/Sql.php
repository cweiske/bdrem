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
    public function getEvents($strDate, $nDaysPrevious, $nDaysNext)
    {
        $dbh = new \PDO(
            $this->dsn, $this->user, $this->password,
            array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION)
        );
        $arDays = $this->getDates($strDate, $nDaysPrevious, $nDaysNext);
        $arEvents = array();

        foreach ($this->fields['date'] as $field => $typeName) {
            if (substr($this->dsn, 0, 5) == 'dblib') {
                //MS SQL Server does of course cook its own sht
                $sqlMonth = 'DATEPART(month, ' . $field . ')';
                $sqlDay = 'DATEPART(day, ' . $field . ')';
            } else {
                $sqlMonth = 'EXTRACT(MONTH FROM ' . $field . ')';
                $sqlDay = 'EXTRACT(DAY FROM ' . $field . ')';
            }

            $parts = array();
            foreach ($arDays as $month => $days) {
                $parts[] = '('
                    . $sqlMonth . ' = ' . $dbh->quote($month, \PDO::PARAM_INT)
                    . ' AND ' . $sqlDay . ' >= '
                    . $dbh->quote(min($days), \PDO::PARAM_INT)
                    . ' AND ' . $sqlDay . ' <= '
                    . $dbh->quote(max($days), \PDO::PARAM_INT)
                    . ')';
            }
            $sql = 'SELECT ' . $field . ' AS e_date'
                . ', ' . implode(', ', (array) $this->fields['name'])
                . ' FROM ' . $this->table
                . ' WHERE '
                . implode(' OR ', $parts);

            $res = $dbh->query($sql);
            while ($row = $res->fetchObject()) {
                $arNameData = array();
                foreach ((array) $this->fields['name'] as $fieldName) {
                    $arNameData[] = $row->$fieldName;
                }
                $name = call_user_func_array(
                    'sprintf',
                    array_merge(
                        array($this->fields['nameFormat']),
                        $arNameData
                    )
                );
                $event = new Event(
                    $name, $typeName,
                    str_replace('0000', '????', $row->e_date)
                );
                if ($event->isWithin($strDate, $nDaysPrevious, $nDaysNext)) {
                    $arEvents[] = $event;
                }
            }
        }
        return $arEvents;
    }

    /**
     * @return array Key is the month, value an array of days
     */
    protected function getDates($strDate, $nDaysPrevious, $nDaysNext)
    {
        $ts = strtotime($strDate) - 86400 * $nDaysPrevious;
        $numDays = $nDaysPrevious + $nDaysNext;

        $arDays = array();
        do {
            $arDays[(int) date('n', $ts)][] = (int) date('j', $ts);
            $ts += 86400;
        } while (--$numDays >= 0);
        return $arDays;
    }
}
?>
