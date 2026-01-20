<!-- ============================================================================
     GESTIONE CAMPI - Campus Sports Arena Admin
     ============================================================================ -->

<?php
// Helper per formattare tempo relativo
function tempoRelativo($datetime) {
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

// Colori sport (per nome sport)
$sportColors = [
    'Calcetto' => '#10B981',
    'Calcio a 5' => '#10B981',
    'Calcio a 7' => '#10B981',
    'Basket' => '#F59E0B',
    'Tennis' => '#EC4899',
    'Pallavolo' => '#8B5CF6',
    'Padel' => '#06B6D4',
    'Calcio' => '#10B981',
    'Badminton' => '#EC4899',
    'Ping Pong' => '#F97316'
];

// Icone sport (per nome sport) - usando immagini PNG
$sportIcons = [
    'Calcetto' => 'calcio5.png',
    'Calcio a 5' => 'calcio5.png',
    'Calcio a 7' => 'calcio7.png',
    'Basket' => 'basket.png',
    'Tennis' => 'tennis.png',
    'Pallavolo' => 'pallavolo.png',
    'Padel' => 'padel.png',
    'Calcio' => 'calcio5.png',
    'Badminton' => 'badminton.png',
    'Ping Pong' => 'pingpong.png'
];

// Emoji sport (fallback)
$sportEmojis = [
    'Calcetto' => '⚽',
    'Calcio a 5' => '⚽',
    'Calcio a 7' => '⚽',
    'Basket' => '🏀',
    'Tennis' => '🎾',
    'Pallavolo' => '🏐',
    'Padel' => '🏸',
    'Calcio' => '⚽',
    'Badminton' => '🏸',
    'Ping Pong' => '🏓'
];

// Mappa da icona file (.png) a emoji
$iconaToEmoji = [
    'calcio5.png' => '⚽',
    'calcio7.png' => '⚽',
    'basket.png' => '🏀',
    'tennis.png' => '🎾',
    'pallavolo.png' => '🏐',
    'padel.png' => '🏸',
    'badminton.png' => '🏸',
    'pingpong.png' => '🏓'
];

// Helper per ottenere emoji da icona file
function getEmojiFromIcona($icona) {
    global $iconaToEmoji;
    return $iconaToEmoji[$icona] ?? '🏟️';
}

// Helper per ottenere sport slug per CSS
function getSportSlug($sportName) {
    $slugMap = [
        'Calcio a 5' => 'calcio5',
        'Calcetto' => 'calcio5',
        'Calcio a 7' => 'calcio7',
        'Basket' => 'basket',
        'Tennis' => 'tennis',
        'Pallavolo' => 'pallavolo',
        'Padel' => 'padel',
        'Badminton' => 'badminton',
        'Ping Pong' => 'pingpong'
    ];
    return $slugMap[$sportName] ?? 'default';
}

$stats = $templateParams["stats"];
$campi = $templateParams["campi"];
$sports = $templateParams["sports"];
$filtri = $templateParams["filtri"];
$prenotazioniOggi = $templateParams["prenotazioni_oggi"] ?? [];
$recensioniRecenti = $templateParams["recensioni_recenti"] ?? [];
?>

<!-- Header Gestione Campi - Tutto in linea -->
<div class="gestione-header">
    <span class="header-icon">🏟️</span>
    <p class="page-subtitle">Gestisci tutti i campi sportivi del campus</p>
    
    <!-- Search -->
    <div class="search-box" id="searchContainer">
        <span class="search-icon">🔍</span>
        <input type="text" class="search-input" id="searchCampi" placeholder="Cerca campi..." value="<?php echo htmlspecialchars($filtri['search']); ?>">
    </div>
    
    <!-- Add New Field Button -->
    <button class="btn-add-new" data-bs-toggle="modal" data-bs-target="#modalNuovoCampo">
        <span>+</span> Nuovo Campo
    </button>
</div>

<!-- ============================================================================
     QUICK STATS - KPI Cards
     ============================================================================ -->
<div class="row g-3 mb-4">
    <!-- Campi Totali -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="blue">
            <div class="kpi-icon-wrapper">
                <span class="kpi-icon">🏟️</span>
            </div>
            <div class="kpi-value"><?php echo $stats['totale'] ?? 0; ?></div>
            <div class="kpi-label">Campi Totali</div>
        </div>
    </div>
    
    <!-- Disponibili -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="green">
            <div class="kpi-icon-wrapper">
                <span class="kpi-icon">✅</span>
            </div>
            <div class="kpi-value"><?php echo $stats['disponibili'] ?? 0; ?></div>
            <div class="kpi-label">Disponibili</div>
        </div>
    </div>
    
    <!-- Manutenzione -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="orange">
            <div class="kpi-icon-wrapper">
                <span class="kpi-icon">🔧</span>
            </div>
            <div class="kpi-value"><?php echo $stats['manutenzione'] ?? 0; ?></div>
            <div class="kpi-label">Manutenzione</div>
        </div>
    </div>
    
    <!-- Chiusi -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="red">
            <div class="kpi-icon-wrapper">
                <span class="kpi-icon">🚫</span>
            </div>
            <div class="kpi-value"><?php echo $stats['chiusi'] ?? 0; ?></div>
            <div class="kpi-label">Chiusi</div>
        </div>
    </div>
    
    <!-- Prenotazioni Oggi -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="purple">
            <div class="kpi-icon-wrapper">
                <span class="kpi-icon">📅</span>
            </div>
            <div class="kpi-value"><?php echo $stats['prenotazioni_oggi'] ?? 0; ?></div>
            <div class="kpi-label">Prenotazioni Oggi</div>
        </div>
    </div>
    
    <!-- Utilizzo Medio -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="cyan">
            <div class="kpi-icon-wrapper">
                <span class="kpi-icon">📈</span>
            </div>
            <div class="kpi-value"><?php echo $stats['utilizzo_medio'] ?? 0; ?>%</div>
            <div class="kpi-label">Utilizzo Medio</div>
        </div>
    </div>
</div>

<!-- ============================================================================
     FILTRI - Design come nelle immagini
     ============================================================================ -->
<div class="filters-card mb-4">
    <form id="formFiltri" method="GET">
        <!-- Sport Filter -->
        <div class="filter-row">
            <span class="filter-label">Sport:</span>
            <div class="filter-chips">
                <button type="button" class="filter-chip <?php echo empty($filtri['sport']) ? 'active' : ''; ?>" data-filter="sport" data-value="">
                    Tutti
                </button>
                <?php foreach ($sports as $sport): 
                    $sportEmoji = getEmojiFromIcona($sport['icona'] ?? '');
                    $isActive = $filtri['sport'] == $sport['nome'];
                ?>
                <button type="button" class="filter-chip <?php echo $isActive ? 'active' : ''; ?>" data-filter="sport" data-value="<?php echo htmlspecialchars($sport['nome']); ?>">
                    <span class="chip-icon"><?php echo $sportEmoji; ?></span>
                    <?php echo htmlspecialchars($sport['nome']); ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Stato Filter -->
        <div class="filter-row">
            <span class="filter-label">Stato:</span>
            <div class="filter-chips">
                <button type="button" class="filter-chip <?php echo empty($filtri['stato']) ? 'active' : ''; ?>" data-filter="stato" data-value="">
                    Tutti
                </button>
                <button type="button" class="filter-chip <?php echo $filtri['stato'] == 'disponibile' ? 'active' : ''; ?>" data-filter="stato" data-value="disponibile">
                    <span class="status-dot green"></span> Disponibile
                </button>
                <button type="button" class="filter-chip <?php echo $filtri['stato'] == 'manutenzione' ? 'active' : ''; ?>" data-filter="stato" data-value="manutenzione">
                    <span class="status-dot orange"></span> Manutenzione
                </button>
                <button type="button" class="filter-chip <?php echo $filtri['stato'] == 'chiuso' ? 'active' : ''; ?>" data-filter="stato" data-value="chiuso">
                    <span class="status-dot red"></span> Chiuso
                </button>
            </div>
        </div>
        
        <!-- Tipo Filter -->
        <div class="filter-row">
            <span class="filter-label">Tipo:</span>
            <div class="filter-chips">
                <button type="button" class="filter-chip <?php echo empty($filtri['tipo']) ? 'active' : ''; ?>" data-filter="tipo" data-value="">
                    Tutti
                </button>
                <button type="button" class="filter-chip <?php echo $filtri['tipo'] == 'indoor' ? 'active' : ''; ?>" data-filter="tipo" data-value="indoor">
                    <span class="chip-icon">🏠</span> Indoor
                </button>
                <button type="button" class="filter-chip <?php echo $filtri['tipo'] == 'outdoor' ? 'active' : ''; ?>" data-filter="tipo" data-value="outdoor">
                    <span class="chip-icon">🌳</span> Outdoor
                </button>
            </div>
            
            <!-- Ordina dropdown -->
            <div class="sort-wrapper ms-auto">
                <span class="filter-label">Ordina:</span>
                <select name="ordina" id="sortSelect" class="sort-select">
                    <option value="nome" <?php echo $filtri['ordina'] == 'nome' ? 'selected' : ''; ?>>Nome</option>
                    <option value="rating" <?php echo $filtri['ordina'] == 'rating' ? 'selected' : ''; ?>>Rating</option>
                    <option value="utilizzo" <?php echo $filtri['ordina'] == 'utilizzo' ? 'selected' : ''; ?>>Utilizzo</option>
                    <option value="prenotazioni" <?php echo $filtri['ordina'] == 'prenotazioni' ? 'selected' : ''; ?>>Prenotazioni</option>
                </select>
            </div>
        </div>
        
        <!-- Hidden inputs for filters -->
        <input type="hidden" name="sport" id="filterSport" value="<?php echo htmlspecialchars($filtri['sport']); ?>">
        <input type="hidden" name="stato" id="filterStato" value="<?php echo htmlspecialchars($filtri['stato']); ?>">
        <input type="hidden" name="tipo" id="filterTipo" value="<?php echo htmlspecialchars($filtri['tipo']); ?>">
        <input type="hidden" name="search" id="filterSearch" value="<?php echo htmlspecialchars($filtri['search']); ?>">
    </form>
</div>

<!-- ============================================================================
     TITOLO SEZIONE CAMPI
     ============================================================================ -->
<div class="section-header mb-4">
    <div class="d-flex align-items-center gap-2">
        <span class="section-icon">🏟️</span>
        <h2 class="section-title mb-0">Tutti i Campi</h2>
        <span class="section-count">(<?php echo count($campi); ?> campi)</span>
    </div>
</div>

<!-- ============================================================================
     GRIGLIA CAMPI - Design come nelle immagini
     ============================================================================ -->
<div class="row g-4 mb-4" id="campiGrid">
    <?php if (empty($campi)): ?>
    <div class="col-12">
        <div class="empty-state">
            <span class="empty-icon">🏟️</span>
            <h3>Nessun campo trovato</h3>
            <p>Prova a modificare i filtri o aggiungi un nuovo campo.</p>
            <button class="btn-add-new" data-bs-toggle="modal" data-bs-target="#modalNuovoCampo">
                + Aggiungi Campo
            </button>
        </div>
    </div>
    <?php else: ?>
    <?php foreach ($campi as $campo): 
        $sportColor = $sportColors[$campo['sport_nome']] ?? '#3B82F6';
        $sportIcon = $sportIcons[$campo['sport_nome']] ?? 'calcio5.png';
        $sportEmoji = $sportEmojis[$campo['sport_nome']] ?? '🏟️';
        $sportSlug = getSportSlug($campo['sport_nome']);
        
        // Status config
        $statusConfig = [
            'disponibile' => ['color' => '#10B981', 'label' => 'Disponibile', 'class' => 'green'],
            'manutenzione' => ['color' => '#F59E0B', 'label' => 'Manutenzione', 'class' => 'orange'],
            'chiuso' => ['color' => '#EF4444', 'label' => 'Chiuso', 'class' => 'red']
        ];
        $status = $statusConfig[$campo['stato']] ?? $statusConfig['disponibile'];
        
        // Calcolo utilizzo
        $utilizzo = min(100, ($campo['prenotazioni_settimana'] ?? 0) * 3);
        
        // Rating
        $rating = floatval($campo['rating_medio'] ?? 0);
    ?>
    <div class="col-xl-4 col-lg-6">
        <div class="field-card" data-campo-id="<?php echo $campo['campo_id']; ?>" data-sport="<?php echo $sportSlug; ?>" style="--sport-color: <?php echo $sportColor; ?>">
            <!-- Header con sfondo gradient -->
            <div class="field-card-header">
                <!-- Status Badge -->
                <div class="field-status <?php echo $status['class']; ?>">
                    <span class="status-indicator"></span>
                    <span class="status-text"><?php echo $status['label']; ?></span>
                </div>
                
                <!-- Sport Icon - Grande al centro -->
                <div class="field-icon-wrapper">
                    <img src="assets/icons/<?php echo $sportIcon; ?>" alt="<?php echo htmlspecialchars($campo['sport_nome']); ?>" class="field-sport-icon" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <span class="field-sport-emoji" style="display: none;"><?php echo $sportEmoji; ?></span>
                </div>
                
                <!-- Nome Campo -->
                <h3 class="field-name"><?php echo htmlspecialchars($campo['nome']); ?></h3>
                
                <!-- Sport e Tipo -->
                <div class="field-type">
                    <?php echo htmlspecialchars($campo['sport_nome']); ?> • <?php echo $campo['tipo_campo'] == 'indoor' ? 'Indoor' : 'Outdoor'; ?>
                </div>
            </div>
            
            <!-- Body -->
            <div class="field-card-body">
                <!-- Stats Row -->
                <div class="field-stats-row">
                    <div class="field-stat">
                        <span class="stat-value"><?php echo $campo['prenotazioni_oggi'] ?? 0; ?></span>
                        <span class="stat-label">oggi</span>
                    </div>
                    <div class="field-stat">
                        <span class="stat-value"><?php echo $campo['prenotazioni_settimana'] ?? 0; ?></span>
                        <span class="stat-label">settimana</span>
                    </div>
                    <div class="field-stat">
                        <span class="stat-value highlight" style="color: <?php echo $sportColor; ?>"><?php echo $utilizzo; ?>%</span>
                        <span class="stat-label">utilizzo</span>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="field-progress-wrapper">
                    <div class="field-progress-bar" style="width: <?php echo $utilizzo; ?>%; background: <?php echo $sportColor; ?>"></div>
                </div>
                
                <!-- Orario -->
                <div class="field-schedule">
                    <span class="schedule-icon">🕐</span>
                    <?php echo substr($campo['orario_apertura'], 0, 5); ?> - <?php echo substr($campo['orario_chiusura'], 0, 5); ?>
                </div>
                
                <!-- Rating -->
                <div class="field-rating-row">
                    <div class="stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star <?php echo $i <= round($rating) ? 'filled' : 'empty'; ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <span class="rating-value"><?php echo number_format($rating, 1); ?></span>
                    <span class="rating-count">(<?php echo $campo['num_recensioni'] ?? 0; ?>)</span>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- ============================================================================
     MODAL: NUOVO CAMPO
     ============================================================================ -->
<div class="modal fade" id="modalNuovoCampo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-dark">
            <div class="modal-header modal-header-gradient">
                <div class="d-flex align-items-center gap-3">
                    <span class="modal-icon">🏟️</span>
                    <div>
                        <h5 class="modal-title mb-0">Nuovo Campo</h5>
                        <p class="modal-subtitle mb-0">Aggiungi un nuovo campo sportivo</p>
                    </div>
                </div>
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close">✕</button>
            </div>
            <div class="modal-body">
                <form id="formNuovoCampo">
                    <div class="row g-3">
                        <!-- Nome e Sport -->
                        <div class="col-md-8">
                            <label class="form-label-custom">Nome Campo <span class="required">*</span></label>
                            <input type="text" class="form-input-custom" name="nome" placeholder="Es. Campo Calcetto Nord" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-custom">Sport <span class="required">*</span></label>
                            <select class="form-select-custom" name="sport_id" required>
                                <?php foreach ($sports as $sport): 
                                    $sportEmoji = getEmojiFromIcona($sport['icona'] ?? '');
                                ?>
                                <option value="<?php echo $sport['sport_id']; ?>"><?php echo $sportEmoji; ?> <?php echo htmlspecialchars($sport['nome']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Tipo e Superficie -->
                        <div class="col-md-6">
                            <label class="form-label-custom">Tipo <span class="required">*</span></label>
                            <div class="btn-group-custom w-100">
                                <input type="radio" class="btn-check" name="tipo_campo" id="tipoOutdoor" value="outdoor" checked>
                                <label class="btn-option" for="tipoOutdoor">🌳 Outdoor</label>
                                
                                <input type="radio" class="btn-check" name="tipo_campo" id="tipoIndoor" value="indoor">
                                <label class="btn-option" for="tipoIndoor">🏠 Indoor</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Superficie <span class="required">*</span></label>
                            <select class="form-select-custom" name="tipo_superficie" required>
                                <option value="erba_sintetica">Erba sintetica</option>
                                <option value="erba_naturale">Erba naturale</option>
                                <option value="parquet">Parquet</option>
                                <option value="cemento">Cemento</option>
                                <option value="terra_battuta">Terra battuta</option>
                                <option value="resina">Resina</option>
                            </select>
                        </div>
                        
                        <!-- Capienza e Location -->
                        <div class="col-md-4">
                            <label class="form-label-custom">Capienza Max <span class="required">*</span></label>
                            <input type="number" class="form-input-custom" name="capienza_max" placeholder="Es. 10" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label-custom">Posizione <span class="required">*</span></label>
                            <input type="text" class="form-input-custom" name="location" placeholder="Es. Zona Nord - Edificio Sport" required>
                        </div>
                        
                        <!-- Dimensioni -->
                        <div class="col-md-6">
                            <label class="form-label-custom">Lunghezza (m)</label>
                            <input type="number" step="0.1" class="form-input-custom" name="lunghezza_m" placeholder="Opzionale">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Larghezza (m)</label>
                            <input type="number" step="0.1" class="form-input-custom" name="larghezza_m" placeholder="Opzionale">
                        </div>
                        
                        <!-- Orari -->
                        <div class="col-md-6">
                            <label class="form-label-custom">Orario Apertura <span class="required">*</span></label>
                            <input type="time" class="form-input-custom" name="orario_apertura" value="08:00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Orario Chiusura <span class="required">*</span></label>
                            <input type="time" class="form-input-custom" name="orario_chiusura" value="22:00" required>
                        </div>
                        
                        <!-- Stato -->
                        <div class="col-12">
                            <label class="form-label-custom">Stato Iniziale</label>
                            <div class="btn-group-custom w-100">
                                <input type="radio" class="btn-check" name="stato" id="statoDisp" value="disponibile" checked>
                                <label class="btn-option btn-success-option" for="statoDisp">🟢 Disponibile</label>
                                
                                <input type="radio" class="btn-check" name="stato" id="statoMan" value="manutenzione">
                                <label class="btn-option btn-warning-option" for="statoMan">🟡 Manutenzione</label>
                                
                                <input type="radio" class="btn-check" name="stato" id="statoChiuso" value="chiuso">
                                <label class="btn-option btn-danger-option" for="statoChiuso">🔴 Chiuso</label>
                            </div>
                        </div>
                        
                        <!-- Servizi -->
                        <div class="col-12">
                            <label class="form-label-custom">Servizi Disponibili</label>
                            <div class="services-grid">
                                <label class="service-checkbox">
                                    <input type="checkbox" name="servizi[]" value="illuminazione_notturna">
                                    <span class="service-icon">💡</span>
                                    <span class="service-name">Illuminazione</span>
                                </label>
                                <label class="service-checkbox">
                                    <input type="checkbox" name="servizi[]" value="spogliatoi">
                                    <span class="service-icon">🚿</span>
                                    <span class="service-name">Spogliatoi</span>
                                </label>
                                <label class="service-checkbox">
                                    <input type="checkbox" name="servizi[]" value="docce">
                                    <span class="service-icon">🚿</span>
                                    <span class="service-name">Docce</span>
                                </label>
                                <label class="service-checkbox">
                                    <input type="checkbox" name="servizi[]" value="parcheggio">
                                    <span class="service-icon">🅿️</span>
                                    <span class="service-name">Parcheggio</span>
                                </label>
                                <label class="service-checkbox">
                                    <input type="checkbox" name="servizi[]" value="distributori">
                                    <span class="service-icon">💧</span>
                                    <span class="service-name">Distributori</span>
                                </label>
                                <label class="service-checkbox">
                                    <input type="checkbox" name="servizi[]" value="noleggio_attrezzatura">
                                    <span class="service-icon">🎾</span>
                                    <span class="service-name">Noleggio</span>
                                </label>
                                <label class="service-checkbox">
                                    <input type="checkbox" name="servizi[]" value="bar_ristoro">
                                    <span class="service-icon">☕</span>
                                    <span class="service-name">Bar/Ristoro</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Descrizione -->
                        <div class="col-12">
                            <label class="form-label-custom">Descrizione</label>
                            <textarea class="form-textarea-custom" name="descrizione" rows="3" placeholder="Descrivi le caratteristiche del campo..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer modal-footer-dark">
                <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn-success-custom" id="btnSalvaCampo">
                    <span>✓</span> Crea Campo
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: DETTAGLIO CAMPO
     ============================================================================ -->
<div class="modal fade" id="modalDettaglioCampo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-dark">
            <div class="modal-header modal-header-sport" id="modalDetailHeader">
                <div class="d-flex align-items-center gap-3">
                    <span class="modal-icon-lg" id="detailIcon">⚽</span>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="modal-title mb-0" id="detailNome">Nome Campo</h5>
                            <div class="status-badge-modal" id="detailStatus">
                                <span class="status-dot-modal"></span>
                                <span class="status-text-modal">Disponibile</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 text-muted small">
                            <span class="sport-badge-modal" id="detailSport">⚽ Calcetto</span>
                            <span id="detailLocation">📍 Location</span>
                            <span id="detailTipo">🏠 Indoor</span>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close">✕</button>
            </div>
            
            <!-- Tabs -->
            <ul class="nav nav-tabs-custom" id="detailTabs">
                <li class="nav-item">
                    <button class="nav-link-custom active" data-bs-toggle="tab" data-bs-target="#tabInfo">
                        📋 Informazioni
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link-custom" data-bs-toggle="tab" data-bs-target="#tabCalendario">
                        📅 Calendario
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link-custom" data-bs-toggle="tab" data-bs-target="#tabStats">
                        📊 Statistiche
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link-custom" data-bs-toggle="tab" data-bs-target="#tabRecensioni">
                        ⭐ Recensioni
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link-custom" data-bs-toggle="tab" data-bs-target="#tabManutenzione">
                        🔧 Manutenzione
                    </button>
                </li>
            </ul>
            
            <div class="modal-body">
                <div class="tab-content" id="detailTabContent">
                    <!-- Tab Info -->
                    <div class="tab-pane fade show active" id="tabInfo">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h4 class="tab-section-title">📋 Dettagli Campo</h4>
                                <div class="info-card">
                                    <div class="info-row">
                                        <span class="info-label">Superficie</span>
                                        <span class="info-value" id="detailSuperficie">-</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Capienza</span>
                                        <span class="info-value" id="detailCapienza">-</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Orario</span>
                                        <span class="info-value" id="detailOrario">-</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Dimensioni</span>
                                        <span class="info-value" id="detailDimensioni">-</span>
                                    </div>
                                </div>
                                
                                <h4 class="tab-section-title mt-4">✨ Servizi Disponibili</h4>
                                <div class="services-display" id="detailServizi">
                                    <!-- Servizi caricati via JS -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4 class="tab-section-title">📈 Statistiche</h4>
                                <div class="stats-grid">
                                    <div class="stat-card" data-color="blue">
                                        <span class="stat-card-icon">📅</span>
                                        <span class="stat-card-value" id="detailPrenOggi">0</span>
                                        <span class="stat-card-label">Prenotazioni Oggi</span>
                                    </div>
                                    <div class="stat-card" data-color="purple">
                                        <span class="stat-card-icon">📊</span>
                                        <span class="stat-card-value" id="detailPrenSett">0</span>
                                        <span class="stat-card-label">Questa Settimana</span>
                                    </div>
                                    <div class="stat-card" data-color="green">
                                        <span class="stat-card-icon">📈</span>
                                        <span class="stat-card-value" id="detailUtilizzo">0%</span>
                                        <span class="stat-card-label">Tasso Utilizzo</span>
                                    </div>
                                    <div class="stat-card" data-color="orange">
                                        <span class="stat-card-icon">⭐</span>
                                        <span class="stat-card-value" id="detailNumRec">0</span>
                                        <span class="stat-card-label">Recensioni</span>
                                    </div>
                                </div>
                                
                                <h4 class="tab-section-title mt-4">⭐ Rating</h4>
                                <div class="rating-display-card">
                                    <span class="rating-big" id="detailRatingBig">0.0</span>
                                    <div>
                                        <div class="stars-display" id="detailStars">
                                            <span class="star">★</span>
                                            <span class="star">★</span>
                                            <span class="star">★</span>
                                            <span class="star">★</span>
                                            <span class="star">★</span>
                                        </div>
                                        <div class="rating-info" id="detailRatingInfo">Basato su 0 recensioni</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Calendario -->
                    <div class="tab-pane fade" id="tabCalendario">
                        <div class="empty-tab">
                            <span class="empty-tab-icon">📅</span>
                            <p>Calendario prenotazioni - Visualizza slot disponibili e occupati</p>
                            <small class="text-muted">(Funzionalità in sviluppo)</small>
                        </div>
                    </div>
                    
                    <!-- Tab Statistiche -->
                    <div class="tab-pane fade" id="tabStats">
                        <div class="empty-tab">
                            <span class="empty-tab-icon">📊</span>
                            <p>Statistiche dettagliate - Grafici di utilizzo, heatmap orari</p>
                            <small class="text-muted">(Funzionalità in sviluppo)</small>
                        </div>
                    </div>
                    
                    <!-- Tab Recensioni -->
                    <div class="tab-pane fade" id="tabRecensioni">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="tab-section-title mb-0">Recensioni Utenti</h4>
                            <button class="btn-secondary-custom btn-sm">Filtra</button>
                        </div>
                        <div id="detailRecensioniList">
                            <!-- Recensioni caricate via JS -->
                        </div>
                    </div>
                    
                    <!-- Tab Manutenzione -->
                    <div class="tab-pane fade" id="tabManutenzione">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="tab-section-title mb-0">Gestione Manutenzione</h4>
                            <button class="btn-add-new btn-sm" id="btnProgrammaManutenzione">
                                🔧 Programma Manutenzione
                            </button>
                        </div>
                        <div id="detailManutenzioneContent">
                            <div class="manutenzione-ok">
                                <span class="manutenzione-icon">✅</span>
                                <p>Nessuna manutenzione programmata</p>
                                <small class="text-muted">Il campo è operativo</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer modal-footer-dark justify-content-between">
                <button type="button" class="btn-danger-custom btn-sm" id="btnEliminaCampo">
                    🗑️ Elimina Campo
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Chiudi</button>
                    <button type="button" class="btn-add-new" id="btnModificaCampo">
                        ✏️ Modifica Campo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: BLOCCO MANUTENZIONE
     ============================================================================ -->
<div class="modal fade" id="modalBloccoManutenzione" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <div class="modal-header modal-header-gradient orange">
                <div class="d-flex align-items-center gap-3">
                    <span class="modal-icon">🔧</span>
                    <div>
                        <h5 class="modal-title mb-0">Programma Manutenzione</h5>
                        <p class="modal-subtitle mb-0" id="manutenzioneSubtitle">Campo selezionato</p>
                    </div>
                </div>
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close">✕</button>
            </div>
            <div class="modal-body">
                <form id="formBloccoManutenzione">
                    <input type="hidden" name="campo_id" id="blocco_campo_id">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">Data Inizio <span class="required">*</span></label>
                            <input type="date" class="form-input-custom" name="data_inizio" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Ora Inizio</label>
                            <input type="time" class="form-input-custom" name="ora_inizio" value="08:00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Data Fine <span class="required">*</span></label>
                            <input type="date" class="form-input-custom" name="data_fine" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Ora Fine</label>
                            <input type="time" class="form-input-custom" name="ora_fine" value="22:00">
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Tipo Blocco</label>
                            <select class="form-select-custom" name="tipo_blocco">
                                <option value="manutenzione_ordinaria">Manutenzione Ordinaria</option>
                                <option value="manutenzione_straordinaria">Manutenzione Straordinaria</option>
                                <option value="evento_speciale">Evento Speciale</option>
                                <option value="chiusura_temporanea">Chiusura Temporanea</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Motivo</label>
                            <textarea class="form-textarea-custom" name="motivo" rows="3" placeholder="Descrivi il motivo del blocco..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer modal-footer-dark">
                <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn-warning-custom" id="btnCreaBlocco">
                    🔧 Crea Blocco
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
    let currentCampoId = null;
    const sportColors = <?php echo json_encode($sportColors); ?>;
    const sportEmojis = <?php echo json_encode($sportEmojis); ?>;
    
    // ============================================================================
    // TOAST NOTIFICATIONS
    // ============================================================================
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toastNotification');
        const toastIcon = toast.querySelector('.toast-icon');
        const toastTitle = toast.querySelector('.toast-title');
        const toastBody = toast.querySelector('.toast-body');
        
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };
        
        toastIcon.textContent = icons[type] || icons.success;
        toastTitle.textContent = type.charAt(0).toUpperCase() + type.slice(1);
        toastBody.textContent = message;
        
        toast.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info');
        toast.classList.add(type === 'error' ? 'bg-danger' : 'bg-' + type);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    }
    
    // ============================================================================
    // FILTRI
    // ============================================================================
    const filterChips = document.querySelectorAll('.filter-chip[data-filter]');
    filterChips.forEach(chip => {
        chip.addEventListener('click', function() {
            const filter = this.dataset.filter;
            const value = this.dataset.value;
            
            // Update active state
            document.querySelectorAll(`.filter-chip[data-filter="${filter}"]`).forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            
            // Update hidden input
            document.getElementById('filter' + filter.charAt(0).toUpperCase() + filter.slice(1)).value = value;
            
            // Submit form
            document.getElementById('formFiltri').submit();
        });
    });
    
    // Sort Select
    document.getElementById('sortSelect').addEventListener('change', function() {
        document.getElementById('formFiltri').submit();
    });
    
    // Search
    let searchTimeout;
    document.getElementById('searchCampi').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('filterSearch').value = this.value;
            document.getElementById('formFiltri').submit();
        }, 500);
    });
    
    // ============================================================================
    // CLICK SU CAMPO CARD
    // ============================================================================
    document.querySelectorAll('.field-card').forEach(card => {
        card.addEventListener('click', function() {
            const campoId = this.dataset.campoId;
            loadCampoDetails(campoId);
        });
    });
    
    // ============================================================================
    // CARICA DETTAGLI CAMPO
    // ============================================================================
    function loadCampoDetails(campoId) {
        currentCampoId = campoId;
        
        fetch(`gestione-campi.php?action=get_campo&campo_id=${campoId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateDetailModal(data);
                new bootstrap.Modal(document.getElementById('modalDettaglioCampo')).show();
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
    // POPOLA MODAL DETTAGLIO
    // ============================================================================
    function populateDetailModal(data) {
        const campo = data.campo;
        const servizi = data.servizi;
        const recensioni = data.recensioni || [];
        const stats = data.recensioni_stats || {};
        
        const sportColor = sportColors[campo.sport_nome] || '#3B82F6';
        const sportEmoji = sportEmojis[campo.sport_nome] || '🏟️';
        
        // Header
        document.getElementById('modalDetailHeader').style.setProperty('--sport-color', sportColor);
        document.getElementById('detailIcon').textContent = sportEmoji;
        document.getElementById('detailNome').textContent = campo.nome;
        document.getElementById('detailSport').innerHTML = `${sportEmoji} ${campo.sport_nome}`;
        document.getElementById('detailLocation').textContent = `📍 ${campo.location}`;
        document.getElementById('detailTipo').textContent = campo.tipo_campo === 'indoor' ? '🏠 Indoor' : '🌳 Outdoor';
        
        // Status
        const statusConfig = {
            'disponibile': { color: '#10B981', label: 'Disponibile' },
            'manutenzione': { color: '#F59E0B', label: 'In Manutenzione' },
            'chiuso': { color: '#EF4444', label: 'Chiuso' }
        };
        const status = statusConfig[campo.stato] || statusConfig.disponibile;
        const statusBadge = document.getElementById('detailStatus');
        statusBadge.querySelector('.status-dot-modal').style.background = status.color;
        statusBadge.querySelector('.status-text-modal').textContent = status.label;
        statusBadge.style.setProperty('--status-color', status.color);
        
        // Info
        const superficieNames = {
            'erba_sintetica': 'Erba sintetica',
            'erba_naturale': 'Erba naturale',
            'parquet': 'Parquet',
            'cemento': 'Cemento',
            'terra_battuta': 'Terra battuta',
            'resina': 'Resina'
        };
        document.getElementById('detailSuperficie').textContent = superficieNames[campo.tipo_superficie] || campo.tipo_superficie;
        document.getElementById('detailCapienza').textContent = `${campo.capienza_max} giocatori`;
        document.getElementById('detailOrario').textContent = `${campo.orario_apertura.substring(0,5)} - ${campo.orario_chiusura.substring(0,5)}`;
        
        if (campo.lunghezza_m && campo.larghezza_m) {
            document.getElementById('detailDimensioni').textContent = `${campo.lunghezza_m}m x ${campo.larghezza_m}m`;
        } else {
            document.getElementById('detailDimensioni').textContent = 'Non specificato';
        }
        
        // Servizi
        const serviziContainer = document.getElementById('detailServizi');
        serviziContainer.innerHTML = '';
        
        const serviziList = [
            { key: 'illuminazione_notturna', icon: '💡', name: 'Illuminazione' },
            { key: 'spogliatoi', icon: '🚿', name: 'Spogliatoi' },
            { key: 'docce', icon: '🚿', name: 'Docce' },
            { key: 'parcheggio', icon: '🅿️', name: 'Parcheggio' },
            { key: 'distributori', icon: '💧', name: 'Distributori' },
            { key: 'noleggio_attrezzatura', icon: '🎾', name: 'Noleggio' },
            { key: 'bar_ristoro', icon: '☕', name: 'Bar/Ristoro' }
        ];
        
        serviziList.forEach(s => {
            if (servizi && servizi[s.key]) {
                const tag = document.createElement('span');
                tag.className = 'service-tag';
                tag.innerHTML = `${s.icon} ${s.name}`;
                serviziContainer.appendChild(tag);
            }
        });
        
        if (serviziContainer.innerHTML === '') {
            serviziContainer.innerHTML = '<span class="text-muted">Nessun servizio disponibile</span>';
        }
        
        // Stats
        document.getElementById('detailPrenOggi').textContent = campo.prenotazioni_oggi || 0;
        document.getElementById('detailPrenSett').textContent = campo.prenotazioni_settimana || 0;
        document.getElementById('detailUtilizzo').textContent = Math.min(100, (campo.prenotazioni_settimana || 0) * 3) + '%';
        document.getElementById('detailNumRec').textContent = campo.num_recensioni || 0;
        
        // Rating
        const rating = parseFloat(campo.rating_medio) || 0;
        document.getElementById('detailRatingBig').textContent = rating.toFixed(1);
        document.getElementById('detailRatingInfo').textContent = `Basato su ${campo.num_recensioni || 0} recensioni`;
        
        const starsContainer = document.getElementById('detailStars');
        starsContainer.innerHTML = '';
        for (let i = 1; i <= 5; i++) {
            const star = document.createElement('span');
            star.className = 'star ' + (i <= rating ? 'filled' : 'empty');
            star.textContent = '★';
            starsContainer.appendChild(star);
        }
        
        // Recensioni
        const recensioniList = document.getElementById('detailRecensioniList');
        recensioniList.innerHTML = '';
        
        if (recensioni.length > 0) {
            recensioni.forEach(rev => {
                const ratingColor = rev.rating_generale >= 4 ? '#10B981' : (rev.rating_generale >= 3 ? '#F59E0B' : '#EF4444');
                recensioniList.innerHTML += `
                    <div class="review-item" style="--rating-color: ${ratingColor}">
                        <div class="review-item-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar-sm">${rev.utente_iniziale || 'U'}</div>
                                <div>
                                    <div class="fw-semibold">${rev.utente_nome}</div>
                                    <div class="text-muted small">${formatTimeAgo(rev.created_at)}</div>
                                </div>
                            </div>
                            <div class="review-rating">
                                ${Array(5).fill().map((_, i) => `<span class="star ${i < rev.rating_generale ? 'filled' : 'empty'}">★</span>`).join('')}
                            </div>
                        </div>
                        <p class="review-item-text">"${rev.commento || ''}"</p>
                    </div>
                `;
            });
        } else {
            recensioniList.innerHTML = '<div class="empty-tab"><p>Nessuna recensione per questo campo</p></div>';
        }
        
        // Manutenzione
        const manutenzioneContent = document.getElementById('detailManutenzioneContent');
        if (campo.stato === 'manutenzione') {
            manutenzioneContent.innerHTML = `
                <div class="manutenzione-alert">
                    <span class="manutenzione-icon">🔧</span>
                    <div>
                        <div class="fw-bold" style="color: #F59E0B">Campo in Manutenzione</div>
                        <div class="text-muted small">Il campo non è prenotabile</div>
                    </div>
                    <button class="btn-success-custom btn-sm ms-auto" onclick="terminaManutenzione(${campo.campo_id})">
                        Termina Manutenzione
                    </button>
                </div>
            `;
        } else {
            manutenzioneContent.innerHTML = `
                <div class="manutenzione-ok">
                    <span class="manutenzione-icon">✅</span>
                    <p>Nessuna manutenzione programmata</p>
                    <small class="text-muted">Il campo è operativo</small>
                </div>
            `;
        }
    }
    
    // ============================================================================
    // FORMATO TEMPO RELATIVO
    // ============================================================================
    function formatTimeAgo(datetime) {
        const now = new Date();
        const date = new Date(datetime);
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMins / 60);
        const diffDays = Math.floor(diffHours / 24);
        
        if (diffDays > 0) return diffDays + ' giorn' + (diffDays > 1 ? 'i' : 'o') + ' fa';
        if (diffHours > 0) return diffHours + ' or' + (diffHours > 1 ? 'e' : 'a') + ' fa';
        if (diffMins > 0) return diffMins + ' min fa';
        return 'ora';
    }
    
    // ============================================================================
    // SALVA NUOVO CAMPO
    // ============================================================================
    document.getElementById('btnSalvaCampo').addEventListener('click', function() {
        const form = document.getElementById('formNuovoCampo');
        const formData = new FormData(form);
        formData.append('action', 'create');
        formData.append('ajax', '1');
        
        fetch('gestione-campi.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('modalNuovoCampo')).hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Errore nella creazione', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Errore di connessione', 'error');
        });
    });
    
    // ============================================================================
    // ELIMINA CAMPO
    // ============================================================================
    document.getElementById('btnEliminaCampo').addEventListener('click', function() {
        if (!currentCampoId) return;
        
        if (!confirm('Sei sicuro di voler eliminare questo campo? L\'operazione non può essere annullata.')) {
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('campo_id', currentCampoId);
        formData.append('ajax', '1');
        
        fetch('gestione-campi.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('modalDettaglioCampo')).hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Errore nell\'eliminazione', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Errore di connessione', 'error');
        });
    });
    
    // ============================================================================
    // PROGRAMMA MANUTENZIONE
    // ============================================================================
    document.getElementById('btnProgrammaManutenzione').addEventListener('click', function() {
        if (!currentCampoId) return;
        
        document.getElementById('blocco_campo_id').value = currentCampoId;
        document.getElementById('manutenzioneSubtitle').textContent = document.getElementById('detailNome').textContent;
        
        // Set default dates
        const today = new Date().toISOString().split('T')[0];
        document.querySelector('[name="data_inizio"]').value = today;
        document.querySelector('[name="data_fine"]').value = today;
        
        new bootstrap.Modal(document.getElementById('modalBloccoManutenzione')).show();
    });
    
    document.getElementById('btnCreaBlocco').addEventListener('click', function() {
        const form = document.getElementById('formBloccoManutenzione');
        const formData = new FormData(form);
        formData.append('action', 'blocco_manutenzione');
        formData.append('ajax', '1');
        
        fetch('gestione-campi.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('modalBloccoManutenzione')).hide();
                bootstrap.Modal.getInstance(document.getElementById('modalDettaglioCampo')).hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Errore nella creazione del blocco', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Errore di connessione', 'error');
        });
    });
    
    // Termina manutenzione
    window.terminaManutenzione = function(campoId) {
        const formData = new FormData();
        formData.append('action', 'change_status');
        formData.append('campo_id', campoId);
        formData.append('stato', 'disponibile');
        formData.append('ajax', '1');
        
        fetch('gestione-campi.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Manutenzione terminata', 'success');
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
    
});
</script>