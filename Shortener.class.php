<?php

class Shortener
{
    protected static $chars = "abcdfghjkmnpqrstvwxyz|ABCDFGHJKLMNPQRSTVWXYZ|0123456789";
    protected static $table = "short_urls";
    protected static $checkUrlExists = false;
    protected static $codeLength = 7;

    protected $pdo;
    protected $timestamp;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->timestamp = date("Y-m-d H:i:s");
    }

    public function urlToShortCode($url)
    {
        if (empty($url)) {
            throw new Exception("No URL was supplied.");
        }

        if ($this->validateUrlFormat($url) == false) {
            throw new Exception("URL does not have a valid format.");
        }

        if (self::$checkUrlExists) {
            if (!$this->verifyUrlExists($url)) {
                throw new Exception("URL does not appear to exist.");
            }
        }

        $shortCode = $this->urlExistsInDB($url);
        if ($shortCode == false) {
            $shortCode = $this->createShortCode($url);
        }

        return $shortCode;
    }

    protected function validateUrlFormat($url)
    {
        // Utilizar el valor numérico 64 en lugar de la constante FILTER_FLAG_HOST_REQUIRED
        return filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
    }


    protected function verifyUrlExists($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return (!empty($response) && $response != 404);
    }

    protected function urlExistsInDB($url)
    {
        $query = "SELECT short_code FROM " . self::$table . " WHERE long_url = :long_url LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $params = array(
            "long_url" => $url
        );
        $stmt->execute($params);

        $result = $stmt->fetch();
        return (empty($result)) ? false : $result["short_code"];
    }

    protected function createShortCode($url)
    {
        $shortCode = $this->generateRandomString(self::$codeLength);
        $id = $this->insertUrlInDB($url, $shortCode);
        return $shortCode;
    }

    protected function generateRandomString($length = 6)
    {
        $sets = explode('|', self::$chars);
        $all = '';
        $randString = '';
        foreach ($sets as $set) {
            $randString .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++) {
            $randString .= $all[array_rand($all)];
        }
        $randString = str_shuffle($randString);
        return $randString;
    }

    protected function insertUrlInDB($url, $code)
    {
        $query = "INSERT INTO " . self::$table . " (long_url, short_code, created) VALUES (:long_url, :short_code, :timestamp)";
        $stmnt = $this->pdo->prepare($query);
        $params = array(
            "long_url" => $url,
            "short_code" => $code,
            "timestamp" => $this->timestamp
        );
        $stmnt->execute($params);

        return $this->pdo->lastInsertId();
    }

    public function shortCodeToUrl($code, $increment = true)
    {
        if (empty($code)) {
            throw new Exception("No short code was supplied.");
        }

        if ($this->validateShortCode($code) == false) {
            throw new Exception("Short code does not have a valid format.");
        }

        $urlRow = $this->getUrlFromDB($code);
        if (empty($urlRow)) {
            throw new Exception("Short code does not appear to exist.");
        }

        if ($increment == true) {
            $this->incrementCounter($urlRow["id"]);
        }

        return $urlRow["long_url"];
    }

    protected function validateShortCode($code)
    {
        $rawChars = str_replace('|', '', self::$chars);
        return preg_match("|[" . $rawChars . "]+|", $code);
    }

    public function getUrlFromDB($code)
    {
        $query = "SELECT * FROM " . self::$table . " WHERE short_code = :short_code LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $params = array(
            "short_code" => $code
        );
        $stmt->execute($params);

        $result = $stmt->fetch();
        return (empty($result)) ? false : $result;
    }

    protected function incrementCounter($id)
    {
        $visitorIP = $_SERVER['REMOTE_ADDR'];
        $visitorUserAgent = $_SERVER['HTTP_USER_AGENT'];
        $visitTimestamp = date("Y-m-d H:i:s");
        $visitorLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 9) : '';
        $visitorReferrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $visitorPage = isset($_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']) ? $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : '';
        // Realizar la solicitud a la API de geolocalización
        $apiUrl = 'http://ip-api.com/json/' . $visitorIP . '?fields=status,message,continent,continentCode,country,countryCode,region,regionName,city,district,zip,lat,lon,timezone,offset,currency,isp,org,as,asname,reverse,mobile,proxy,hosting,query';
        $response = file_get_contents($apiUrl);

        // Decodificar la respuesta JSON en un arreglo asociativo
        $visitorGeoData = json_decode($response, true);

        // Almacenar la información de ubicación geográfica en formato JSON en la columna 'visitor_geo'
        $visitorGeoJSON = json_encode($visitorGeoData);
        // Si la API no pudo obtener la ubicación o hubo un error, establecemos el valor a null
        if ($visitorGeoJSON === false || $visitorGeoData['status'] !== 'success') {
            $visitorGeoJSON = null;
        }

        $query = "UPDATE " . self::$table . " SET hits = hits + 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $params = array(
            "id" => $id
        );
        $stmt->execute($params);


        // Almacenar los datos del visitante en la tabla 'visitors' incluyendo 'visitor_geo'
        $query = "INSERT INTO visitors (short_url_id, visitor_ip, visitor_user_agent, visit_timestamp, visitor_language, visitor_referrer, visitor_page, visitor_geo) VALUES (:short_url_id, :visitor_ip, :visitor_user_agent, :visit_timestamp, :visitor_language, :visitor_referrer, :visitor_page, :visitor_geo)";
        $stmt = $this->pdo->prepare($query);
        $params = array(
            "short_url_id" => $id,
            "visitor_ip" => $visitorIP,
            "visitor_user_agent" => $visitorUserAgent,
            "visit_timestamp" => $visitTimestamp,
            "visitor_language" => $visitorLanguage,
            "visitor_referrer" => $visitorReferrer,
            "visitor_page" => $visitorPage,
            "visitor_geo" => $visitorGeoJSON // Almacenar la ubicación geográfica en formato JSON
        );
        $stmt->execute($params);
    }

    // Agrega el siguiente método en la clase Shortener
    public function fetchAllUrls()
    {
        $query = "SELECT * FROM " . self::$table;
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getHits($code)
    {
        $urlData = $this->getUrlFromDB($code);
        if ($urlData) {
            return $urlData['hits'];
        } else {
            throw new Exception("El enlace acortado no existe o el código no es válido.");
        }
    }


    public function getVisitors($linkID)
    {
        // Consultar la base de datos para obtener todos los visitantes del enlace acortado
        $query = "SELECT * FROM visitors WHERE short_url_id = :link_id";
        $stmt = $this->pdo->prepare($query);
        $params = array(
            "link_id" => $linkID
        );
        $stmt->execute($params);

        // Obtener todos los datos de los visitantes y decodificar el JSON de visitor_geo
        $visitorsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($visitorsData as &$visitor) {
            $visitor['visitor_geo'] = json_decode($visitor['visitor_geo'], true);
        }

        return $visitorsData;
    }
}
