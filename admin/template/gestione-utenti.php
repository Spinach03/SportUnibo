<?php
/**
 * GESTIONE UTENTI - Template
 * Campus Sports Arena - Admin Panel
 */

// Helper per colori ruolo
function getRoleConfig($ruolo) {
    $config = [
        'admin' => ['color' => '#8B5CF6', 'label' => 'Admin', 'icon' => '👑'],
        'user' => ['color' => '#3B82F6', 'label' => 'Utente', 'icon' => '👤']
    ];
    return $config[$ruolo] ?? $config['user'];
}

// Helper per colori stato
function getStatusConfig($stato) {
    $config = [
        'attivo' => ['color' => '#10B981', 'label' => 'Attivo', 'class' => 'green'],
        'sospeso' => ['color' => '#F59E0B', 'label' => 'Sospeso', 'class' => 'orange'],
        'bannato' => ['color' => '#EF4444', 'label' => 'Bannato', 'class' => 'red']
    ];
    return $config[$stato] ?? $config['attivo'];
}

// Helper per penalty level
function getPenaltyLevel($points) {
    if ($points == 0) return ['color' => '#10B981', 'label' => 'Nessuno', 'level' => 'none'];
    if ($points <= 2) return ['color' => '#3B82F6', 'label' => 'Basso', 'level' => 'low'];
    if ($points <= 5) return ['color' => '#F59E0B', 'label' => 'Medio', 'level' => 'medium'];
    return ['color' => '#EF4444', 'label' => 'Alto', 'level' => 'high'];
}

// Helper per iniziali
function getInitials($nome, $cognome) {
    return strtoupper(substr($nome, 0, 1) . substr($cognome, 0, 1));
}

include 'header.php';
?>

<!-- CSS Specifico -->
<link rel="stylesheet" href="css/gestione-utenti.css">
<link rel="stylesheet" href="css/modal-nuovo-campo.css">

