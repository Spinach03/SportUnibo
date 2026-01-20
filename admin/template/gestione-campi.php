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
                
                <?php if (isset($campo['manutenzioni_future']) && $campo['manutenzioni_future'] > 0 && $campo['stato'] !== 'chiuso'): ?>
                <!-- Indicatore Manutenzione Prevista -->
                <div class="field-status-secondary">
                    <span class="status-secondary-text">⚠️ Manutenzione prevista</span>
                </div>
                <?php endif; ?>
                
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
     MODAL: NUOVO CAMPO - Bootstrap Design
     ============================================================================ -->
<div class="modal fade" id="modalNuovoCampo" tabindex="-1" aria-labelledby="modalNuovoCampoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content nuovo-campo-modal">
            <!-- Header -->
            <div class="modal-header nuovo-campo-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon">🏟️</div>
                    <div>
                        <h5 class="modal-title" id="modalNuovoCampoLabel">Nuovo Campo</h5>
                        <p class="modal-subtitle mb-0">Aggiungi un nuovo campo sportivo</p>
                    </div>
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Chiudi">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <!-- Body -->
            <div class="modal-body nuovo-campo-body">
                <form id="formNuovoCampo" novalidate>
                    <!-- Riga 1: Nome Campo + Sport -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="nc-label">Nome Campo <span class="text-danger">*</span></label>
                            <input type="text" class="nc-input" name="nome" placeholder="Es. Campo Calcetto Nord" required>
                        </div>
                        <div class="col-md-5">
                            <label class="nc-label">Sport <span class="text-danger">*</span></label>
                            <select class="nc-select" name="sport_id" required>
                                <?php foreach ($sports as $sport): 
                                    $sportEmoji = getEmojiFromIcona($sport['icona'] ?? '');
                                ?>
                                <option value="<?php echo $sport['sport_id']; ?>" data-icon="<?php echo $sportEmoji; ?>">
                                    <?php echo $sportEmoji; ?> <?php echo htmlspecialchars($sport['nome']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Riga 2: Tipo + Superficie -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="nc-label">Tipo <span class="text-danger">*</span></label>
                            <div class="nc-btn-group">
                                <input type="radio" class="btn-check" name="tipo_campo" id="ncTipoIndoor" value="indoor">
                                <label class="nc-btn-option" for="ncTipoIndoor">
                                    <span class="nc-btn-icon">🏠</span> Indoor
                                </label>
                                
                                <input type="radio" class="btn-check" name="tipo_campo" id="ncTipoOutdoor" value="outdoor" checked>
                                <label class="nc-btn-option" for="ncTipoOutdoor">
                                    <span class="nc-btn-icon">🌳</span> Outdoor
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="nc-label">Superficie <span class="text-danger">*</span></label>
                            <select class="nc-select" name="tipo_superficie" required>
                                <option value="" disabled selected>Seleziona...</option>
                                <option value="erba_sintetica">Erba sintetica</option>
                                <option value="erba_naturale">Erba naturale</option>
                                <option value="parquet">Parquet</option>
                                <option value="cemento">Cemento</option>
                                <option value="terra_battuta">Terra battuta</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Riga 3: Capienza + Posizione -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="nc-label">Capienza Max <span class="text-danger">*</span></label>
                            <input type="number" class="nc-input" name="capienza_max" placeholder="Es. 10" min="1" required>
                        </div>
                        <div class="col-md-8">
                            <label class="nc-label">Posizione <span class="text-danger">*</span></label>
                            <input type="text" class="nc-input" name="location" placeholder="Es. Zona Nord - Edificio Sport" required>
                        </div>
                    </div>
                    
                    <!-- Riga 4: Orari -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="nc-label">Orario Apertura <span class="text-danger">*</span></label>
                            <div class="nc-input-icon-wrapper">
                                <input type="time" class="nc-input nc-input-time" name="orario_apertura" value="08:00" required>
                                <span class="nc-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="nc-label">Orario Chiusura <span class="text-danger">*</span></label>
                            <div class="nc-input-icon-wrapper">
                                <input type="time" class="nc-input nc-input-time" name="orario_chiusura" value="22:00" required>
                                <span class="nc-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Servizi Disponibili -->
                    <div class="mb-3">
                        <label class="nc-label">Servizi Disponibili</label>
                        <div class="nc-services-grid">
                            <label class="nc-service-item">
                                <input type="checkbox" name="servizi[]" value="illuminazione_notturna">
                                <div class="nc-service-box">
                                    <span class="nc-service-icon">💡</span>
                                    <span class="nc-service-name">Illuminazione</span>
                                </div>
                            </label>
                            <label class="nc-service-item">
                                <input type="checkbox" name="servizi[]" value="spogliatoi">
                                <div class="nc-service-box">
                                    <span class="nc-service-icon">🚿</span>
                                    <span class="nc-service-name">Spogliatoi</span>
                                </div>
                            </label>
                            <label class="nc-service-item">
                                <input type="checkbox" name="servizi[]" value="docce">
                                <div class="nc-service-box">
                                    <span class="nc-service-icon">🚿</span>
                                    <span class="nc-service-name">Docce</span>
                                </div>
                            </label>
                            <label class="nc-service-item">
                                <input type="checkbox" name="servizi[]" value="parcheggio">
                                <div class="nc-service-box">
                                    <span class="nc-service-icon">🅿️</span>
                                    <span class="nc-service-name">Parcheggio</span>
                                </div>
                            </label>
                            <label class="nc-service-item">
                                <input type="checkbox" name="servizi[]" value="noleggio_attrezzatura">
                                <div class="nc-service-box">
                                    <span class="nc-service-icon">🎾</span>
                                    <span class="nc-service-name">Noleggio attrezzatura</span>
                                </div>
                            </label>
                            <label class="nc-service-item">
                                <input type="checkbox" name="servizi[]" value="bar_ristoro">
                                <div class="nc-service-box">
                                    <span class="nc-service-icon">☕</span>
                                    <span class="nc-service-name">Bar/Ristoro</span>
                                </div>
                            </label>
                            <label class="nc-service-item">
                                <input type="checkbox" name="servizi[]" value="distributori">
                                <div class="nc-service-box">
                                    <span class="nc-service-icon">💧</span>
                                    <span class="nc-service-name">Distributori acqua</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Descrizione -->
                    <div class="mb-0">
                        <label class="nc-label">Descrizione</label>
                        <textarea class="nc-textarea" name="descrizione" rows="3" placeholder="Descrivi le caratteristiche del campo..."></textarea>
                    </div>
                    
                    <!-- Hidden: stato default disponibile -->
                    <input type="hidden" name="stato" value="disponibile">
                </form>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer nuovo-campo-footer">
                <button type="button" class="nc-btn-cancel" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="nc-btn-submit" id="btnSalvaCampo">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Crea Campo
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
                        <div id="calendarioContent">
                            <!-- Prenotazioni Oggi -->
                            <div class="prenotazioni-section" id="prenotazioniOggiSection">
                                <div class="prenotazioni-section-title">
                                    📅 Prenotazioni di Oggi
                                    <span class="badge badge-oggi" id="countOggi">0</span>
                                </div>
                                <div id="prenotazioniOggiList">
                                    <div class="no-prenotazioni">Nessuna prenotazione per oggi</div>
                                </div>
                            </div>
                            
                            <!-- Prenotazioni Future -->
                            <div class="prenotazioni-section" id="prenotazioniFutureSection">
                                <div class="prenotazioni-section-title">
                                    🔜 Prenotazioni Future
                                    <span class="badge badge-future" id="countFuture">0</span>
                                </div>
                                <div id="prenotazioniFutureList">
                                    <div class="no-prenotazioni">Nessuna prenotazione futura</div>
                                </div>
                            </div>
                            
                            <!-- Prenotazioni Passate -->
                            <div class="prenotazioni-section" id="prenotazioniPassateSection">
                                <div class="prenotazioni-section-title">
                                    ✅ Prenotazioni Completate
                                    <span class="badge badge-passate" id="countPassate">0</span>
                                </div>
                                <div id="prenotazioniPassateList">
                                    <div class="no-prenotazioni">Nessuna prenotazione completata</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Statistiche -->
                    <div class="tab-pane fade" id="tabStats">
                        <div class="stats-chart-container">
                            <div class="stats-chart-title">📊 Prenotazioni Ultimi 7 Giorni</div>
                            <div class="chart-wrapper">
                                <div class="bar-chart" id="weeklyChart">
                                    <!-- Bars generate via JS -->
                                </div>
                            </div>
                            <div class="stats-summary">
                                <div class="stats-summary-item">
                                    <div class="stats-summary-value" id="statsTotale">0</div>
                                    <div class="stats-summary-label">Totale Settimana</div>
                                </div>
                                <div class="stats-summary-item">
                                    <div class="stats-summary-value" id="statsMedia">0</div>
                                    <div class="stats-summary-label">Media Giornaliera</div>
                                </div>
                                <div class="stats-summary-item">
                                    <div class="stats-summary-value" id="statsPicco">-</div>
                                    <div class="stats-summary-label">Giorno di Picco</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Recensioni -->
                    <div class="tab-pane fade" id="tabRecensioni">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="tab-section-title mb-0">⭐ Recensioni Utenti</h4>
                            <a href="recensioni.php" class="btn-add-new btn-sm">
                                📝 Gestisci Recensioni
                            </a>
                        </div>
                        <div class="recensioni-list" id="detailRecensioniList">
                            <!-- Recensioni caricate via JS -->
                        </div>
                    </div>
                    
                    <!-- Tab Manutenzione -->
                    <div class="tab-pane fade" id="tabManutenzione">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="tab-section-title mb-0">🔧 Gestione Manutenzione</h4>
                            <button class="btn-add-new btn-sm" id="btnProgrammaManutenzione">
                                + Programma Manutenzione
                            </button>
                        </div>
                        <div id="detailManutenzioneContent">
                            <!-- Contenuto caricato via JS -->
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer modal-footer-dark justify-content-between">
                <div class="d-flex gap-2">
                    <button type="button" class="btn-danger-outline btn-sm" id="btnEliminaCampo">
                        🗑️ Elimina Campo
                    </button>
                    <button type="button" class="btn-warning-outline btn-sm" id="btnChiudiCampo">
                        🚫 Chiudi Campo
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn-add-new" id="btnModificaCampo">
                        ✏️ Modifica Campo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: BLOCCO MANUTENZIONE - Layout Orizzontale
     ============================================================================ -->
<div class="modal fade" id="modalBloccoManutenzione" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content nuovo-campo-modal">
            <!-- Header -->
            <div class="modal-header nuovo-campo-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon">🔧</div>
                    <div>
                        <h5 class="modal-title">Programma Manutenzione</h5>
                        <p class="modal-subtitle mb-0" id="manutenzioneSubtitle">Campo selezionato</p>
                    </div>
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Chiudi">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <!-- Body -->
            <div class="modal-body nuovo-campo-body">
                <form id="formBloccoManutenzione">
                    <input type="hidden" name="campo_id" id="blocco_campo_id">
                    
                    <!-- Riga 1: Date e Ore -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="nc-label">Data Inizio <span class="text-danger">*</span></label>
                            <input type="date" class="nc-input" name="data_inizio" required>
                        </div>
                        <div class="col-md-3">
                            <label class="nc-label">Ora Inizio</label>
                            <input type="time" class="nc-input" name="ora_inizio" value="08:00">
                        </div>
                        <div class="col-md-3">
                            <label class="nc-label">Data Fine <span class="text-danger">*</span></label>
                            <input type="date" class="nc-input" name="data_fine" required>
                        </div>
                        <div class="col-md-3">
                            <label class="nc-label">Ora Fine</label>
                            <input type="time" class="nc-input" name="ora_fine" value="22:00">
                        </div>
                    </div>
                    
                    <!-- Riga 2: Tipo Blocco -->
                    <div class="mb-3">
                        <label class="nc-label">Tipo Blocco</label>
                        <select class="nc-select" name="tipo_blocco">
                            <option value="manutenzione_ordinaria">🔧 Manutenzione Ordinaria</option>
                            <option value="manutenzione_straordinaria">⚠️ Manutenzione Straordinaria</option>
                            <option value="evento_speciale">🎉 Evento Speciale</option>
                            <option value="chiusura_temporanea">🚫 Chiusura Temporanea</option>
                        </select>
                    </div>
                    
                    <!-- Riga 3: Motivo -->
                    <div class="mb-3">
                        <label class="nc-label">Motivo</label>
                        <textarea class="nc-textarea" name="motivo" rows="3" placeholder="Descrivi il motivo del blocco..."></textarea>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer nuovo-campo-footer">
                <button type="button" class="nc-btn-cancel" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="nc-btn-submit" id="btnCreaBlocco" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    🔧 Crea Blocco
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: MODIFICA CAMPO - Layout Orizzontale
     ============================================================================ -->
<div class="modal fade" id="modalModificaCampo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content nuovo-campo-modal">
            <!-- Header -->
            <div class="modal-header nuovo-campo-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon">✏️</div>
                    <div>
                        <h5 class="modal-title">Modifica Campo</h5>
                        <p class="modal-subtitle mb-0" id="modificaCampoSubtitle">Modifica i dettagli del campo</p>
                    </div>
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Chiudi">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <!-- Body -->
            <div class="modal-body nuovo-campo-body">
                <form id="formModificaCampo">
                    <input type="hidden" name="campo_id" id="modifica_campo_id">
                    <input type="hidden" name="stato" id="mod_stato">
                    
                    <!-- Riga 1: Nome Campo + Sport -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="nc-label">Nome Campo <span class="text-danger">*</span></label>
                            <input type="text" class="nc-input" name="nome" id="mod_nome" placeholder="Es. Campo Calcetto Nord" required>
                        </div>
                        <div class="col-md-5">
                            <label class="nc-label">Sport <span class="text-danger">*</span></label>
                            <select class="nc-select" name="sport_id" id="mod_sport_id" required>
                                <?php foreach ($sports as $sport): 
                                    $sportEmoji = getEmojiFromIcona($sport['icona'] ?? '');
                                ?>
                                <option value="<?php echo $sport['sport_id']; ?>">
                                    <?php echo $sportEmoji; ?> <?php echo htmlspecialchars($sport['nome']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Riga 2: Tipo + Superficie -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="nc-label">Tipo <span class="text-danger">*</span></label>
                            <div class="nc-btn-group">
                                <input type="radio" class="btn-check" name="tipo_campo" id="modTipoIndoor" value="indoor">
                                <label class="nc-btn-option" for="modTipoIndoor">
                                    <span class="nc-btn-icon">🏠</span> Indoor
                                </label>
                                
                                <input type="radio" class="btn-check" name="tipo_campo" id="modTipoOutdoor" value="outdoor" checked>
                                <label class="nc-btn-option" for="modTipoOutdoor">
                                    <span class="nc-btn-icon">🌳</span> Outdoor
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="nc-label">Superficie <span class="text-danger">*</span></label>
                            <select class="nc-select" name="tipo_superficie" id="mod_tipo_superficie" required>
                                <option value="" disabled>Seleziona...</option>
                                <option value="erba_sintetica">Erba sintetica</option>
                                <option value="erba_naturale">Erba naturale</option>
                                <option value="parquet">Parquet</option>
                                <option value="cemento">Cemento</option>
                                <option value="terra_battuta">Terra battuta</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Riga 3: Capienza + Posizione -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="nc-label">Capienza Max <span class="text-danger">*</span></label>
                            <input type="number" class="nc-input" name="capienza_max" id="mod_capienza_max" min="2" max="100" required>
                        </div>
                        <div class="col-md-8">
                            <label class="nc-label">Posizione <span class="text-danger">*</span></label>
                            <input type="text" class="nc-input" name="location" id="mod_location" placeholder="Es. Blocco A - Piano Terra" required>
                        </div>
                    </div>
                    
                    <!-- Riga 4: Orari -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="nc-label">Orario Apertura</label>
                            <input type="time" class="nc-input" name="orario_apertura" id="mod_orario_apertura" value="08:00">
                        </div>
                        <div class="col-md-6">
                            <label class="nc-label">Orario Chiusura</label>
                            <input type="time" class="nc-input" name="orario_chiusura" id="mod_orario_chiusura" value="22:00">
                        </div>
                    </div>
                    
                    <!-- Riga 5: Dimensioni (opzionali) -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="nc-label">Lunghezza (m)</label>
                            <input type="number" class="nc-input" name="lunghezza_m" id="mod_lunghezza_m" min="1" max="200" placeholder="Opzionale">
                        </div>
                        <div class="col-md-6">
                            <label class="nc-label">Larghezza (m)</label>
                            <input type="number" class="nc-input" name="larghezza_m" id="mod_larghezza_m" min="1" max="100" placeholder="Opzionale">
                        </div>
                    </div>
                    
                    <!-- Servizi Disponibili -->
                    <div class="mb-3">
                        <label class="nc-label">Servizi Disponibili</label>
                        <div class="nc-services-grid">
                            <label class="nc-service-item">
                                <input type="checkbox" name="servizi[]" value="illuminazione_notturna" id="mod_serv_illuminazione">
                                <div class="nc-service-box">
                                    <span class="nc-service-icon">💡</span>
                                    <span class="nc-service-name">Illuminazione</span>
                                </div>
                            </label>
                            <label class="nc-service-item">
                                <input type="checkbox" name="servizi[]" value="spogliatoi" id="mod_serv_spogliatoi">
                                <div class="nc-service-box">
                                    <span class="nc-service-icon">🚿</span>
                                    <span class="nc-service-name">Spogliatoi</span>
                                </div>
                            </label>
                            <label class="nc-service-item">
                                <input type="checkbox" name="servizi[]" value="docce" id="mod_serv_docce">
                                <div class="nc-service-box">
                                    <span class="nc-service-icon">🚿</span>
                                    <span class="nc-service-name">Docce</span>
                                </div>
                            </label>
                            <label class="nc-service-item">
                                <input type="checkbox" name="servizi[]" value="parcheggio" id="mod_serv_parcheggio">
                                <div class="nc-service-box">
                                    <span class="nc-service-icon">🅿️</span>
                                    <span class="nc-service-name">Parcheggio</span>
                                </div>
                            </label>
                            <label class="nc-service-item">
                                <input type="checkbox" name="servizi[]" value="noleggio_attrezzatura" id="mod_serv_noleggio">
                                <div class="nc-service-box">
                                    <span class="nc-service-icon">🎾</span>
                                    <span class="nc-service-name">Noleggio attrezzatura</span>
                                </div>
                            </label>
                            <label class="nc-service-item">
                                <input type="checkbox" name="servizi[]" value="bar_ristoro" id="mod_serv_bar">
                                <div class="nc-service-box">
                                    <span class="nc-service-icon">☕</span>
                                    <span class="nc-service-name">Bar/Ristoro</span>
                                </div>
                            </label>
                            <label class="nc-service-item">
                                <input type="checkbox" name="servizi[]" value="distributori" id="mod_serv_distributori">
                                <div class="nc-service-box">
                                    <span class="nc-service-icon">💧</span>
                                    <span class="nc-service-name">Distributori acqua</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Descrizione -->
                    <div class="mb-3">
                        <label class="nc-label">Descrizione</label>
                        <textarea class="nc-textarea" name="descrizione" id="mod_descrizione" rows="2" placeholder="Descrizione opzionale del campo..."></textarea>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer nuovo-campo-footer">
                <button type="button" class="nc-btn-cancel" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="nc-btn-submit" id="btnSalvaModifiche" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                    ✓ Salva Modifiche
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
    let currentBlocchiFuturi = [];
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
        
        // Salva i dati per il modal modifica
        currentCampoData = campo;
        currentServiziData = servizi;
        
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
            'disponibile': { color: '#10B981', label: 'Disponibile', class: '' },
            'manutenzione': { color: '#F59E0B', label: 'In Manutenzione', class: 'manutenzione' },
            'chiuso': { color: '#EF4444', label: 'Chiuso', class: 'chiuso' }
        };
        const status = statusConfig[campo.stato] || statusConfig.disponibile;
        const statusBadge = document.getElementById('detailStatus');
        
        // Rimuovi classi precedenti e aggiungi quella corretta
        statusBadge.classList.remove('manutenzione', 'chiuso');
        if (status.class) {
            statusBadge.classList.add(status.class);
        }
        
        statusBadge.querySelector('.status-dot-modal').style.background = status.color;
        statusBadge.querySelector('.status-text-modal').textContent = status.label;
        statusBadge.style.setProperty('--status-color', status.color);
        
        // Aggiorna testo bottone Chiudi/Riapri Campo
        const btnChiudiCampo = document.getElementById('btnChiudiCampo');
        if (campo.stato === 'chiuso') {
            btnChiudiCampo.innerHTML = '🔓 Riapri Campo';
        } else {
            btnChiudiCampo.innerHTML = '🚫 Chiudi Campo';
        }
        
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
        
        // Recensioni - Formattate meglio e ordinate per data
        const recensioniList = document.getElementById('detailRecensioniList');
        recensioniList.innerHTML = '';
        
        if (recensioni.length > 0) {
            // Ordina per data (più recente prima)
            const recensioniOrdinate = [...recensioni].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            
            recensioniOrdinate.forEach(rev => {
                const iniziale = (rev.utente_nome || 'U').charAt(0).toUpperCase();
                const dataFormatted = formatDataItaliana(rev.created_at);
                
                recensioniList.innerHTML += `
                    <div class="recensione-card">
                        <div class="recensione-header">
                            <div class="recensione-avatar">${iniziale}</div>
                            <div class="recensione-info">
                                <div class="recensione-nome">${rev.utente_nome || 'Utente'}</div>
                                <div class="recensione-data">${dataFormatted}</div>
                            </div>
                            <div class="recensione-rating">
                                ${Array(5).fill().map((_, i) => `<span class="star ${i < rev.rating_generale ? 'filled' : ''}">★</span>`).join('')}
                            </div>
                        </div>
                        <div class="recensione-testo">"${rev.commento || 'Nessun commento'}"</div>
                    </div>
                `;
            });
        } else {
            recensioniList.innerHTML = `
                <div class="no-recensioni">
                    <div class="no-recensioni-icon">⭐</div>
                    <p>Nessuna recensione per questo campo</p>
                </div>
            `;
        }
        
        // Manutenzione - Con gestione blocchi futuri e campo chiuso
        const manutenzioneContent = document.getElementById('detailManutenzioneContent');
        const btnProgrammaManutenzione = document.getElementById('btnProgrammaManutenzione');
        const blocchiManutenzione = data.blocchi_manutenzione || [];
        
        // Filtra blocchi futuri (data_inizio > oggi)
        const oggi = new Date();
        oggi.setHours(0, 0, 0, 0);
        const blocchiFuturi = blocchiManutenzione.filter(b => {
            const dataInizio = new Date(b.data_inizio);
            dataInizio.setHours(0, 0, 0, 0);
            return dataInizio > oggi;
        });
        
        if (campo.stato === 'chiuso') {
            // Campo chiuso - Non operativo
            manutenzioneContent.innerHTML = `
                <div class="manutenzione-status chiuso">
                    <div class="manutenzione-status-icon">🚫</div>
                    <div class="manutenzione-status-text">
                        <div class="manutenzione-status-title" style="color: #ef4444;">Il campo non è operativo</div>
                        <div class="manutenzione-status-subtitle">Il campo è attualmente chiuso</div>
                    </div>
                </div>
            `;
            // Disabilita bottone programma manutenzione
            btnProgrammaManutenzione.disabled = true;
            btnProgrammaManutenzione.style.opacity = '0.5';
            btnProgrammaManutenzione.style.cursor = 'not-allowed';
            btnProgrammaManutenzione.title = 'Non puoi programmare manutenzione su un campo chiuso';
        } else if (campo.stato === 'manutenzione') {
            // Campo in manutenzione attiva
            manutenzioneContent.innerHTML = `
                <div class="manutenzione-status in-manutenzione">
                    <div class="manutenzione-status-icon">🔧</div>
                    <div class="manutenzione-status-text">
                        <div class="manutenzione-status-title">Campo in Manutenzione</div>
                        <div class="manutenzione-status-subtitle">Il campo non è prenotabile al momento</div>
                    </div>
                    <button class="btn-success-custom btn-sm" onclick="terminaManutenzione(${campo.campo_id})">
                        ✓ Termina Manutenzione
                    </button>
                </div>
            `;
            btnProgrammaManutenzione.disabled = false;
            btnProgrammaManutenzione.style.opacity = '1';
            btnProgrammaManutenzione.style.cursor = 'pointer';
            btnProgrammaManutenzione.title = '';
        } else if (blocchiFuturi.length > 0) {
            // Campo operativo con manutenzione futura programmata
            const prossimoBlocko = blocchiFuturi[0];
            const dataInizio = new Date(prossimoBlocko.data_inizio).toLocaleDateString('it-IT', { day: 'numeric', month: 'long', year: 'numeric' });
            const dataFine = new Date(prossimoBlocko.data_fine).toLocaleDateString('it-IT', { day: 'numeric', month: 'long', year: 'numeric' });
            
            let blocchiHtml = blocchiFuturi.map(b => {
                const dIn = new Date(b.data_inizio).toLocaleDateString('it-IT', { day: 'numeric', month: 'short' });
                const dFi = new Date(b.data_fine).toLocaleDateString('it-IT', { day: 'numeric', month: 'short' });
                const tipoLabel = {
                    'manutenzione_ordinaria': '🔧 Ordinaria',
                    'manutenzione_straordinaria': '⚠️ Straordinaria',
                    'evento_speciale': '🎉 Evento',
                    'chiusura_temporanea': '🚫 Chiusura'
                }[b.tipo_blocco] || b.tipo_blocco;
                return `
                    <div class="manutenzione-item">
                        <div class="manutenzione-item-icon">📅</div>
                        <div class="manutenzione-item-info">
                            <div class="manutenzione-item-tipo">${tipoLabel}</div>
                            <div class="manutenzione-item-date">${dIn} - ${dFi}</div>
                            ${b.motivo ? `<div class="manutenzione-item-motivo">${b.motivo}</div>` : ''}
                        </div>
                    </div>
                `;
            }).join('');
            
            manutenzioneContent.innerHTML = `
                <div class="manutenzione-status">
                    <div class="manutenzione-status-icon">✅</div>
                    <div class="manutenzione-status-text">
                        <div class="manutenzione-status-title">Campo Operativo</div>
                        <div class="manutenzione-status-subtitle">Il campo è attualmente disponibile</div>
                    </div>
                </div>
                <div class="manutenzione-prevista mt-3">
                    <div class="manutenzione-prevista-header">
                        <span class="manutenzione-prevista-icon">⚠️</span>
                        <span class="manutenzione-prevista-title">Manutenzione Prevista</span>
                    </div>
                    ${blocchiHtml}
                </div>
            `;
            btnProgrammaManutenzione.disabled = false;
            btnProgrammaManutenzione.style.opacity = '1';
            btnProgrammaManutenzione.style.cursor = 'pointer';
            btnProgrammaManutenzione.title = '';
        } else {
            // Campo operativo senza manutenzione programmata
            manutenzioneContent.innerHTML = `
                <div class="manutenzione-status">
                    <div class="manutenzione-status-icon">✅</div>
                    <div class="manutenzione-status-text">
                        <div class="manutenzione-status-title">Campo Operativo</div>
                        <div class="manutenzione-status-subtitle">Nessuna manutenzione programmata</div>
                    </div>
                </div>
            `;
            btnProgrammaManutenzione.disabled = false;
            btnProgrammaManutenzione.style.opacity = '1';
            btnProgrammaManutenzione.style.cursor = 'pointer';
            btnProgrammaManutenzione.title = '';
        }
        
        // Salva i blocchi futuri per usarli nella card
        currentBlocchiFuturi = blocchiFuturi;
        
        // Calendario - Carica prenotazioni
        loadPrenotazioniCalendario(campo.campo_id);
        
        // Statistiche - Genera grafico
        generateStatsChart(campo);
    }
    
    // ============================================================================
    // FORMATO DATA ITALIANA
    // ============================================================================
    function formatDataItaliana(datetime) {
        const date = new Date(datetime);
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        return date.toLocaleDateString('it-IT', options);
    }
    
    // ============================================================================
    // CARICA PRENOTAZIONI CALENDARIO
    // ============================================================================
    function loadPrenotazioniCalendario(campoId) {
        // Simulazione dati prenotazioni (in produzione si fa una chiamata AJAX)
        const oggi = new Date();
        oggi.setHours(0, 0, 0, 0);
        
        // Per ora mostriamo placeholder - in produzione si caricano dal server
        fetch(`gestione-campi.php?action=get_prenotazioni&campo_id=${campoId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.prenotazioni) {
                renderPrenotazioni(data.prenotazioni);
            } else {
                renderPrenotazioniPlaceholder();
            }
        })
        .catch(() => {
            renderPrenotazioniPlaceholder();
        });
    }
    
    function renderPrenotazioni(prenotazioni) {
        const oggi = new Date();
        oggi.setHours(0, 0, 0, 0);
        const domani = new Date(oggi);
        domani.setDate(domani.getDate() + 1);
        
        const prenotazioniOggi = [];
        const prenotazioniFuture = [];
        const prenotazioniPassate = [];
        
        prenotazioni.forEach(p => {
            const dataPrenotazione = new Date(p.data_prenotazione);
            dataPrenotazione.setHours(0, 0, 0, 0);
            
            if (dataPrenotazione.getTime() === oggi.getTime()) {
                prenotazioniOggi.push(p);
            } else if (dataPrenotazione > oggi) {
                prenotazioniFuture.push(p);
            } else {
                prenotazioniPassate.push(p);
            }
        });
        
        // Ordina
        prenotazioniOggi.sort((a, b) => a.ora_inizio.localeCompare(b.ora_inizio));
        prenotazioniFuture.sort((a, b) => new Date(a.data_prenotazione) - new Date(b.data_prenotazione));
        prenotazioniPassate.sort((a, b) => new Date(b.data_prenotazione) - new Date(a.data_prenotazione));
        
        // Render
        document.getElementById('countOggi').textContent = prenotazioniOggi.length;
        document.getElementById('countFuture').textContent = prenotazioniFuture.length;
        document.getElementById('countPassate').textContent = prenotazioniPassate.length;
        
        document.getElementById('prenotazioniOggiList').innerHTML = prenotazioniOggi.length > 0 
            ? prenotazioniOggi.map(p => renderPrenotazioneCard(p, 'oggi')).join('')
            : '<div class="no-prenotazioni">Nessuna prenotazione per oggi</div>';
            
        document.getElementById('prenotazioniFutureList').innerHTML = prenotazioniFuture.length > 0
            ? prenotazioniFuture.slice(0, 10).map(p => renderPrenotazioneCard(p, 'futura')).join('')
            : '<div class="no-prenotazioni">Nessuna prenotazione futura</div>';
            
        document.getElementById('prenotazioniPassateList').innerHTML = prenotazioniPassate.length > 0
            ? prenotazioniPassate.slice(0, 5).map(p => renderPrenotazioneCard(p, 'passata')).join('')
            : '<div class="no-prenotazioni">Nessuna prenotazione completata</div>';
    }
    
    function renderPrenotazioneCard(p, tipo) {
        const classTipo = tipo === 'oggi' ? 'oggi' : (tipo === 'passata' ? 'passata' : '');
        const statusClass = tipo === 'passata' ? 'completata' : (tipo === 'oggi' ? 'in-corso' : 'confermata');
        const statusLabel = tipo === 'passata' ? 'Completata' : (tipo === 'oggi' ? 'Oggi' : 'Confermata');
        
        const data = new Date(p.data_prenotazione);
        const dataStr = data.toLocaleDateString('it-IT', { weekday: 'short', day: 'numeric', month: 'short' });
        
        return `
            <div class="prenotazione-card ${classTipo}">
                <div class="prenotazione-time">
                    <span class="prenotazione-ora">${p.ora_inizio.substring(0,5)}</span>
                    <span class="prenotazione-durata">${p.ora_fine.substring(0,5)}</span>
                </div>
                <div class="prenotazione-info">
                    <div class="prenotazione-utente">${p.utente_nome || 'Utente'}</div>
                    <div class="prenotazione-dettagli">${p.num_partecipanti || '-'} partecipanti</div>
                </div>
                <div class="prenotazione-data">${dataStr}</div>
                <span class="prenotazione-status ${statusClass}">${statusLabel}</span>
            </div>
        `;
    }
    
    function renderPrenotazioniPlaceholder() {
        document.getElementById('countOggi').textContent = '0';
        document.getElementById('countFuture').textContent = '0';
        document.getElementById('countPassate').textContent = '0';
        document.getElementById('prenotazioniOggiList').innerHTML = '<div class="no-prenotazioni">Nessuna prenotazione per oggi</div>';
        document.getElementById('prenotazioniFutureList').innerHTML = '<div class="no-prenotazioni">Nessuna prenotazione futura</div>';
        document.getElementById('prenotazioniPassateList').innerHTML = '<div class="no-prenotazioni">Nessuna prenotazione completata</div>';
    }
    
    // ============================================================================
    // GENERA GRAFICO STATISTICHE
    // ============================================================================
    function generateStatsChart(campo) {
        const chartContainer = document.getElementById('weeklyChart');
        const giorni = ['Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom'];
        
        // Genera dati casuali basati sulle prenotazioni settimanali
        const prenotazioniSett = campo.prenotazioni_settimana || 0;
        const mediaGiorno = Math.ceil(prenotazioniSett / 7);
        
        // Simula distribuzione (weekend più prenotazioni)
        const valori = [
            Math.floor(mediaGiorno * 0.8),
            Math.floor(mediaGiorno * 0.9),
            Math.floor(mediaGiorno * 1.0),
            Math.floor(mediaGiorno * 0.9),
            Math.floor(mediaGiorno * 1.2),
            Math.floor(mediaGiorno * 1.5),
            Math.floor(mediaGiorno * 1.3)
        ];
        
        const maxVal = Math.max(...valori, 1);
        
        chartContainer.innerHTML = valori.map((val, i) => `
            <div class="bar-item">
                <span class="bar-value">${val}</span>
                <div class="bar" style="height: ${(val / maxVal) * 180}px"></div>
                <span class="bar-label">${giorni[i]}</span>
            </div>
        `).join('');
        
        // Summary
        const totale = valori.reduce((a, b) => a + b, 0);
        const media = (totale / 7).toFixed(1);
        const piccoIndex = valori.indexOf(maxVal);
        
        document.getElementById('statsTotale').textContent = totale;
        document.getElementById('statsMedia').textContent = media;
        document.getElementById('statsPicco').textContent = giorni[piccoIndex];
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
    const btnSalvaCampo = document.getElementById('btnSalvaCampo');
    const formNuovoCampo = document.getElementById('formNuovoCampo');
    
    // Reset form quando il modal si chiude
    document.getElementById('modalNuovoCampo').addEventListener('hidden.bs.modal', function() {
        formNuovoCampo.reset();
        // Reset radio buttons
        document.getElementById('ncTipoOutdoor').checked = true;
        // Reset select superficie
        formNuovoCampo.querySelector('[name="tipo_superficie"]').selectedIndex = 0;
        // Remove validation classes
        formNuovoCampo.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    });
    
    btnSalvaCampo.addEventListener('click', function() {
        // Validazione
        const nome = formNuovoCampo.querySelector('[name="nome"]').value.trim();
        const sportId = formNuovoCampo.querySelector('[name="sport_id"]').value;
        const superficie = formNuovoCampo.querySelector('[name="tipo_superficie"]').value;
        const capienza = formNuovoCampo.querySelector('[name="capienza_max"]').value;
        const location = formNuovoCampo.querySelector('[name="location"]').value.trim();
        
        let isValid = true;
        
        // Reset validation
        formNuovoCampo.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        if (!nome) {
            formNuovoCampo.querySelector('[name="nome"]').classList.add('is-invalid');
            isValid = false;
        }
        if (!sportId) {
            formNuovoCampo.querySelector('[name="sport_id"]').classList.add('is-invalid');
            isValid = false;
        }
        if (!superficie) {
            formNuovoCampo.querySelector('[name="tipo_superficie"]').classList.add('is-invalid');
            isValid = false;
        }
        if (!capienza || parseInt(capienza) < 1) {
            formNuovoCampo.querySelector('[name="capienza_max"]').classList.add('is-invalid');
            isValid = false;
        }
        if (!location) {
            formNuovoCampo.querySelector('[name="location"]').classList.add('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            showToast('Compila tutti i campi obbligatori', 'error');
            return;
        }
        
        // Loading state
        const originalContent = btnSalvaCampo.innerHTML;
        btnSalvaCampo.disabled = true;
        btnSalvaCampo.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creazione...';
        
        const formData = new FormData(formNuovoCampo);
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
                btnSalvaCampo.disabled = false;
                btnSalvaCampo.innerHTML = originalContent;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Errore di connessione', 'error');
            btnSalvaCampo.disabled = false;
            btnSalvaCampo.innerHTML = originalContent;
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
    // CHIUDI/RIAPRI CAMPO
    // ============================================================================
    document.getElementById('btnChiudiCampo').addEventListener('click', function() {
        if (!currentCampoId) return;
        
        // Controlla lo stato attuale del campo
        const statusText = document.querySelector('#detailStatus .status-text-modal').textContent;
        const isChiuso = statusText === 'Chiuso';
        
        const azione = isChiuso ? 'riaprire' : 'chiudere';
        const nuovoStato = isChiuso ? 'disponibile' : 'chiuso';
        
        if (!confirm(`Sei sicuro di voler ${azione} questo campo?`)) {
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'update_stato');
        formData.append('campo_id', currentCampoId);
        formData.append('stato', nuovoStato);
        formData.append('ajax', '1');
        
        fetch('gestione-campi.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || `Campo ${isChiuso ? 'riaperto' : 'chiuso'} con successo`, 'success');
                bootstrap.Modal.getInstance(document.getElementById('modalDettaglioCampo')).hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Errore nell\'operazione', 'error');
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
        
        // Controlla se il bottone è disabilitato (campo chiuso)
        if (this.disabled) {
            showToast('Non puoi programmare manutenzione su un campo chiuso', 'error');
            return;
        }
        
        document.getElementById('blocco_campo_id').value = currentCampoId;
        document.getElementById('manutenzioneSubtitle').textContent = document.getElementById('detailNome').textContent;
        
        // Set default dates
        const today = new Date().toISOString().split('T')[0];
        document.querySelector('#formBloccoManutenzione [name="data_inizio"]').value = today;
        document.querySelector('#formBloccoManutenzione [name="data_fine"]').value = today;
        
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
    
    // ============================================================================
    // MODIFICA CAMPO
    // ============================================================================
    let currentCampoData = null;
    let currentServiziData = null;
    
    document.getElementById('btnModificaCampo').addEventListener('click', function() {
        if (!currentCampoId || !currentCampoData) return;
        
        const campo = currentCampoData;
        const servizi = currentServiziData || {};
        
        // Popola i campi del form
        document.getElementById('modifica_campo_id').value = campo.campo_id;
        document.getElementById('mod_stato').value = campo.stato || 'disponibile';
        document.getElementById('mod_nome').value = campo.nome || '';
        document.getElementById('mod_sport_id').value = campo.sport_id || '';
        document.getElementById('mod_tipo_superficie').value = campo.tipo_superficie || '';
        document.getElementById('mod_capienza_max').value = campo.capienza_max || '';
        document.getElementById('mod_location').value = campo.location || '';
        document.getElementById('mod_orario_apertura').value = (campo.orario_apertura || '08:00').substring(0, 5);
        document.getElementById('mod_orario_chiusura').value = (campo.orario_chiusura || '22:00').substring(0, 5);
        document.getElementById('mod_lunghezza_m').value = campo.lunghezza_m || '';
        document.getElementById('mod_larghezza_m').value = campo.larghezza_m || '';
        document.getElementById('mod_descrizione').value = campo.descrizione || '';
        
        // Tipo campo
        if (campo.tipo_campo === 'indoor') {
            document.getElementById('modTipoIndoor').checked = true;
        } else {
            document.getElementById('modTipoOutdoor').checked = true;
        }
        
        // Servizi
        document.getElementById('mod_serv_illuminazione').checked = servizi.illuminazione_notturna == 1;
        document.getElementById('mod_serv_spogliatoi').checked = servizi.spogliatoi == 1;
        document.getElementById('mod_serv_docce').checked = servizi.docce == 1;
        document.getElementById('mod_serv_parcheggio').checked = servizi.parcheggio == 1;
        document.getElementById('mod_serv_noleggio').checked = servizi.noleggio_attrezzatura == 1;
        document.getElementById('mod_serv_bar').checked = servizi.bar_ristoro == 1;
        document.getElementById('mod_serv_distributori').checked = servizi.distributori == 1;
        
        // Aggiorna subtitle
        document.getElementById('modificaCampoSubtitle').textContent = campo.nome;
        
        // Chiudi il modal dettaglio
        const modalDettaglio = bootstrap.Modal.getInstance(document.getElementById('modalDettaglioCampo'));
        if (modalDettaglio) {
            modalDettaglio.hide();
        }
        
        // Attendi che il modal dettaglio sia chiuso e rimuovi backdrop residui
        setTimeout(() => {
            // Rimuovi eventuali backdrop residui
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            document.body.style.removeProperty('overflow');
            
            // Apri il modal modifica
            const modalModifica = new bootstrap.Modal(document.getElementById('modalModificaCampo'));
            modalModifica.show();
        }, 350);
    });
    
    // Salva modifiche
    document.getElementById('btnSalvaModifiche').addEventListener('click', function() {
        const form = document.getElementById('formModificaCampo');
        const btn = this;
        
        // Validazione
        const nome = form.querySelector('[name="nome"]').value.trim();
        const sportId = form.querySelector('[name="sport_id"]').value;
        const superficie = form.querySelector('[name="tipo_superficie"]').value;
        const capienza = form.querySelector('[name="capienza_max"]').value;
        const location = form.querySelector('[name="location"]').value.trim();
        
        let isValid = true;
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        if (!nome) { form.querySelector('[name="nome"]').classList.add('is-invalid'); isValid = false; }
        if (!sportId) { form.querySelector('[name="sport_id"]').classList.add('is-invalid'); isValid = false; }
        if (!superficie) { form.querySelector('[name="tipo_superficie"]').classList.add('is-invalid'); isValid = false; }
        if (!capienza) { form.querySelector('[name="capienza_max"]').classList.add('is-invalid'); isValid = false; }
        if (!location) { form.querySelector('[name="location"]').classList.add('is-invalid'); isValid = false; }
        
        if (!isValid) {
            showToast('Compila tutti i campi obbligatori', 'error');
            return;
        }
        
        // Loading
        const originalContent = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Salvataggio...';
        
        const formData = new FormData(form);
        formData.append('action', 'update');
        formData.append('ajax', '1');
        
        fetch('gestione-campi.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Campo modificato con successo', 'success');
                bootstrap.Modal.getInstance(document.getElementById('modalModificaCampo')).hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Errore nella modifica', 'error');
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Errore di connessione', 'error');
            btn.disabled = false;
            btn.innerHTML = originalContent;
        });
    });
    
    // ============================================================================
    // FIX MODAL - Sposta i modal nel body per evitare problemi di z-index
    // ============================================================================
    const modalsToMove = ['modalNuovoCampo', 'modalDettaglioCampo', 'modalBloccoManutenzione', 'modalModificaCampo'];
    modalsToMove.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });
    
});
</script>