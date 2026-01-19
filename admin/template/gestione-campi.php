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

// Colori sport
$sportColors = [
    'Calcetto' => '#10B981',
    'Basket' => '#F59E0B',
    'Tennis' => '#3B82F6',
    'Pallavolo' => '#8B5CF6',
    'Padel' => '#06B6D4',
    'Calcio' => '#10B981',
    'Badminton' => '#EC4899'
];

// Icone sport
$sportIcons = [
    'Calcetto' => '⚽',
    'Basket' => '🏀',
    'Tennis' => '🎾',
    'Pallavolo' => '🏐',
    'Padel' => '🏸',
    'Calcio' => '⚽',
    'Badminton' => '🏸'
];

$stats = $templateParams["stats"];
$campi = $templateParams["campi"];
$sports = $templateParams["sports"];
$filtri = $templateParams["filtri"];
$prenotazioniOggi = $templateParams["prenotazioni_oggi"] ?? [];
$recensioniRecenti = $templateParams["recensioni_recenti"] ?? [];
?>

<!-- Header Gestione Campi -->
<div class="gestione-header d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
    <div>
        <h1 class="page-title mb-1">
            <span class="me-2">🏟️</span>Gestione Campi
        </h1>
        <p class="page-subtitle mb-0">Gestisci tutti i campi sportivi del campus</p>
    </div>
    
    <div class="d-flex align-items-center gap-3">
        <!-- Search -->
        <div class="dashboard-search" id="searchContainer">
            <span class="search-icon">🔍</span>
            <input type="text" class="form-control" id="searchCampi" placeholder="Cerca campi..." value="<?php echo htmlspecialchars($filtri['search']); ?>">
        </div>
        
        <!-- Add New Field Button -->
        <button class="btn-primary-gradient d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNuovoCampo">
            <span>+</span> Nuovo Campo
        </button>
    </div>
</div>

<!-- ============================================================================
     QUICK STATS - KPI Cards
     ============================================================================ -->
<div class="row g-3 mb-4">
    <!-- Campi Totali -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="blue">
            <div class="kpi-header">
                <span class="kpi-icon">🏟️</span>
            </div>
            <div class="kpi-value"><?php echo $stats['totale'] ?? 0; ?></div>
            <div class="kpi-label">Campi Totali</div>
            <div class="kpi-progress">
                <div class="kpi-progress-bar bg-blue" style="width: 100%"></div>
            </div>
        </div>
    </div>
    
    <!-- Disponibili -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="green">
            <div class="kpi-header">
                <span class="kpi-icon">✅</span>
            </div>
            <div class="kpi-value"><?php echo $stats['disponibili'] ?? 0; ?></div>
            <div class="kpi-label">Disponibili</div>
            <div class="kpi-progress">
                <?php $percDisp = ($stats['totale'] > 0) ? round(($stats['disponibili'] / $stats['totale']) * 100) : 0; ?>
                <div class="kpi-progress-bar bg-green" style="width: <?php echo $percDisp; ?>%"></div>
            </div>
        </div>
    </div>
    
    <!-- Manutenzione -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="orange">
            <div class="kpi-header">
                <span class="kpi-icon">🔧</span>
            </div>
            <div class="kpi-value"><?php echo $stats['manutenzione'] ?? 0; ?></div>
            <div class="kpi-label">Manutenzione</div>
            <div class="kpi-progress">
                <?php $percMan = ($stats['totale'] > 0) ? round(($stats['manutenzione'] / $stats['totale']) * 100) : 0; ?>
                <div class="kpi-progress-bar bg-orange" style="width: <?php echo max($percMan, ($stats['manutenzione'] > 0 ? 10 : 0)); ?>%"></div>
            </div>
        </div>
    </div>
    
    <!-- Chiusi -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="red">
            <div class="kpi-header">
                <span class="kpi-icon">🚫</span>
            </div>
            <div class="kpi-value"><?php echo $stats['chiusi'] ?? 0; ?></div>
            <div class="kpi-label">Chiusi</div>
            <div class="kpi-progress">
                <?php $percChiusi = ($stats['totale'] > 0) ? round(($stats['chiusi'] / $stats['totale']) * 100) : 0; ?>
                <div class="kpi-progress-bar bg-red" style="width: <?php echo max($percChiusi, ($stats['chiusi'] > 0 ? 10 : 0)); ?>%"></div>
            </div>
        </div>
    </div>
    
    <!-- Prenotazioni Oggi -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="purple">
            <div class="kpi-header">
                <span class="kpi-icon">📅</span>
            </div>
            <div class="kpi-value"><?php echo $stats['prenotazioni_oggi'] ?? 0; ?></div>
            <div class="kpi-label">Prenotazioni Oggi</div>
            <div class="kpi-progress">
                <div class="kpi-progress-bar bg-purple" style="width: <?php echo min(100, ($stats['prenotazioni_oggi'] ?? 0) * 5); ?>%"></div>
            </div>
        </div>
    </div>
    
    <!-- Utilizzo Medio -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card" data-color="cyan">
            <div class="kpi-header">
                <span class="kpi-icon">📈</span>
            </div>
            <div class="kpi-value"><?php echo $stats['utilizzo_medio'] ?? 0; ?>%</div>
            <div class="kpi-label">Utilizzo Medio</div>
            <div class="kpi-progress">
                <div class="kpi-progress-bar bg-cyan" style="width: <?php echo $stats['utilizzo_medio'] ?? 0; ?>%"></div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     PRENOTAZIONI OGGI
     ============================================================================ -->
