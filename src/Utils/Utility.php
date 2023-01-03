<?php

namespace Joseph\AdtCleanUp\Utils;

use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Google\Client;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Umb\EventsManager\Models\County;
use Umb\EventsManager\Models\Facility;
use Illuminate\Database\Capsule\Manager as DB;

class Utility
{

    /***
     * Checks for missing attributes
     * @param array $data
     * @param array $attributes
     *
     * @return array - an array of missing attrs
     */
    public static function checkMissingAttributes(array $data, array $attributes): array
    {
        $missingAttrs = [];
        foreach ($attributes as $attribute) {
            if (!isset($data[$attribute])) $missingAttrs[] = $attribute;
        }
        return $missingAttrs;
    }

    public static function logError($code, $message)
    {
        if (!is_dir($_ENV['LOGS_DIR'])) {
            mkdir($_ENV['LOGS_DIR']);
        }
        $today = date_format(date_create(), 'Ymd');
        $handle = fopen($_ENV['LOGS_DIR'] . "errors_" . $today . ".txt", 'a');
        $data = date("Y-m-d H:i:s ", time());
        $data .= "      Code " . $code;
        $data .= "      Message " . $message;
        $data .= "      ClientAddr " . $_SERVER["REMOTE_ADDR"];
        $data .= "\n";
        fwrite($handle, $data);
        fclose($handle);
    }

    public static function uploadFile($newName = '', $dir = null)
    {
        try {
            if (!is_dir($_ENV['PUBLIC_DIR'])) {
                mkdir($_ENV['PUBLIC_DIR']);
            }
            $uploadDir = $dir ?? $_ENV['PUBLIC_DIR'];
            // $uploadedFiles = '';
            $file_name = $_FILES['upload_file']['name'];
            $ext = substr($file_name, strrpos($file_name, '.'));
            $mF = ($newName == '' ? $file_name : $newName . $ext);
            $tmp_name = $_FILES['upload_file']['tmp_name'];
            $file_name = str_replace(" ", "_", $file_name);
            $file_name = str_replace("/", "_", $file_name);
            $file_name = str_replace(".", "_" . time() . ".", $file_name);
            $uploaded = move_uploaded_file($tmp_name, $uploadDir . $mF);
            if (!$uploaded) throw new \Exception("File not uploaded");
            /*
            foreach ($_FILES['upload_files']['name'] as $file_name) {
                $tmp_name = $_FILES['upload_files']['tmp_name'][$count];
                $file_name = str_replace(" ", "_", $file_name);
                $file_name = str_replace(".", "_" . time() . ".", $file_name);
                $uploaded = move_uploaded_file($tmp_name, $_ENV['PUBLIC_DIR'] . $file_name);
                if (!$uploaded) throw new \Exception("File not uploaded");
                if ($count == (sizeof($_FILES['upload_files']['tmp_name']) - 1)) {
                    $uploadedFiles .= $file_name;
                } else {
                    $uploadedFiles .= $file_name . ',';
                }
                $count++;
            }*/
            return $mF;
        } catch (\Throwable $th) {
            self::logError($th->getCode(), $th->getMessage());
            //            http_response_code(PRECONDITION_FAILED_ERROR_CODE);
            return null;
        }
    }


    /***
     * This function takes an integer number $num and generates a string similar to columns in a spreadsheet.
     * ie A, B, ...... AA, AB, ..... BA, and so on.
     * given a number 0 = A, 1 = B and so on...
     * @param int $num
     *
     *
     * @return String corresponding column string
     */
    public static function getColumnLabel(int $num): string
    {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return self::getColumnLabel($num2 - 1) . $letter;
        } else {
            return $letter;
        }
    }

    /*******
     * This function calculates the distance between two points A and B given the gps coordinates of the points
     * @param $pointA array of [lat, lon]
     * @param $pointB array of [lat, lon]
     *
     * @return double The distance.
     * */
    public static function getDistanceFromCoordinates(array $pointA, array $pointB)
    {
        $radius = 6378; // Radius of the earth in km
        $dLat = self::deg2rad($pointB[0] - $pointA[0]);
        $dLon = self::deg2rad($pointB[1] - $pointA[1]);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(self::deg2rad($pointA[0])) * cos(self::deg2rad($pointB[0])) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $radius * $c;
    }

    public static function deg2rad($deg)
    {
        return $deg * (pi() / 180);
    }
}
