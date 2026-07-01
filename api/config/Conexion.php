<?php

/**
 * ============================================================
 * SECTION: Conexion a base de datos
 * ============================================================
 *
 * Clase responsable de crear una conexion PDO hacia MySQL.
 *
 * La configuracion se lee desde el archivo `.env` ubicado en la
 * raiz del proyecto. Si el archivo no existe, se usan valores por
 * defecto compatibles con XAMPP local.
 */
class Conexion
{
    private $host;
    private $port;
    private $dbname;
    private $user;
    private $password;
    private $charset;

    /**
     * Carga variables de entorno y define los datos de conexion.
     */
    public function __construct()
    {
        $this->loadEnv(__DIR__ . '/../../.env');

        $this->host = $this->env('DB_HOST', 'localhost');
        $this->port = $this->env('DB_PORT', '3306');
        $this->dbname = $this->env('DB_NAME', 'multimedios_tickets');
        $this->user = $this->env('DB_USER', 'root');
        $this->password = $this->env('DB_PASSWORD', '');
        $this->charset = $this->env('DB_CHARSET', 'utf8mb4');
    }

    /**
     * Abre la conexion PDO usada por los DAO.
     *
     * @return PDO Conexion activa con MySQL.
     */
    public function Conectar()
    {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
            $conexion = new PDO($dsn, $this->user, $this->password);
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conexion;
        } catch (PDOException $th) {
            http_response_code(500);
            die(json_encode([
                "error" => "No se pudo conectar a la base de datos",
                "detalle" => $th->getMessage()
            ]));
        }
    }

    /**
     * Lee el archivo `.env` y registra sus variables en PHP.
     *
     * @param string $path Ruta absoluta del archivo `.env`.
     */
    private function loadEnv($path)
    {
        if (!file_exists($path)) {
            return;
        }

        $vars = parse_ini_file($path, false, INI_SCANNER_RAW);
        if ($vars === false) {
            return;
        }

        foreach ($vars as $key => $value) {
            if (getenv($key) === false) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }

    /**
     * Obtiene una variable de entorno con valor por defecto.
     *
     * @param string $key Nombre de la variable.
     * @param mixed $default Valor usado si la variable no existe.
     * @return mixed Valor de entorno o valor por defecto.
     */
    private function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        return $value;
    }
}