<?php if (!empty($prenotazioniOggi)): ?>
<div class="dashboard-card mb-4">
    <div class="card-header-custom">
        <h3><span class="card-icon">📅</span> Prenotazioni Oggi</h3>
        <div class="d-flex gap-2">
            <?php
            $completate = count(array_filter($prenotazioniOggi, fn($p) => $p['stato'] == 'completata'));
            $confermate = count(array_filter($prenotazioniOggi, fn($p) => $p['stato'] == 'confermata'));
            ?>
            <span class="badge-status badge-success">✓ <?php echo $completate; ?> completate</span>
            <span class="badge-status badge-info">▶ <?php echo $confermate; ?> confermate</span>
        </div>
    </div>
    <div class="card-body-custom">
        <div class="booking-today-scroll">
            <?php foreach ($prenotazioniOggi as $pren): 
                $statusClass = $pren['stato'] == 'completata' ? 'booking-completed' : 'booking-confirmed';
                $statusLabel = $pren['stato'] == 'completata' ? 'Completata' : 'Confermata';
            ?>
            <div class="booking-today-card <?php echo $statusClass; ?>">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="booking-time"><?php echo substr($pren['ora_inizio'], 0, 5); ?> - <?php echo substr($pren['ora_fine'], 0, 5); ?></span>
                    <span class="booking-status-badge"><?php echo $statusLabel; ?></span>
                </div>
                <div class="booking-user fw-semibold mb-1"><?php echo htmlspecialchars($pren['utente_nome']); ?></div>
                <div class="booking-field text-muted small">
                    <span class="me-1"><?php echo $sportIcons[$pren['sport_nome']] ?? '🏟️'; ?></span>
                    <?php echo htmlspecialchars($pren['campo_nome']); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ============================================================================
     FILTRI
     ============================================================================ -->
