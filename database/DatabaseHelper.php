<?php
class DatabaseHelper {
    private $db;

    public function __construct($servername, $username, $password, $dbname, $port) {
        $this->db = new mysqli($servername, $username, $password, $dbname, $port);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
        $this->db->set_charset("utf8mb4");
    }

    // ============================================================================
    // AUTH - Login
    // ============================================================================
    
    public function checkLogin($email, $password){
        $query = "SELECT user_id, email, nome, cognome, ruolo, stato FROM users WHERE stato = 'attivo' AND email = ? AND password_hash = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ss', $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // ============================================================================
    // DASHBOARD - KPI Stats
    // ============================================================================
    
    // Prenotazioni totali (tutte)
    public function getPrenotazioniOggi() {
        $query = "SELECT COUNT(*) as totale FROM prenotazioni";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    // Prenotazioni completate (per calcolo variazione)
    public function getPrenotazioniIeri() {
        $query = "SELECT COUNT(*) as totale FROM prenotazioni WHERE stato = 'completata'";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    // Prenotazioni del mese corrente (basato sui dati esistenti)
    public function getPrenotazioniSettimana() {
        $query = "SELECT COUNT(*) as totale FROM prenotazioni WHERE stato IN ('confermata', 'completata')";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    // Prenotazioni cancellate (per variazione)
    public function getPrenotazioniSettimanaScorsa() {
        $query = "SELECT COUNT(*) as totale FROM prenotazioni WHERE stato = 'cancellata'";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    // Utilizzo campi (percentuale media basata su prenotazioni reali)
    public function getUtilizzoCampi() {
        $query = "SELECT 
            (SELECT COUNT(*) FROM prenotazioni WHERE stato IN ('completata', 'confermata')) as prenotazioni,
            (SELECT COUNT(*) FROM campi_sportivi WHERE stato != 'chiuso') as campi_attivi";
        $result = $this->db->query($query);
        $row = $result->fetch_assoc();
        
        $prenotazioni = $row['prenotazioni'] ?? 0;
        $campi = $row['campi_attivi'] ?? 1;
        // Percentuale basata su 100 slot ideali per campo
        $maxSlot = 100 * $campi;
        
        return $maxSlot > 0 ? round(($prenotazioni / $maxSlot) * 100) : 0;
    }
    
    // Utenti attivi (tutti gli utenti che hanno fatto prenotazioni)
    public function getUtentiAttivi() {
        $query = "SELECT COUNT(DISTINCT user_id) as totale FROM prenotazioni";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    // Utenti totali registrati (per variazione)
    public function getUtentiAttiviMeseScorso() {
        $query = "SELECT COUNT(*) as totale FROM utenti_standard";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    // Campi in manutenzione
    public function getCampiManutenzione() {
        $query = "SELECT COUNT(*) as totale FROM campi_sportivi WHERE stato = 'manutenzione'";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    // ============================================================================
    // DASHBOARD - Alerts
    // ============================================================================
    
    // Segnalazioni pending
    public function getSegnalazioniPending() {
        $query = "SELECT COUNT(*) as totale FROM segnalazioni WHERE stato = 'pending'";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    // Campo con rating più basso
    public function getCampoRatingBasso() {
        $query = "SELECT c.campo_id, c.nome, ROUND(AVG(r.rating_generale), 1) as rating_medio
                  FROM campi_sportivi c
                  JOIN recensioni r ON c.campo_id = r.campo_id
                  GROUP BY c.campo_id
                  HAVING rating_medio < 4
                  ORDER BY rating_medio ASC
                  LIMIT 1";
        $result = $this->db->query($query);
        return $result->fetch_assoc();
    }
    
    // Notifiche non lette (per admin)
    public function getNotificheNonLette($userId) {
        $query = "SELECT COUNT(*) as totale FROM notifiche WHERE user_id = ? AND letta = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['totale'] ?? 0;
    }
    
    // ============================================================================
    // DASHBOARD - Trend Prenotazioni (Grafico)
    // ============================================================================
    
    public function getTrendPrenotazioni($giorni = 7) {
        // Raggruppa prenotazioni per giorno della settimana
        $query = "SELECT 
                    DAYOFWEEK(data_prenotazione) as giorno_settimana,
                    COUNT(*) as totale
                  FROM prenotazioni
                  GROUP BY DAYOFWEEK(data_prenotazione)
                  ORDER BY giorno_settimana ASC";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // DASHBOARD - Utilizzo Campi (Lista)
    // ============================================================================
    
    public function getUtilizzoCampiLista() {
        $query = "SELECT 
                    c.campo_id,
                    c.nome,
                    s.nome as sport,
                    COUNT(p.prenotazione_id) as prenotazioni,
                    ROUND((COUNT(p.prenotazione_id) / 20) * 100) as percentuale
                  FROM campi_sportivi c
                  JOIN sport s ON c.sport_id = s.sport_id
                  LEFT JOIN prenotazioni p ON c.campo_id = p.campo_id
                  WHERE c.stato != 'chiuso'
                  GROUP BY c.campo_id
                  ORDER BY prenotazioni DESC
                  LIMIT 6";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // DASHBOARD - Distribuzione Sport (Donut)
    // ============================================================================
    
    public function getDistribuzioneSport() {
        $query = "SELECT 
                    s.nome as sport,
                    s.icona,
                    COUNT(p.prenotazione_id) as prenotazioni,
                    COUNT(p.prenotazione_id) as ore
                  FROM sport s
                  JOIN campi_sportivi c ON s.sport_id = c.sport_id
                  LEFT JOIN prenotazioni p ON c.campo_id = p.campo_id
                  GROUP BY s.sport_id
                  ORDER BY prenotazioni DESC";
        $result = $this->db->query($query);
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        // Calcola percentuali
        $totale = array_sum(array_column($data, 'prenotazioni'));
        foreach ($data as &$row) {
            $row['percentuale'] = $totale > 0 ? round(($row['prenotazioni'] / $totale) * 100) : 0;
        }
        
        return $data;
    }
    
    // ============================================================================
    // DASHBOARD - Attività Recenti
    // ============================================================================
    
    public function getAttivitaRecenti($limit = 5) {
        // Union di prenotazioni, recensioni e segnalazioni recenti
        $query = "(SELECT 
                    'booking' as tipo,
                    CONCAT(u.nome, ' ', u.cognome) as utente,
                    CONCAT(UPPER(LEFT(u.nome, 1)), UPPER(LEFT(u.cognome, 1))) as avatar,
                    'Nuova prenotazione' as azione,
                    c.nome as dettaglio,
                    p.created_at as data
                  FROM prenotazioni p
                  JOIN users u ON p.user_id = u.user_id
                  JOIN campi_sportivi c ON p.campo_id = c.campo_id
                  ORDER BY p.created_at DESC
                  LIMIT 10)
                  
                  UNION ALL
                  
                  (SELECT 
                    'review' as tipo,
                    CONCAT(u.nome, ' ', u.cognome) as utente,
                    CONCAT(UPPER(LEFT(u.nome, 1)), UPPER(LEFT(u.cognome, 1))) as avatar,
                    CONCAT('Recensione ', r.rating_generale, '★') as azione,
                    c.nome as dettaglio,
                    r.created_at as data
                  FROM recensioni r
                  JOIN users u ON r.user_id = u.user_id
                  JOIN campi_sportivi c ON r.campo_id = c.campo_id
                  ORDER BY r.created_at DESC
                  LIMIT 10)
                  
                  UNION ALL
                  
                  (SELECT 
                    'report' as tipo,
                    CONCAT(u.nome, ' ', u.cognome) as utente,
                    CONCAT(UPPER(LEFT(u.nome, 1)), UPPER(LEFT(u.cognome, 1))) as avatar,
                    'Nuova segnalazione' as azione,
                    s.tipo as dettaglio,
                    s.created_at as data
                  FROM segnalazioni s
                  JOIN users u ON s.user_segnalante_id = u.user_id
                  ORDER BY s.created_at DESC
                  LIMIT 10)
                  
                  ORDER BY data DESC
                  LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // HELPER - Calcola tempo relativo
    // ============================================================================
    
    public function tempoRelativo($datetime) {
        $now = new DateTime();
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        
        if ($diff->y > 0) return $diff->y . ' ann' . ($diff->y > 1 ? 'i' : 'o') . ' fa';
        if ($diff->m > 0) return $diff->m . ' mes' . ($diff->m > 1 ? 'i' : 'e') . ' fa';
        if ($diff->d > 0) return $diff->d . ' giorn' . ($diff->d > 1 ? 'i' : 'o') . ' fa';
        if ($diff->h > 0) return $diff->h . ' or' . ($diff->h > 1 ? 'e' : 'a') . ' fa';
        if ($diff->i > 0) return $diff->i . ' min fa';
        return 'ora';
    }

}
?>