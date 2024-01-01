<?php

class CryptoManager {
    private $db;

    public function __construct($host, $dbname, $username, $password) {
        try {
            $this->db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    public function importCryptoData() {
        // Remplacez 'VOTRE_CLE_API' par votre clé d'API CoinMarketCap
        $config = include './config.php';
        $api_key = $config['api_key'];
        $api_url = "https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest?CMC_PRO_API_KEY=$api_key";

        try {
            // Effectuez la demande à l'API
            $crypto_data = json_decode(file_get_contents($api_url), true);

            // Insérez les données dans la base de données
            foreach ($crypto_data['data'] as $crypto) {
                $name = $crypto['name'] ?? 'N/A';
                $symbol = $crypto['symbol'] ?? 'N/A';
                $slug = $crypto['slug'] ?? 'N/A';
                $max_supply = $crypto['max_supply'] ?? null; // null si non disponible

                // Remplacez 'cryptomonnaies' par le nom de votre table
                $stmt = $this->db->prepare("INSERT INTO cryptomonnaies (Nom, Symbole, Slug, MaxSupply) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $symbol, $slug, $max_supply]);
            }

            echo "Données importées avec succès.";

        } catch (Exception $e) {
            echo "Une erreur s'est produite : " . $e->getMessage();
        }
    }
}

// Remplacez ces informations par les paramètres de votre base de données
$host = 'localhost';
$dbname = 'crypto';
$username = 'root';
$password = '';

// Instanciez la classe CryptoManager
$cryptoManager = new CryptoManager($host, $dbname, $username, $password);

// Appelez la méthode pour importer les données
$cryptoManager->importCryptoData();
?>