<div class="dashboard-card mb-4">
    <div class="card-body-custom py-3">
        <form id="formFiltri" method="GET" class="d-flex align-items-center gap-4 flex-wrap">
            <!-- Sport Filter -->
            <div class="filter-group d-flex align-items-center gap-2">
                <span class="filter-label">Sport:</span>
                <div class="filter-chips">
                    <button type="button" class="filter-chip <?php echo empty($filtri['sport']) ? 'active' : ''; ?>" data-filter="sport" data-value="">
                        Tutti
                    </button>
                    <?php foreach ($sports as $sport): ?>
                    <button type="button" class="filter-chip <?php echo $filtri['sport'] == $sport['nome'] ? 'active' : ''; ?>" data-filter="sport" data-value="<?php echo htmlspecialchars($sport['nome']); ?>">
                        <?php echo $sport['icona'] ?? '🏟️'; ?> <?php echo htmlspecialchars($sport['nome']); ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="filter-divider"></div>
            
            <!-- Stato Filter -->
            <div class="filter-group d-flex align-items-center gap-2">
                <span class="filter-label">Stato:</span>
                <div class="filter-chips">
                    <button type="button" class="filter-chip <?php echo empty($filtri['stato']) ? 'active' : ''; ?>" data-filter="stato" data-value="">
                        Tutti
                    </button>
                    <button type="button" class="filter-chip <?php echo $filtri['stato'] == 'disponibile' ? 'active' : ''; ?>" data-filter="stato" data-value="disponibile">
                        🟢 Disponibile
                    </button>
                    <button type="button" class="filter-chip <?php echo $filtri['stato'] == 'manutenzione' ? 'active' : ''; ?>" data-filter="stato" data-value="manutenzione">
                        🟡 Manutenzione
                    </button>
                    <button type="button" class="filter-chip <?php echo $filtri['stato'] == 'chiuso' ? 'active' : ''; ?>" data-filter="stato" data-value="chiuso">
                        🔴 Chiuso
                    </button>
                </div>
            </div>
            
            <div class="filter-divider"></div>
            
            <!-- Tipo Filter -->
            <div class="filter-group d-flex align-items-center gap-2">
                <span class="filter-label">Tipo:</span>
                <div class="filter-chips">
                    <button type="button" class="filter-chip <?php echo empty($filtri['tipo']) ? 'active' : ''; ?>" data-filter="tipo" data-value="">
                        Tutti
                    </button>
                    <button type="button" class="filter-chip <?php echo $filtri['tipo'] == 'indoor' ? 'active' : ''; ?>" data-filter="tipo" data-value="indoor">
                        🏠 Indoor
                    </button>
                    <button type="button" class="filter-chip <?php echo $filtri['tipo'] == 'outdoor' ? 'active' : ''; ?>" data-filter="tipo" data-value="outdoor">
                        🌳 Outdoor
                    </button>
                </div>
            </div>
            
            <div class="ms-auto">
                <select name="ordina" id="sortSelect" class="form-select-custom">
                    <option value="nome" <?php echo $filtri['ordina'] == 'nome' ? 'selected' : ''; ?>>Nome</option>
                    <option value="rating" <?php echo $filtri['ordina'] == 'rating' ? 'selected' : ''; ?>>Rating</option>
                    <option value="utilizzo" <?php echo $filtri['ordina'] == 'utilizzo' ? 'selected' : ''; ?>>Utilizzo</option>
                    <option value="prenotazioni" <?php echo $filtri['ordina'] == 'prenotazioni' ? 'selected' : ''; ?>>Prenotazioni</option>
                </select>
            </div>
            
            <!-- Hidden inputs for filters -->
            <input type="hidden" name="sport" id="filterSport" value="<?php echo htmlspecialchars($filtri['sport']); ?>">
            <input type="hidden" name="stato" id="filterStato" value="<?php echo htmlspecialchars($filtri['stato']); ?>">
            <input type="hidden" name="tipo" id="filterTipo" value="<?php echo htmlspecialchars($filtri['tipo']); ?>">
            <input type="hidden" name="search" id="filterSearch" value="<?php echo htmlspecialchars($filtri['search']); ?>">
        </form>
    </div>
</div>

<!-- ============================================================================
     GRIGLIA CAMPI
     ============================================================================ -->
<div class="section-header mb-3">
    <h2 class="section-title">
        <span class="me-2">🏟️</span>Tutti i Campi
        <span class="section-count">(<?php echo count($campi); ?> campi)</span>
    </h2>
</div>