<div class="admin-page gestione-utenti-page">
    
    <!-- ============================================================================
         HEADER SEZIONE
         ============================================================================ -->
    <div class="gestione-header">
        <span class="header-icon">👥</span>
        <div class="header-text">
            <h1 class="page-title mb-0"><?php echo $pageTitle; ?></h1>
            <p class="page-subtitle"><?php echo $pageSubtitle; ?></p>
        </div>
        
        <!-- Stats Cards Mini -->
        <div class="header-stats">
            <div class="stat-mini">
                <span class="stat-mini-value"><?php echo $statsGenerali['totale'] ?? 0; ?></span>
                <span class="stat-mini-label">Totale</span>
            </div>
            <div class="stat-mini">
                <span class="stat-mini-value text-success"><?php echo $statsGenerali['attivi'] ?? 0; ?></span>
                <span class="stat-mini-label">Attivi</span>
            </div>
            <div class="stat-mini">
                <span class="stat-mini-value text-warning"><?php echo $statsGenerali['sospesi'] ?? 0; ?></span>
                <span class="stat-mini-label">Sospesi</span>
            </div>
            <div class="stat-mini">
                <span class="stat-mini-value text-danger"><?php echo $statsGenerali['bannati'] ?? 0; ?></span>
                <span class="stat-mini-label">Bannati</span>
            </div>
        </div>
    </div>
    
    <!-- ============================================================================
         FILTRI E RICERCA
         ============================================================================ -->
    <div class="filters-section">
        <form method="GET" class="filters-form" id="filtersForm">
            <!-- Ricerca -->
            <div class="filter-search">
                <span class="search-icon">🔍</span>
                <input type="text" name="search" class="filter-input-search" 
                       placeholder="Cerca per nome o email..." 
                       value="<?php echo htmlspecialchars($filtri['search']); ?>">
            </div>
            
            <!-- Filtro Ruolo -->
            <div class="filter-group">
                <select name="ruolo" class="filter-select" onchange="this.form.submit()">
                    <option value="">Tutti i ruoli</option>
                    <option value="user" <?php echo $filtri['ruolo'] === 'user' ? 'selected' : ''; ?>>👤 Utente</option>
                    <option value="admin" <?php echo $filtri['ruolo'] === 'admin' ? 'selected' : ''; ?>>👑 Admin</option>
                </select>
            </div>
            
            <!-- Filtro Stato -->
            <div class="filter-group">
                <select name="stato" class="filter-select" onchange="this.form.submit()">
                    <option value="">Tutti gli stati</option>
                    <option value="attivo" <?php echo $filtri['stato'] === 'attivo' ? 'selected' : ''; ?>>🟢 Attivo</option>
                    <option value="sospeso" <?php echo $filtri['stato'] === 'sospeso' ? 'selected' : ''; ?>>🟡 Sospeso</option>
                    <option value="bannato" <?php echo $filtri['stato'] === 'bannato' ? 'selected' : ''; ?>>🔴 Bannato</option>
                </select>
            </div>
            
            <!-- Filtro Corso -->
            <div class="filter-group">
                <select name="corso" class="filter-select" onchange="this.form.submit()">
                    <option value="">Tutti i corsi</option>
                    <?php foreach ($corsi as $corso): ?>
                    <option value="<?php echo $corso['corso_id']; ?>" <?php echo $filtri['corso'] == $corso['corso_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($corso['nome']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Filtro Penalty -->
            <div class="filter-group">
                <select name="penalty_min" class="filter-select" onchange="this.form.submit()">
                    <option value="">Tutti i penalty</option>
                    <option value="1" <?php echo $filtri['penalty_min'] === '1' ? 'selected' : ''; ?>>⚠️ Con penalty (≥1)</option>
                    <option value="3" <?php echo $filtri['penalty_min'] === '3' ? 'selected' : ''; ?>>⚠️ Medio (≥3)</option>
                    <option value="5" <?php echo $filtri['penalty_min'] === '5' ? 'selected' : ''; ?>>🔴 Alto (≥5)</option>
                </select>
            </div>
            
            <!-- Ordinamento -->
            <div class="filter-group">
                <select name="ordina" class="filter-select" onchange="this.form.submit()">
                    <option value="nome" <?php echo $filtri['ordina'] === 'nome' ? 'selected' : ''; ?>>Nome A-Z</option>
                    <option value="recente" <?php echo $filtri['ordina'] === 'recente' ? 'selected' : ''; ?>>Più recenti</option>
                    <option value="attivita" <?php echo $filtri['ordina'] === 'attivita' ? 'selected' : ''; ?>>Più attivi</option>
                    <option value="penalty" <?php echo $filtri['ordina'] === 'penalty' ? 'selected' : ''; ?>>Più penalty</option>
                </select>
            </div>
            
            <!-- Reset -->
            <?php if (!empty(array_filter($filtri))): ?>
            <a href="gestione-utenti.php" class="btn-reset-filters">✕ Reset</a>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- ============================================================================
         GRIGLIA UTENTI
         ============================================================================ -->
    <div class="users-grid">
        <?php if (empty($users)): ?>
        <div class="no-results">
            <div class="no-results-icon">😕</div>
            <h3>Nessun utente trovato</h3>
            <p>Prova a modificare i filtri di ricerca</p>
        </div>
        <?php else: ?>
        
        <?php foreach ($users as $user): 
            $role = getRoleConfig($user['ruolo']);
            $status = getStatusConfig($user['stato']);
            $penalty = getPenaltyLevel($user['penalty_points'] ?? 0);
            $initials = getInitials($user['nome'], $user['cognome']);
            
            // Calcolo affidabilità (simulato)
            $affidabilita = 100 - (($user['penalty_points'] ?? 0) * 5) - (($user['no_show_count'] ?? 0) * 3);
            $affidabilita = max(0, min(100, $affidabilita));
        ?>
        <div class="user-card" data-user-id="<?php echo $user['user_id']; ?>" data-ruolo="<?php echo $user['ruolo']; ?>">
            <!-- Header Card -->
            <div class="user-card-header">
                <!-- Status Badge -->
                <div class="user-status <?php echo $status['class']; ?>">
                    <span class="status-indicator"></span>
                    <span class="status-text"><?php echo $status['label']; ?></span>
                </div>
                
                <!-- Avatar -->
                <div class="user-avatar" style="background: linear-gradient(135deg, <?php echo $role['color']; ?>, <?php echo $role['color']; ?>88);">
                    <?php echo $initials; ?>
                    <?php if ($user['ruolo'] === 'admin'): ?>
                    <span class="admin-badge">👑</span>
                    <?php endif; ?>
                </div>
                
                <!-- Nome -->
                <h3 class="user-name"><?php echo htmlspecialchars($user['nome'] . ' ' . $user['cognome']); ?></h3>
                
                <!-- Email -->
                <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                
                <!-- Corso -->
                <?php if (!empty($user['corso_nome'])): ?>
                <div class="user-corso">🎓 <?php echo htmlspecialchars($user['corso_nome']); ?></div>
                <?php endif; ?>
            </div>
            
            <!-- Body Card -->
            <div class="user-card-body">
                <!-- Stats Row -->
                <div class="user-stats-row">
                    <div class="user-stat">
                        <span class="stat-value"><?php echo $user['totale_prenotazioni'] ?? 0; ?></span>
                        <span class="stat-label">prenotazioni</span>
                    </div>
                    <div class="user-stat">
                        <span class="stat-value" style="color: <?php echo $penalty['color']; ?>"><?php echo $user['penalty_points'] ?? 0; ?></span>
                        <span class="stat-label">penalty</span>
                    </div>
                    <div class="user-stat">
                        <span class="stat-value highlight"><?php echo $affidabilita; ?>%</span>
                        <span class="stat-label">affidabilità</span>
                    </div>
                </div>
                
                <!-- Progress Bar Affidabilità -->
                <div class="user-progress-wrapper">
                    <div class="user-progress-bar" style="width: <?php echo $affidabilita; ?>%; background: <?php 
                        echo $affidabilita >= 80 ? '#10B981' : ($affidabilita >= 50 ? '#F59E0B' : '#EF4444'); 
                    ?>"></div>
                </div>
                
                <!-- Info Row -->
                <div class="user-info-row">
                    <span class="user-info-item">
                        <span class="info-icon"><?php echo $role['icon']; ?></span>
                        <?php echo $role['label']; ?>
                    </span>
                    <?php if (!empty($user['ultimo_accesso'])): ?>
                    <span class="user-info-item">
                        <span class="info-icon">🕐</span>
                        <?php echo date('d/m/Y', strtotime($user['ultimo_accesso'])); ?>
                    </span>
                    <?php endif; ?>
                </div>
                
                <!-- XP e Livello -->
                <div class="user-xp-row">
                    <span class="xp-badge">⭐ <?php echo $user['xp_points'] ?? 0; ?> XP</span>
                    <?php if (!empty($user['livello_nome'])): ?>
                    <span class="level-badge">🏅 <?php echo htmlspecialchars($user['livello_nome']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php endif; ?>
    </div>
</div>

<!-- ============================================================================
     MODAL: DETTAGLIO UTENTE
     ============================================================================ -->
<div class="modal fade" id="modalDettaglioUtente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-dark-custom">
            <!-- Header -->
            <div class="modal-header modal-header-user" id="modalUserHeader">
                <div class="d-flex align-items-center gap-3">
                    <div class="user-avatar-large" id="detailAvatar">AB</div>
                    <div>
                        <h5 class="modal-title mb-1" id="detailNome">Nome Utente</h5>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge-role" id="detailRuolo">👤 Utente</span>
                            <span class="text-muted" id="detailEmail">email@example.com</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="status-badge-modal" id="detailStatus">
                        <span class="status-dot-modal"></span>
                        <span class="status-text-modal">Attivo</span>
                    </div>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Chiudi">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Tabs -->
            <div class="modal-tabs-wrapper">
                <ul class="nav nav-tabs-custom" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link-custom active" data-bs-toggle="tab" data-bs-target="#tabInfo" type="button">
                            📋 Informazioni
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link-custom" data-bs-toggle="tab" data-bs-target="#tabStats" type="button">
                            📊 Statistiche
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link-custom" data-bs-toggle="tab" data-bs-target="#tabPenalty" type="button">
                            ⚠️ Penalty
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link-custom" data-bs-toggle="tab" data-bs-target="#tabSegnalazioni" type="button">
                            🚩 Segnalazioni
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link-custom" data-bs-toggle="tab" data-bs-target="#tabBadges" type="button">
                            🏆 Badge
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link-custom" data-bs-toggle="tab" data-bs-target="#tabAzioni" type="button">
                            ⚡ Azioni
                        </button>
                    </li>
                </ul>
            </div>
            
            <!-- Body -->
            <div class="modal-body modal-body-tabs">
                <div class="tab-content">
                    
                    <!-- Tab Informazioni -->
                    <div class="tab-pane fade show active" id="tabInfo">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h4 class="tab-section-title mb-3">📋 Dati Anagrafici</h4>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <span class="info-label">Nome completo</span>
                                        <span class="info-value" id="infoNomeCompleto">-</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Email</span>
                                        <span class="info-value" id="infoEmail">-</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Telefono</span>
                                        <span class="info-value" id="infoTelefono">-</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Data di nascita</span>
                                        <span class="info-value" id="infoDataNascita">-</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4 class="tab-section-title mb-3">🎓 Info Universitarie</h4>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <span class="info-label">Corso di laurea</span>
                                        <span class="info-value" id="infoCorso">-</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Facoltà</span>
                                        <span class="info-value" id="infoFacolta">-</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Anno iscrizione</span>
                                        <span class="info-value" id="infoAnnoIscrizione">-</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Registrato il</span>
                                        <span class="info-value" id="infoRegistrato">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Attività Recenti -->
                        <div class="mt-4">
                            <h4 class="tab-section-title mb-3">🕐 Attività Recenti</h4>
                            <div class="activity-timeline" id="activityTimeline">
                                <!-- Caricato via JS -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Statistiche -->
                    <div class="tab-pane fade" id="tabStats">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="stat-card-big">
                                    <div class="stat-card-icon">📅</div>
                                    <div class="stat-card-value" id="statPrenotazioni">0</div>
                                    <div class="stat-card-label">Prenotazioni Totali</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card-big">
                                    <div class="stat-card-icon">✅</div>
                                    <div class="stat-card-value" id="statCompletate">0</div>
                                    <div class="stat-card-label">Completate</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card-big">
                                    <div class="stat-card-icon">❌</div>
                                    <div class="stat-card-value" id="statNoShow">0</div>
                                    <div class="stat-card-label">No-Show</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-4 mt-2">
                            <div class="col-md-4">
                                <div class="stat-card-big">
                                    <div class="stat-card-icon">⏱️</div>
                                    <div class="stat-card-value" id="statOre">0</div>
                                    <div class="stat-card-label">Ore Giocate</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card-big">
                                    <div class="stat-card-icon">🏆</div>
                                    <div class="stat-card-value" id="statTornei">0</div>
                                    <div class="stat-card-label">Tornei Partecipati</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card-big">
                                    <div class="stat-card-icon">🥇</div>
                                    <div class="stat-card-value" id="statVittorie">0</div>
                                    <div class="stat-card-label">Tornei Vinti</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Affidabilità -->
                        <div class="mt-4">
                            <h4 class="tab-section-title mb-3">📈 Indice di Affidabilità</h4>
                            <div class="affidabilita-box">
                                <div class="affidabilita-score" id="affidabilitaScore">0%</div>
                                <div class="affidabilita-bar-wrapper">
                                    <div class="affidabilita-bar" id="affidabilitaBar" style="width: 0%"></div>
                                </div>
                                <div class="affidabilita-label" id="affidabilitaLabel">Calcolo in corso...</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Penalty -->
                    <div class="tab-pane fade" id="tabPenalty">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="tab-section-title mb-0">⚠️ Penalty Points</h4>
                            <div class="penalty-current">
                                <span class="penalty-label">Punteggio attuale:</span>
                                <span class="penalty-value" id="penaltyCurrentValue">0</span>
                            </div>
                        </div>
                        
                        <!-- Azioni Penalty -->
                        <div class="penalty-actions mb-4">
                            <button class="btn-action-penalty add" onclick="showPenaltyModal('add')">
                                ➕ Aggiungi Penalty
                            </button>
                            <button class="btn-action-penalty remove" onclick="showPenaltyModal('remove')">
                                ➖ Rimuovi Penalty
                            </button>
                            <button class="btn-action-penalty reset" onclick="resetPenalty()">
                                🔄 Azzera Tutto
                            </button>
                        </div>
                        
                        <!-- Storico Penalty -->
                        <h5 class="mb-3">📜 Storico Modifiche</h5>
                        <div class="penalty-log-list" id="penaltyLogList">
                            <!-- Caricato via JS -->
                        </div>
                    </div>
                    
                    <!-- Tab Segnalazioni -->
                    <div class="tab-pane fade" id="tabSegnalazioni">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h4 class="tab-section-title mb-3">📥 Segnalazioni Ricevute</h4>
                                <div class="segnalazioni-list" id="segnalazioniRicevuteList">
                                    <!-- Caricato via JS -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4 class="tab-section-title mb-3">📤 Segnalazioni Fatte</h4>
                                <div class="segnalazioni-list" id="segnalazioniFatteList">
                                    <!-- Caricato via JS -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Badge -->
                    <div class="tab-pane fade" id="tabBadges">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="tab-section-title mb-0">🏆 Badge e Gamification</h4>
                            <div class="xp-display">
                                <span class="xp-icon">⭐</span>
                                <span class="xp-value" id="xpValue">0</span>
                                <span class="xp-label">XP</span>
                            </div>
                        </div>
                        
                        <div class="badges-grid" id="badgesGrid">
                            <!-- Caricato via JS -->
                        </div>
                    </div>
                    
                    <!-- Tab Azioni -->
                    <div class="tab-pane fade" id="tabAzioni">
                        <h4 class="tab-section-title mb-3">⚡ Azioni Amministrative</h4>
                        
                        <div class="actions-grid">
                            <!-- Modifica Ruolo -->
                            <div class="action-card">
                                <div class="action-icon">👑</div>
                                <h5 class="action-title">Modifica Ruolo</h5>
                                <p class="action-desc">Promuovi o rimuovi privilegi admin</p>
                                <div class="action-buttons">
                                    <button class="btn-action promote" id="btnPromuoviAdmin" onclick="changeRole('admin')">
                                        Promuovi ad Admin
                                    </button>
                                    <button class="btn-action demote" id="btnRimuoviAdmin" onclick="changeRole('user')" style="display:none;">
                                        Rimuovi Admin
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Sospensione -->
                            <div class="action-card">
                                <div class="action-icon">⏸️</div>
                                <h5 class="action-title">Sospensione</h5>
                                <p class="action-desc">Sospendi temporaneamente l'account</p>
                                <div class="action-buttons">
                                    <button class="btn-action suspend" id="btnSospendi" onclick="showSuspendModal()">
                                        Sospendi Utente
                                    </button>
                                    <button class="btn-action reactivate" id="btnRiabilita" onclick="reactivateUser()" style="display:none;">
                                        Riabilita Utente
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Ban -->
                            <div class="action-card danger">
                                <div class="action-icon">🚫</div>
                                <h5 class="action-title">Ban Permanente</h5>
                                <p class="action-desc">Banna definitivamente l'utente</p>
                                <button class="btn-action ban" id="btnBan" onclick="showBanModal()">
                                    Banna Utente
                                </button>
                            </div>
                            
                            <!-- Invia Messaggio -->
                            <div class="action-card">
                                <div class="action-icon">✉️</div>
                                <h5 class="action-title">Comunicazione</h5>
                                <p class="action-desc">Invia un messaggio all'utente</p>
                                <button class="btn-action message" onclick="showMessageModal()">
                                    Invia Messaggio
                                </button>
                            </div>
                        </div>
                        
                        <!-- Storico Sanzioni -->
                        <div class="mt-4">
                            <h5 class="mb-3">📜 Storico Sanzioni</h5>
                            <div class="sanzioni-list" id="sanzioniList">
                                <!-- Caricato via JS -->
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer modal-footer-dark justify-content-end">
                <button type="button" class="btn-add-new" id="btnModificaUtente" style="display:none;">
                    ✏️ Modifica Profilo
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: SOSPENSIONE UTENTE
     ============================================================================ -->
<div class="modal fade" id="modalSospensione" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content nuovo-campo-modal">
            <div class="modal-header nuovo-campo-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon">⏸️</div>
                    <div>
                        <h5 class="modal-title">Sospendi Utente</h5>
                        <p class="modal-subtitle mb-0" id="sospensioneSubtitle">Seleziona durata e motivo</p>
                    </div>
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Chiudi">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body nuovo-campo-body">
                <form id="formSospensione">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="nc-label">Durata (giorni) <span class="text-danger">*</span></label>
                            <input type="number" class="nc-input" name="giorni" min="1" max="365" value="7" required>
                        </div>
                        <div class="col-md-6">
                            <label class="nc-label">Durata predefinita</label>
                            <select class="nc-select" onchange="document.querySelector('[name=giorni]').value = this.value">
                                <option value="1">1 giorno</option>
                                <option value="3">3 giorni</option>
                                <option value="7" selected>1 settimana</option>
                                <option value="14">2 settimane</option>
                                <option value="30">1 mese</option>
                                <option value="90">3 mesi</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="nc-label">Motivo <span class="text-danger">*</span></label>
                            <textarea class="nc-textarea" name="motivo" rows="3" placeholder="Descrivi il motivo della sospensione..." required></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer nuovo-campo-footer">
                <button type="button" class="nc-btn-cancel" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="nc-btn-submit" id="btnConfirmSospensione" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    ⏸️ Sospendi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: BAN PERMANENTE
     ============================================================================ -->
<div class="modal fade" id="modalBan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content nuovo-campo-modal">
            <div class="modal-header nuovo-campo-header" style="--header-gradient: linear-gradient(135deg, #ef4444, #dc2626);">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon">🚫</div>
                    <div>
                        <h5 class="modal-title">Ban Permanente</h5>
                        <p class="modal-subtitle mb-0" id="banSubtitle">Questa azione è irreversibile</p>
                    </div>
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Chiudi">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body nuovo-campo-body">
                <div class="alert-warning-custom mb-3">
                    ⚠️ <strong>Attenzione:</strong> Il ban è permanente e difficilmente reversibile. L'utente perderà l'accesso al sistema e tutte le prenotazioni future verranno cancellate.
                </div>
                <form id="formBan">
                    <div class="mb-3">
                        <label class="nc-label">Motivo del Ban <span class="text-danger">*</span></label>
                        <textarea class="nc-textarea" name="motivo" rows="4" placeholder="Descrivi dettagliatamente il motivo del ban..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="nc-checkbox-label">
                            <input type="checkbox" name="conferma" required>
                            <span>Confermo di voler bannare permanentemente questo utente</span>
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer nuovo-campo-footer">
                <button type="button" class="nc-btn-cancel" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="nc-btn-submit" id="btnConfirmBan" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                    🚫 Conferma Ban
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: PENALTY POINTS
     ============================================================================ -->
<div class="modal fade" id="modalPenalty" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content nuovo-campo-modal">
            <div class="modal-header nuovo-campo-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon" id="penaltyModalIcon">➕</div>
                    <div>
                        <h5 class="modal-title" id="penaltyModalTitle">Gestisci Penalty</h5>
                        <p class="modal-subtitle mb-0" id="penaltyModalSubtitle">Modifica penalty points</p>
                    </div>
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Chiudi">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body nuovo-campo-body">
                <form id="formPenalty">
                    <input type="hidden" name="action" id="penaltyAction" value="add">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="nc-label">Punti <span class="text-danger">*</span></label>
                            <input type="number" class="nc-input" name="punti" min="1" max="10" value="1" required>
                        </div>
                        <div class="col-12">
                            <label class="nc-label">Descrizione</label>
                            <textarea class="nc-textarea" name="descrizione" rows="2" placeholder="Motivo della modifica..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer nuovo-campo-footer">
                <button type="button" class="nc-btn-cancel" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="nc-btn-submit" id="btnConfirmPenalty">
                    ✓ Conferma
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: INVIA MESSAGGIO
     ============================================================================ -->
<div class="modal fade" id="modalMessaggio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content nuovo-campo-modal">
            <div class="modal-header nuovo-campo-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon">✉️</div>
                    <div>
                        <h5 class="modal-title">Invia Messaggio</h5>
                        <p class="modal-subtitle mb-0" id="messaggioSubtitle">Comunica con l'utente</p>
                    </div>
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Chiudi">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body nuovo-campo-body">
                <form id="formMessaggio">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="nc-label">Oggetto <span class="text-danger">*</span></label>
                            <input type="text" class="nc-input" name="oggetto" placeholder="Oggetto del messaggio..." required>
                        </div>
                        <div class="col-12">
                            <label class="nc-label">Messaggio <span class="text-danger">*</span></label>
                            <textarea class="nc-textarea" name="messaggio" rows="5" placeholder="Scrivi il messaggio..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="nc-label">Tipo invio</label>
                            <select class="nc-select" name="tipo_invio">
                                <option value="notifica">🔔 Solo notifica in-app</option>
                                <option value="email">📧 Solo email</option>
                                <option value="entrambi">📬 Notifica + Email</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer nuovo-campo-footer">
                <button type="button" class="nc-btn-cancel" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="nc-btn-submit" id="btnConfirmMessaggio">
                    ✉️ Invia
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     TOAST NOTIFICATIONS
     ============================================================================ -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div id="toastNotification" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <span class="toast-icon me-2">✅</span>
            <strong class="me-auto toast-title">Notifica</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body"></div>
    </div>
</div>

<!-- ============================================================================
     JAVASCRIPT
     ============================================================================ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================================================
    // VARIABILI GLOBALI
    // ============================================================================
    let currentUserId = null;
    let currentUserData = null;
    
    // ============================================================================
    // TOAST NOTIFICATIONS
    // ============================================================================
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toastNotification');
        const toastBody = toast.querySelector('.toast-body');
        const toastIcon = toast.querySelector('.toast-icon');
        const toastTitle = toast.querySelector('.toast-title');
        
        toastBody.textContent = message;
        toast.classList.remove('bg-success', 'bg-danger', 'bg-warning');
        
        if (type === 'success') {
            toastIcon.textContent = '✅';
            toastTitle.textContent = 'Successo';
            toast.classList.add('bg-success');
        } else if (type === 'error') {
            toastIcon.textContent = '❌';
            toastTitle.textContent = 'Errore';
            toast.classList.add('bg-danger');
        } else {
            toastIcon.textContent = '⚠️';
            toastTitle.textContent = 'Attenzione';
            toast.classList.add('bg-warning');
        }
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    }
    
    // ============================================================================
    // CLICK SU CARD UTENTE
    // ============================================================================
    document.querySelectorAll('.user-card').forEach(card => {
        card.addEventListener('click', function() {
            const userId = this.dataset.userId;
            currentUserId = userId;
            loadUserDetail(userId);
        });
    });
    
    // ============================================================================
    // CARICA DETTAGLIO UTENTE
    // ============================================================================
    function loadUserDetail(userId) {
        fetch(`gestione-utenti.php?action=get_user&user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentUserData = data;
                    populateUserModal(data);
                    new bootstrap.Modal(document.getElementById('modalDettaglioUtente')).show();
                } else {
                    showToast(data.message || 'Errore nel caricamento', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Errore di connessione', 'error');
            });
    }
    
    // ============================================================================
    // POPOLA MODAL UTENTE
    // ============================================================================
    function populateUserModal(data) {
        const user = data.user;
        const stats = data.stats || {};
        
        // Header
        const initials = (user.nome?.charAt(0) || '') + (user.cognome?.charAt(0) || '');
        document.getElementById('detailAvatar').textContent = initials.toUpperCase();
        document.getElementById('detailNome').textContent = `${user.nome} ${user.cognome}`;
        document.getElementById('detailEmail').textContent = user.email;
        
        // Ruolo
        const roleConfig = {
            'admin': { icon: '👑', label: 'Admin', color: '#8B5CF6' },
            'user': { icon: '👤', label: 'Utente', color: '#3B82F6' }
        };
        const role = roleConfig[user.ruolo] || roleConfig.user;
        document.getElementById('detailRuolo').innerHTML = `${role.icon} ${role.label}`;
        document.getElementById('detailRuolo').style.background = `${role.color}22`;
        document.getElementById('detailRuolo').style.color = role.color;
        
        // Status
        const statusConfig = {
            'attivo': { color: '#10B981', label: 'Attivo', class: '' },
            'sospeso': { color: '#F59E0B', label: 'Sospeso', class: 'sospeso' },
            'bannato': { color: '#EF4444', label: 'Bannato', class: 'bannato' }
        };
        const status = statusConfig[user.stato] || statusConfig.attivo;
        const statusBadge = document.getElementById('detailStatus');
        statusBadge.classList.remove('sospeso', 'bannato');
        if (status.class) statusBadge.classList.add(status.class);
        statusBadge.querySelector('.status-dot-modal').style.background = status.color;
        statusBadge.querySelector('.status-text-modal').textContent = status.label;
        
        // Avatar background
        document.getElementById('detailAvatar').style.background = `linear-gradient(135deg, ${role.color}, ${role.color}88)`;
        
        // Tab Info
        document.getElementById('infoNomeCompleto').textContent = `${user.nome} ${user.cognome}`;
        document.getElementById('infoEmail').textContent = user.email || '-';
        document.getElementById('infoTelefono').textContent = user.telefono || '-';
        document.getElementById('infoDataNascita').textContent = user.data_nascita ? formatDate(user.data_nascita) : '-';
        document.getElementById('infoCorso').textContent = user.corso_nome || '-';
        document.getElementById('infoFacolta').textContent = user.facolta || '-';
        document.getElementById('infoAnnoIscrizione').textContent = user.anno_iscrizione || '-';
        document.getElementById('infoRegistrato').textContent = user.created_at ? formatDate(user.created_at) : '-';
        
        // Tab Stats
        document.getElementById('statPrenotazioni').textContent = stats.totale_prenotazioni || 0;
        document.getElementById('statCompletate').textContent = stats.completate || 0;
        document.getElementById('statNoShow').textContent = stats.no_show || 0;
        document.getElementById('statOre').textContent = stats.ore_giocate || 0;
        document.getElementById('statTornei').textContent = stats.tornei_partecipati || 0;
        document.getElementById('statVittorie').textContent = stats.tornei_vinti || 0;
        
        // Affidabilità
        const affidabilita = Math.max(0, Math.min(100, 100 - (user.penalty_points || 0) * 5 - (stats.no_show || 0) * 3));
        document.getElementById('affidabilitaScore').textContent = affidabilita + '%';
        document.getElementById('affidabilitaBar').style.width = affidabilita + '%';
        document.getElementById('affidabilitaBar').style.background = affidabilita >= 80 ? '#10B981' : (affidabilita >= 50 ? '#F59E0B' : '#EF4444');
        document.getElementById('affidabilitaLabel').textContent = affidabilita >= 80 ? 'Eccellente' : (affidabilita >= 50 ? 'Nella media' : 'Da migliorare');
        
        // Tab Penalty
        document.getElementById('penaltyCurrentValue').textContent = user.penalty_points || 0;
        renderPenaltyLog(data.penalty_log || []);
        
        // Tab Segnalazioni
        renderSegnalazioni(data.segnalazioni_ricevute || [], 'segnalazioniRicevuteList', 'ricevute');
        renderSegnalazioni(data.segnalazioni_fatte || [], 'segnalazioniFatteList', 'fatte');
        
        // Tab Badges
        renderBadges(data.badges || [], user.xp_points || 0);
        
        // Tab Azioni - Aggiorna bottoni in base allo stato
        updateActionButtons(user);
        
        // Sanzioni
        renderSanzioni(data.sanzioni || []);
        
        // Attività recenti
        renderAttivita(data.attivita_recenti || []);
    }
    
    // ============================================================================
    // RENDER FUNCTIONS
    // ============================================================================
    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('it-IT', { day: 'numeric', month: 'long', year: 'numeric' });
    }
    
    function renderPenaltyLog(logs) {
        const container = document.getElementById('penaltyLogList');
        if (!logs.length) {
            container.innerHTML = '<div class="no-data">Nessuna modifica registrata</div>';
            return;
        }
        
        const motivoLabels = {
            'no_show': '❌ No-Show',
            'cancellazione_tardiva': '⏰ Cancellazione tardiva',
            'segnalazione': '🚩 Segnalazione',
            'manuale_add': '➕ Aggiunta manuale',
            'manuale_remove': '➖ Rimozione manuale',
            'reset': '🔄 Reset'
        };
        
        container.innerHTML = logs.map(log => `
            <div class="penalty-log-item ${log.punti > 0 ? 'add' : 'remove'}">
                <div class="penalty-log-icon">${log.punti > 0 ? '➕' : '➖'}</div>
                <div class="penalty-log-content">
                    <div class="penalty-log-motivo">${motivoLabels[log.motivo] || log.motivo}</div>
                    <div class="penalty-log-desc">${log.descrizione || ''}</div>
                    <div class="penalty-log-date">${formatDate(log.created_at)}</div>
                </div>
                <div class="penalty-log-points ${log.punti > 0 ? 'positive' : 'negative'}">${log.punti > 0 ? '+' : ''}${log.punti}</div>
            </div>
        `).join('');
    }
    
    function renderSegnalazioni(segnalazioni, containerId, tipo) {
        const container = document.getElementById(containerId);
        if (!segnalazioni.length) {
            container.innerHTML = `<div class="no-data">Nessuna segnalazione ${tipo}</div>`;
            return;
        }
        
        const tipoIcons = {
            'no_show': '❌',
            'comportamento_scorretto': '😠',
            'linguaggio_offensivo': '🤬',
            'violenza': '⚠️',
            'altro': '📝'
        };
        
        const statoColors = {
            'pending': '#F59E0B',
            'in_review': '#3B82F6',
            'resolved': '#10B981',
            'rejected': '#6B7280'
        };
        
        container.innerHTML = segnalazioni.map(s => `
            <div class="segnalazione-item">
                <div class="segnalazione-icon">${tipoIcons[s.tipo] || '📝'}</div>
                <div class="segnalazione-content">
                    <div class="segnalazione-tipo">${s.tipo.replace('_', ' ')}</div>
                    <div class="segnalazione-desc">${s.descrizione?.substring(0, 100) || ''}...</div>
                    <div class="segnalazione-date">${formatDate(s.created_at)}</div>
                </div>
                <div class="segnalazione-stato" style="background: ${statoColors[s.stato] || '#6B7280'}22; color: ${statoColors[s.stato] || '#6B7280'}">
                    ${s.stato}
                </div>
            </div>
        `).join('');
    }
    
    function renderBadges(badges, xp) {
        document.getElementById('xpValue').textContent = xp;
        const container = document.getElementById('badgesGrid');
        
        if (!badges.length) {
            container.innerHTML = '<div class="no-data">Nessun badge sbloccato</div>';
            return;
        }
        
        const raritaColors = {
            'comune': '#9CA3AF',
            'non_comune': '#10B981',
            'raro': '#3B82F6',
            'epico': '#8B5CF6',
            'leggendario': '#F59E0B'
        };
        
        container.innerHTML = badges.map(b => `
            <div class="badge-card" style="border-color: ${raritaColors[b.rarita] || '#9CA3AF'}">
                <div class="badge-icon">${b.icona || '🏅'}</div>
                <div class="badge-name">${b.nome}</div>
                <div class="badge-desc">${b.descrizione?.substring(0, 50) || ''}</div>
                <div class="badge-rarita" style="color: ${raritaColors[b.rarita] || '#9CA3AF'}">${b.rarita}</div>
            </div>
        `).join('');
    }
    
    function renderSanzioni(sanzioni) {
        const container = document.getElementById('sanzioniList');
        if (!sanzioni.length) {
            container.innerHTML = '<div class="no-data">Nessuna sanzione registrata</div>';
            return;
        }
        
        const tipoConfig = {
            'warning': { icon: '⚠️', color: '#F59E0B', label: 'Warning' },
            'sospensione': { icon: '⏸️', color: '#F59E0B', label: 'Sospensione' },
            'ban': { icon: '🚫', color: '#EF4444', label: 'Ban' }
        };
        
        container.innerHTML = sanzioni.map(s => {
            const tipo = tipoConfig[s.tipo] || tipoConfig.warning;
            return `
                <div class="sanzione-item" style="border-left-color: ${tipo.color}">
                    <div class="sanzione-icon">${tipo.icon}</div>
                    <div class="sanzione-content">
                        <div class="sanzione-tipo">${tipo.label}</div>
                        <div class="sanzione-motivo">${s.motivo}</div>
                        <div class="sanzione-date">
                            ${formatDate(s.data_inizio)}
                            ${s.data_fine ? ' - ' + formatDate(s.data_fine) : ' (permanente)'}
                        </div>
                    </div>
                    <div class="sanzione-stato ${s.attiva ? 'attiva' : 'conclusa'}">
                        ${s.attiva ? 'Attiva' : 'Conclusa'}
                    </div>
                </div>
            `;
        }).join('');
    }
    
    function renderAttivita(attivita) {
        const container = document.getElementById('activityTimeline');
        if (!attivita.length) {
            container.innerHTML = '<div class="no-data">Nessuna attività recente</div>';
            return;
        }
        
        container.innerHTML = attivita.map(a => `
            <div class="activity-item">
                <div class="activity-icon">${a.icona || '📌'}</div>
                <div class="activity-content">
                    <div class="activity-text">${a.descrizione}</div>
                    <div class="activity-date">${formatDate(a.created_at)}</div>
                </div>
            </div>
        `).join('');
    }
    
    function updateActionButtons(user) {
        // Ruolo
        if (user.ruolo === 'admin') {
            document.getElementById('btnPromuoviAdmin').style.display = 'none';
            document.getElementById('btnRimuoviAdmin').style.display = 'block';
        } else {
            document.getElementById('btnPromuoviAdmin').style.display = 'block';
            document.getElementById('btnRimuoviAdmin').style.display = 'none';
        }
        
        // Stato
        if (user.stato === 'sospeso') {
            document.getElementById('btnSospendi').style.display = 'none';
            document.getElementById('btnRiabilita').style.display = 'block';
        } else if (user.stato === 'bannato') {
            document.getElementById('btnSospendi').style.display = 'none';
            document.getElementById('btnRiabilita').style.display = 'none';
            document.getElementById('btnBan').disabled = true;
            document.getElementById('btnBan').textContent = 'Già Bannato';
        } else {
            document.getElementById('btnSospendi').style.display = 'block';
            document.getElementById('btnRiabilita').style.display = 'none';
            document.getElementById('btnBan').disabled = false;
            document.getElementById('btnBan').textContent = 'Banna Utente';
        }
    }
    
    // ============================================================================
    // AZIONI UTENTE - Esposto globalmente
    // ============================================================================
    window.changeRole = function(nuovoRuolo) {
        if (!currentUserId) return;
        
        const azione = nuovoRuolo === 'admin' ? 'promuovere ad admin' : 'rimuovere i privilegi admin a';
        if (!confirm(`Sei sicuro di voler ${azione} questo utente?`)) return;
        
        const formData = new FormData();
        formData.append('action', 'change_role');
        formData.append('user_id', currentUserId);
        formData.append('ruolo', nuovoRuolo);
        formData.append('ajax', '1');
        
        fetch('gestione-utenti.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Errore', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Errore di connessione', 'error');
        });
    };
    
    window.showSuspendModal = function() {
        if (!currentUserId) return;
        document.getElementById('sospensioneSubtitle').textContent = currentUserData?.user?.nome + ' ' + currentUserData?.user?.cognome;
        new bootstrap.Modal(document.getElementById('modalSospensione')).show();
    };
    
    window.showBanModal = function() {
        if (!currentUserId) return;
        document.getElementById('banSubtitle').textContent = currentUserData?.user?.nome + ' ' + currentUserData?.user?.cognome;
        document.getElementById('formBan').reset();
        new bootstrap.Modal(document.getElementById('modalBan')).show();
    };
    
    window.showPenaltyModal = function(action) {
        if (!currentUserId) return;
        document.getElementById('penaltyAction').value = action;
        
        if (action === 'add') {
            document.getElementById('penaltyModalIcon').textContent = '➕';
            document.getElementById('penaltyModalTitle').textContent = 'Aggiungi Penalty';
            document.getElementById('btnConfirmPenalty').style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
        } else {
            document.getElementById('penaltyModalIcon').textContent = '➖';
            document.getElementById('penaltyModalTitle').textContent = 'Rimuovi Penalty';
            document.getElementById('btnConfirmPenalty').style.background = 'linear-gradient(135deg, #10b981, #059669)';
        }
        
        new bootstrap.Modal(document.getElementById('modalPenalty')).show();
    };
    
    window.resetPenalty = function() {
        if (!currentUserId) return;
        if (!confirm('Sei sicuro di voler azzerare tutti i penalty points?')) return;
        
        const formData = new FormData();
        formData.append('action', 'reset_penalty');
        formData.append('user_id', currentUserId);
        formData.append('ajax', '1');
        
        fetch('gestione-utenti.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                loadUserDetail(currentUserId);
            } else {
                showToast(data.message || 'Errore', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Errore di connessione', 'error');
        });
    };
    
    window.reactivateUser = function() {
        if (!currentUserId) return;
        if (!confirm('Sei sicuro di voler riabilitare questo utente?')) return;
        
        const formData = new FormData();
        formData.append('action', 'reactivate_user');
        formData.append('user_id', currentUserId);
        formData.append('ajax', '1');
        
        fetch('gestione-utenti.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Errore', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Errore di connessione', 'error');
        });
    };
    
    window.showMessageModal = function() {
        if (!currentUserId) return;
        document.getElementById('messaggioSubtitle').textContent = currentUserData?.user?.nome + ' ' + currentUserData?.user?.cognome;
        document.getElementById('formMessaggio').reset();
        new bootstrap.Modal(document.getElementById('modalMessaggio')).show();
    };
    
    // ============================================================================
    // CONFERME MODAL
    // ============================================================================
    document.getElementById('btnConfirmSospensione').addEventListener('click', function() {
        const form = document.getElementById('formSospensione');
        const giorni = form.querySelector('[name="giorni"]').value;
        const motivo = form.querySelector('[name="motivo"]').value.trim();
        
        if (!giorni || !motivo) {
            showToast('Compila tutti i campi', 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'suspend_user');
        formData.append('user_id', currentUserId);
        formData.append('giorni', giorni);
        formData.append('motivo', motivo);
        formData.append('ajax', '1');
        
        fetch('gestione-utenti.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('modalSospensione')).hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Errore', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Errore di connessione', 'error');
        });
    });
    
    document.getElementById('btnConfirmBan').addEventListener('click', function() {
        const form = document.getElementById('formBan');
        const motivo = form.querySelector('[name="motivo"]').value.trim();
        const conferma = form.querySelector('[name="conferma"]').checked;
        
        if (!motivo || !conferma) {
            showToast('Compila tutti i campi e conferma', 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'ban_user');
        formData.append('user_id', currentUserId);
        formData.append('motivo', motivo);
        formData.append('ajax', '1');
        
        fetch('gestione-utenti.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('modalBan')).hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Errore', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Errore di connessione', 'error');
        });
    });
    
    document.getElementById('btnConfirmPenalty').addEventListener('click', function() {
        const form = document.getElementById('formPenalty');
        const action = document.getElementById('penaltyAction').value;
        const punti = form.querySelector('[name="punti"]').value;
        const descrizione = form.querySelector('[name="descrizione"]').value.trim();
        
        if (!punti) {
            showToast('Inserisci il numero di punti', 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', action === 'add' ? 'add_penalty' : 'remove_penalty');
        formData.append('user_id', currentUserId);
        formData.append('punti', punti);
        formData.append('descrizione', descrizione);
        formData.append('ajax', '1');
        
        fetch('gestione-utenti.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('modalPenalty')).hide();
                loadUserDetail(currentUserId);
            } else {
                showToast(data.message || 'Errore', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Errore di connessione', 'error');
        });
    });
    
    document.getElementById('btnConfirmMessaggio').addEventListener('click', function() {
        const form = document.getElementById('formMessaggio');
        const oggetto = form.querySelector('[name="oggetto"]').value.trim();
        const messaggio = form.querySelector('[name="messaggio"]').value.trim();
        const tipoInvio = form.querySelector('[name="tipo_invio"]').value;
        
        if (!oggetto || !messaggio) {
            showToast('Compila tutti i campi', 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'send_message');
        formData.append('user_id', currentUserId);
        formData.append('oggetto', oggetto);
        formData.append('messaggio', messaggio);
        formData.append('tipo_invio', tipoInvio);
        formData.append('ajax', '1');
        
        fetch('gestione-utenti.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('modalMessaggio')).hide();
            } else {
                showToast(data.message || 'Errore', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Errore di connessione', 'error');
        });
    });
    
    // ============================================================================
    // RICERCA CON INVIO
    // ============================================================================
    document.querySelector('.filter-input-search').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('filtersForm').submit();
        }
    });
    
    // ============================================================================
    // FIX MODAL - Sposta i modal nel body
    // ============================================================================
    const modalsToMove = ['modalDettaglioUtente', 'modalSospensione', 'modalBan', 'modalPenalty', 'modalMessaggio'];
    modalsToMove.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });
    
});
</script>

<?php include 'footer.php'; ?>