<?php
session_start();
$user = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Notebook</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div id="auth-pane" class="card">
    <h1>Artist Notebook</h1>
    <div class="tabs">
        <button class="tab active" data-tab="login">Login</button>
        <button class="tab" data-tab="register">Register</button>
    </div>
    <div class="tab-content" id="login-tab">
        <input type="email" id="login-email" placeholder="Email">
        <input type="password" id="login-password" placeholder="Password">
        <button id="login-btn" class="primary">Login</button>
        <div class="error" id="login-error"></div>
    </div>
    <div class="tab-content hidden" id="register-tab">
        <input type="text" id="reg-name" placeholder="Full name">
        <input type="email" id="reg-email" placeholder="Email">
        <input type="password" id="reg-password" placeholder="Password (min 6 chars)">
        <button id="register-btn" class="primary">Create account</button>
        <div class="error" id="register-error"></div>
    </div>
</div>

<div id="app" class="hidden">
    <header class="app-header">
        <div class="brand">
            <h1>Artist Notebook</h1>
            <small class="subtitle">Private Artist Cataloguing</small>
        </div>
        <div class="user-info">
            <span id="user-name"></span>
            <div class="progress">
                <div class="progress-label"><span id="user-level"></span> · <span id="user-points"></span> pts</div>
                <div class="progress-bar"><div id="progress-fill"></div></div>
            </div>
            <div id="badges" class="badges"></div>
        </div>
        <div class="actions">
            <button id="export-btn" class="pill secondary hidden">Download export</button>
            <button id="logout-btn" class="pill">Logout</button>
        </div>
    </header>
    <main class="layout">
        <aside class="sidebar">
            <div class="search-box">
                <input type="text" id="search" placeholder="Search artists...">
            </div>
            <button id="add-artist" class="primary pill full">+ Add Artist</button>
            <ul id="artist-list" class="artist-list"></ul>
        </aside>
        <section class="details">
            <div id="empty-state" class="empty">Select or create an artist to begin.</div>
            <div id="artist-detail" class="hidden">
                <div class="card">
                    <h2>Artist</h2>
                    <div class="grid">
                        <div>
                            <label>Name</label>
                            <input type="text" id="artist-name">
                        </div>
                        <div>
                            <label>Nationality</label>
                            <input type="text" id="artist-nationality">
                        </div>
                        <div>
                            <label>Birth Year</label>
                            <input type="number" id="artist-birth">
                        </div>
                        <div>
                            <label>Death Year</label>
                            <input type="number" id="artist-death">
                        </div>
                        <div>
                            <label>Tag 1</label>
                            <input type="text" id="artist-tag1">
                        </div>
                        <div>
                            <label>Tag 2</label>
                            <input type="text" id="artist-tag2">
                        </div>
                    </div>
                    <label>Notes</label>
                    <textarea id="artist-notes" rows="3"></textarea>
                    <div class="image-row">
                        <img id="artist-image-preview" class="preview" alt="artist">
                        <div>
                            <input type="file" id="artist-image" accept="image/*">
                            <button id="save-artist" class="primary pill">Save Artist</button>
                            <button id="delete-artist" class="pill danger">Delete Artist</button>
                        </div>
                    </div>
                    <div class="error" id="artist-error"></div>
                </div>
                <div class="card">
                    <div class="header-row">
                        <h2>Artworks</h2>
                        <button id="add-artwork" class="secondary pill">+ Add Artwork</button>
                    </div>
                    <div id="artwork-list" class="artwork-grid"></div>
                    <div id="artwork-form" class="hidden">
                        <h3 id="artwork-form-title">New Artwork</h3>
                        <div class="grid">
                            <div>
                                <label>Title</label>
                                <input type="text" id="artwork-title">
                            </div>
                            <div>
                                <label>Year</label>
                                <input type="text" id="artwork-year">
                            </div>
                            <div>
                                <label>Medium</label>
                                <input type="text" id="artwork-medium">
                            </div>
                        </div>
                        <label>Notes</label>
                        <textarea id="artwork-notes" rows="3"></textarea>
                        <div class="image-row">
                            <img id="artwork-image-preview" class="preview" alt="artwork">
                            <div>
                                <input type="file" id="artwork-image" accept="image/*">
                                <button id="save-artwork" class="primary pill">Save Artwork</button>
                                <button id="cancel-artwork" class="pill secondary">Cancel</button>
                            </div>
                        </div>
                        <div class="error" id="artwork-error"></div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
<script>
    const LOGGED_IN = <?php echo $user ? 'true' : 'false'; ?>;
</script>
<script src="script.js"></script>
</body>
</html>