<div class="row g-4 mb-4" id="campiGrid">
    <?php if (empty($campi)): ?>
    <div class="col-12">
        <div class="empty-state">
            <span class="empty-icon">🏟️</span>
            <h3>Nessun campo trovato</h3>
            <p>Prova a modificare i filtri o aggiungi un nuovo campo.</p>
            <button class="btn-primary-gradient" data-bs-toggle="modal" data-bs-target="#modalNuovoCampo">
                + Aggiungi Campo
            </button>
        </div>
    </div>
    <?php else: ?>
    <?php foreach ($campi as $campo): 
        $sportColor = $sportColors[$campo['sport_nome']] ?? '#3B82F6';
        $sportIcon = $sportIcons[$campo['sport_nome']] ?? '🏟️';
        
        // Status config
        $statusConfig = [
            'disponibile' => ['color' => '#10B981', 'label' => 'Disponibile'],
            'manutenzione' => ['color' => '#F59E0B', 'label' => 'Manutenzione'],
            'chiuso' => ['color' => '#EF4444', 'label' => 'Chiuso']
        ];
        $status = $statusConfig[$campo['stato']] ?? $statusConfig['disponibile'];
        
        // Calcolo utilizzo (simulato basato su prenotazioni)
        $utilizzo = min(100, ($campo['prenotazioni_settimana'] ?? 0) * 3);
    ?>
    <div class="col-xl-4 col-lg-6">
        <div class="field-card" data-campo-id="<?php echo $campo['campo_id']; ?>" style="--sport-color: <?php echo $sportColor; ?>">
            <!-- Header -->
            <div class="field-card-header">
                <!-- Status Badge -->
                <div class="field-status-wrapper">
                    <div class="field-status" style="--status-color: <?php echo $status['color']; ?>">
                        <span class="status-dot"></span>
                        <span class="status-text"><?php echo $status['label']; ?></span>
                    </div>
                </div>
                
                <!-- Icon -->
                <span class="field-icon"><?php echo $sportIcon; ?></span>
                
                <!-- Name -->
                <h3 class="field-name"><?php echo htmlspecialchars($campo['nome']); ?></h3>
                
                <!-- Type -->
                <div class="field-type">
                    <?php echo htmlspecialchars($campo['sport_nome']); ?> • <?php echo $campo['tipo_campo'] == 'indoor' ? 'Indoor' : 'Outdoor'; ?>
                </div>
            </div>
            
            <!-- Body -->
            <div class="field-card-body">
                <!-- Stats -->
                <div class="field-stats">
                    <div class="field-stat">
                        <span class="stat-value"><?php echo $campo['prenotazioni_oggi'] ?? 0; ?></span>
                        <span class="stat-label">oggi</span>
                    </div>
                    <div class="field-stat">
                        <span class="stat-value"><?php echo $campo['prenotazioni_settimana'] ?? 0; ?></span>
                        <span class="stat-label">settimana</span>
                    </div>
                    <div class="field-stat">
                        <span class="stat-value" style="color: <?php echo $sportColor; ?>"><?php echo $utilizzo; ?>%</span>
                        <span class="stat-label">utilizzo</span>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="field-progress">
                    <div class="field-progress-bar" style="width: <?php echo $utilizzo; ?>%; background: <?php echo $sportColor; ?>"></div>
                </div>
                
                <!-- Time -->
                <div class="field-time">
                    🕐 <?php echo substr($campo['orario_apertura'], 0, 5); ?> - <?php echo substr($campo['orario_chiusura'], 0, 5); ?>
                </div>
                
                <!-- Rating -->
                <div class="field-rating">
                    <?php 
                    $rating = floatval($campo['rating_medio'] ?? 0);
                    for ($i = 1; $i <= 5; $i++): 
                        $starClass = $i <= $rating ? 'star-full' : ($i - 0.5 <= $rating ? 'star-half' : 'star-empty');
                    ?>
                    <span class="star <?php echo $starClass; ?>">⭐</span>
                    <?php endfor; ?>
                    <span class="rating-value"><?php echo number_format($rating, 1); ?></span>
                    <span class="rating-count">(<?php echo $campo['num_recensioni'] ?? 0; ?>)</span>
                </div>
                
                <!-- Hover Text -->
                <div class="field-hover-text">Clicca per gestire →</div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- ============================================================================
     RECENSIONI RECENTI
     ============================================================================ -->
