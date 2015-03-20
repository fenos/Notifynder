<?php
/**
 * Created by Fabrizio Fenoglio.
 */
class NotificationDataBuilder
{

    /**
     * @var int
     */
    protected static $times;

    public function __construct($times = null)
    {
        self::$times = $times;
    }

    /**
     * @param $times
     * @return $this
     */
    public static function times($times)
    {
        return new self($times);
    }

    /**
     * Data for a single notification
     *
     * @return array
     */
    public static function singleNotificationData()
    {
        return self::dataNotification(
                    rand(10, 30),
                    str_random(12),
                    rand(31, 42),
                    str_random(12),
                    rand(43, 60),
                    str_random(24),
                    str_random(12)
                );
    }

    /**
     * @param        $from_id
     * @param        $from_type
     * @param        $to_id
     * @param        $to_type
     * @param        $category_id
     * @param        $url
     * @param  null  $extra
     * @return array
     */
    public static function dataNotification($from_id, $from_type, $to_id, $to_type, $category_id, $url, $extra = null)
    {
        $times = self::$times;

        $data = [];

        $date = new \DateTime();

        if (!is_null($times)) {
            for ($i = 0; $i <= $times; $i++) {
                $data[$i] = [
                    'from_id'     => $from_id,
                    'from_type'   => $from_type,
                    'to_id'       => $to_id,
                    'to_type'     => $to_type,
                    'category_id' => $category_id,
                    'url'         => $url,
                    'read'        => 0,
                    'extra'       => $extra,
                    'created_at'  => $date,
                    'updated_at'  => $date,
                ];
            }

            return $data;
        }

        return [
            'from_id'     => $from_id,
            'from_type'   => $from_type,
            'to_id'       => $to_id,
            'to_type'     => $to_type,
            'category_id' => $category_id,
            'url'         => $url,
            'read'        => 0,
            'extra'       => $extra,
            'created_at'  => $date,
            'updated_at'  => $date,
        ];
    }

    /**
     * Data for multiple notification
     *
     * @param  int   $times
     * @return array
     */
    public static function multipleNotificationData($times = 3)
    {
        $array = self::times($times)->dataNotification(
            rand(10, 30),
            str_random(12),
            rand(31, 42),
            str_random(12),
            rand(43, 60),
            str_random(20),
            str_random(12)
        );

        self::$times = null;

        return $array;
    }
}