<?php if (!empty($recensioniRecenti)): 
    $reviewsToManage = count(array_filter($recensioniRecenti, fn($r) => $r['rating_generale'] < 4));
?>
<div class="dashboard-card">
    <div class="card-header-custom">
        <div class="d-flex align-items-center gap-3">
            <h3><span class="card-icon">⭐</span> Recensioni Recenti</h3>
            <?php if ($reviewsToManage > 0): ?>
            <div class="reviews-alert">
                <span class="alert-dot"></span>
                <span><?php echo $reviewsToManage; ?> da gestire</span>
            </div>
            <?php endif; ?>
        </div>
        <button class="card-link btn-link" data-bs-toggle="modal" data-bs-target="#modalRecensioni">
            Gestisci tutte le recensioni →
        </button>
    </div>
    <div class="card-body-custom">
        <div class="row g-3">
            <?php foreach ($recensioniRecenti as $review): 
                $ratingColor = $review['rating_generale'] >= 4 ? '#10B981' : ($review['rating_generale'] >= 3 ? '#F59E0B' : '#EF4444');
            ?>
            <div class="col-lg-4">
                <div class="review-card" style="--rating-color: <?php echo $ratingColor; ?>">
                    <div class="review-header">
                        <div class="review-user">
                            <div class="user-avatar-sm"><?php echo $review['utente_iniziale'] ?? 'U'; ?></div>
                            <div class="user-info-sm">
                                <div class="user-name-sm"><?php echo htmlspecialchars($review['utente_nome']); ?></div>
                                <div class="review-date"><?php echo tempoRelativo($review['created_at']); ?></div>
                            </div>
                        </div>
                        <div class="review-rating">
                            <span class="star">⭐</span>
                            <span class="rating-num"><?php echo $review['rating_generale']; ?></span>
                        </div>
                    </div>
                    <p class="review-text">"<?php echo htmlspecialchars(mb_substr($review['commento'] ?? '', 0, 100)); ?><?php echo strlen($review['commento'] ?? '') > 100 ? '...' : ''; ?>"</p>
                    <div class="review-footer">
                        <div class="review-field">
                            <span class="field-icon-sm"><?php echo $sportIcons[$review['sport_nome']] ?? '🏟️'; ?></span>
                            <?php echo htmlspecialchars($review['campo_nome']); ?>
                        </div>
                        <button class="btn-reply" data-recensione-id="<?php echo $review['recensione_id']; ?>">
                            Rispondi →
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

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
                                <?php foreach ($sports as $sport): ?>
                                <option value="<?php echo $sport['sport_id']; ?>"><?php echo $sport['icona'] ?? '🏟️'; ?> <?php echo htmlspecialchars($sport['nome']); ?></option>
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
                                            <span class="star">⭐</span>
                                            <span class="star">⭐</span>
                                            <span class="star">⭐</span>
                                            <span class="star">⭐</span>
                                            <span class="star">⭐</span>
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
                            <button class="btn-primary-gradient btn-sm" id="btnProgrammaManutenzione">
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
                    <button type="button" class="btn-primary-gradient" id="btnModificaCampo">
                        ✏️ Modifica Campo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: GESTIONE RECENSIONI
     ============================================================================ -->
<div class="modal fade" id="modalRecensioni" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-dark">
            <div class="modal-header modal-header-gradient" style="--gradient-start: #F59E0B; --gradient-end: #D97706;">
                <div class="d-flex align-items-center gap-3">
                    <span class="modal-icon">⭐</span>
                    <div>
                        <h5 class="modal-title mb-0">Gestione Recensioni</h5>
                        <p class="modal-subtitle mb-0" id="recensioniSubtitle">Caricamento...</p>
                    </div>
                </div>
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close">✕</button>
            </div>
            
            <!-- Filters -->
            <div class="modal-filters">
                <div class="d-flex gap-2 flex-wrap">
                    <button class="filter-chip active" data-filter-recensioni="all">Tutte</button>
                    <button class="filter-chip" data-filter-recensioni="positive">⭐ Positive</button>
                    <button class="filter-chip" data-filter-recensioni="negative">⚠️ Negative</button>
                    <button class="filter-chip" data-filter-recensioni="pending">📩 Da rispondere</button>
                </div>
                <select class="form-select-custom form-select-sm" id="sortRecensioni">
                    <option value="date">Più recenti</option>
                    <option value="rating_high">Rating più alto</option>
                    <option value="rating_low">Rating più basso</option>
                </select>
            </div>
            
            <div class="modal-body">
                <div id="recensioniListContainer">
                    <!-- Recensioni caricate via AJAX -->
                    <div class="loading-state">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Caricamento...</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer modal-footer-dark justify-content-between">
                <span class="text-muted small" id="recensioniCount">Caricamento...</span>
                <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     MODAL: RISPOSTA RECENSIONE
     ============================================================================ -->
<div class="modal fade" id="modalRispostaRecensione" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <div class="modal-header modal-header-gradient" style="--gradient-start: #3B82F6; --gradient-end: #2563EB;">
                <div class="d-flex align-items-center gap-3">
                    <span class="modal-icon">💬</span>
                    <div>
                        <h5 class="modal-title mb-0">Rispondi alla Recensione</h5>
                        <p class="modal-subtitle mb-0">Scrivi una risposta pubblica</p>
                    </div>
                </div>
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close">✕</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rispostaRecensioneId">
                <div class="review-preview mb-3" id="reviewPreview">
                    <!-- Preview recensione caricata via JS -->
                </div>
                <label class="form-label-custom">La tua risposta</label>
                <textarea class="form-textarea-custom" id="rispostaTesto" rows="4" placeholder="Scrivi la tua risposta..."></textarea>
            </div>
            <div class="modal-footer modal-footer-dark">
                <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn-primary-gradient" id="btnInviaRisposta">
                    📤 Invia Risposta
                </button>
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
            <div class="modal-header modal-header-gradient" style="--gradient-start: #F59E0B; --gradient-end: #D97706;">
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
    const sportIcons = <?php echo json_encode($sportIcons); ?>;
    
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
        const sportIcon = sportIcons[campo.sport_nome] || '🏟️';
        
        // Header
        document.getElementById('modalDetailHeader').style.setProperty('--sport-color', sportColor);
        document.getElementById('detailIcon').textContent = sportIcon;
        document.getElementById('detailNome').textContent = campo.nome;
        document.getElementById('detailSport').innerHTML = `${sportIcon} ${campo.sport_nome}`;
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
            star.className = 'star ' + (i <= rating ? 'star-full' : 'star-empty');
            star.textContent = '⭐';
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
                                ${Array(5).fill().map((_, i) => `<span class="star ${i < rev.rating_generale ? 'star-full' : 'star-empty'}">⭐</span>`).join('')}
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
    
    // ============================================================================
    // GESTIONE RECENSIONI MODAL
    // ============================================================================
    const modalRecensioni = document.getElementById('modalRecensioni');
    modalRecensioni.addEventListener('show.bs.modal', function() {
        loadAllRecensioni();
    });
    
    function loadAllRecensioni(filtro = 'all') {
        let url = 'gestione-campi.php?action=get_recensioni&limit=50';
        
        if (filtro === 'positive') {
            url += '&rating_min=4';
        } else if (filtro === 'negative') {
            url += '&rating_max=3';
        } else if (filtro === 'pending') {
            url += '&senza_risposta=1';
        }
        
        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderRecensioniList(data.recensioni);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    function renderRecensioniList(recensioni) {
        const container = document.getElementById('recensioniListContainer');
        const subtitle = document.getElementById('recensioniSubtitle');
        const count = document.getElementById('recensioniCount');
        
        const pending = recensioni.filter(r => r.num_risposte == 0).length;
        subtitle.textContent = `${recensioni.length} recensioni totali • ${pending} in attesa di risposta`;
        count.textContent = `Mostrando ${recensioni.length} recensioni`;
        
        if (recensioni.length === 0) {
            container.innerHTML = '<div class="empty-tab"><p>Nessuna recensione trovata</p></div>';
            return;
        }
        
        container.innerHTML = recensioni.map(rev => {
            const ratingColor = rev.rating_generale >= 4 ? '#10B981' : (rev.rating_generale >= 3 ? '#F59E0B' : '#EF4444');
            return `
                <div class="review-item-full" style="--rating-color: ${ratingColor}">
                    <div class="review-item-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="user-avatar-md">${rev.utente_iniziale || 'U'}</div>
                            <div>
                                <div class="fw-semibold">${rev.utente_nome}</div>
                                <div class="text-muted small">
                                    ${formatTimeAgo(rev.created_at)} • 
                                    ${sportIcons[rev.sport_nome] || '🏟️'} ${rev.campo_nome}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            ${rev.num_risposte > 0 ? '<span class="badge-responded">✓ Risposto</span>' : ''}
                            <div class="review-rating-badge" style="background: ${ratingColor}15; color: ${ratingColor}">
                                ${Array(5).fill().map((_, i) => `<span style="opacity: ${i < rev.rating_generale ? 1 : 0.3}">⭐</span>`).join('')}
                            </div>
                        </div>
                    </div>
                    <p class="review-item-text">"${rev.commento || ''}"</p>
                    <div class="review-item-actions">
                        ${rev.num_risposte == 0 ? `<button class="btn-primary-gradient btn-sm" onclick="openReplyModal(${rev.recensione_id}, '${rev.utente_nome}', '${(rev.commento || '').replace(/'/g, "\\'")}', ${rev.rating_generale})">💬 Rispondi</button>` : ''}
                        <button class="btn-secondary-custom btn-sm">Visualizza</button>
                        <button class="btn-danger-custom btn-sm" onclick="deleteRecensione(${rev.recensione_id})">Elimina</button>
                    </div>
                </div>
            `;
        }).join('');
    }
    
    // Filter buttons in recensioni modal
    document.querySelectorAll('[data-filter-recensioni]').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('[data-filter-recensioni]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            loadAllRecensioni(this.dataset.filterRecensioni);
        });
    });
    
    // Reply modal
    window.openReplyModal = function(recensioneId, userName, commento, rating) {
        document.getElementById('rispostaRecensioneId').value = recensioneId;
        document.getElementById('reviewPreview').innerHTML = `
            <div class="review-preview-content">
                <div class="fw-semibold mb-1">${userName}</div>
                <div class="stars-small mb-2">
                    ${Array(5).fill().map((_, i) => `<span class="star ${i < rating ? 'star-full' : 'star-empty'}">⭐</span>`).join('')}
                </div>
                <p class="text-muted mb-0">"${commento}"</p>
            </div>
        `;
        document.getElementById('rispostaTesto').value = '';
        new bootstrap.Modal(document.getElementById('modalRispostaRecensione')).show();
    };
    
    document.getElementById('btnInviaRisposta').addEventListener('click', function() {
        const recensioneId = document.getElementById('rispostaRecensioneId').value;
        const testo = document.getElementById('rispostaTesto').value;
        
        if (!testo.trim()) {
            showToast('Scrivi una risposta', 'warning');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'reply_recensione');
        formData.append('recensione_id', recensioneId);
        formData.append('testo', testo);
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
                bootstrap.Modal.getInstance(document.getElementById('modalRispostaRecensione')).hide();
                loadAllRecensioni();
            } else {
                showToast(data.message || 'Errore', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Errore di connessione', 'error');
        });
    });
    
    // Delete recensione
    window.deleteRecensione = function(recensioneId) {
        if (!confirm('Sei sicuro di voler eliminare questa recensione?')) return;
        
        const formData = new FormData();
        formData.append('action', 'delete_recensione');
        formData.append('recensione_id', recensioneId);
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
                loadAllRecensioni();
            } else {
                showToast(data.message || 'Errore', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Errore di connessione', 'error');
        });
    };
    
    // Reply button in review cards
    document.querySelectorAll('.btn-reply').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const recensioneId = this.dataset.recensioneId;
            // Open reply modal with basic info
            openReplyModal(recensioneId, 'Utente', '', 0);
        });
    });
    
});
</script>