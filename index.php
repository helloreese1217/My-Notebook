<?php 
include 'db.php'; 
$active_nb_id = isset($_GET['notebook_id']) ? intval($_GET['notebook_id']) : null;
$active_pg_id = isset($_GET['page_id']) ? intval($_GET['page_id']) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Notebook</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="app-header">
        <button class="nav-toggle" id="toggle-sidebar" title="Toggle Navigation Panels">
            <span class="hamburger-bar"></span>
            <span class="hamburger-bar"></span>
            <span class="hamburger-bar"></span>
        </button>
        <div class="app-brand">My Notebook</div>
    </header>

    <div class="workspace-container">
        <div class="column" id="notebooks-col">
            <div class="column-header">
                <h3>Notebooks</h3>
                <button class="btn-add circular" id="btn-new-notebook" title="Create New Notebook">+</button>
            </div>
            <div class="column-content">
                <?php
                $notebooks = $conn->query("SELECT * FROM notebooks ORDER BY id DESC")->fetchAll();
                if (count($notebooks) > 0) {
                    foreach ($notebooks as $notebook) {
                        $active_nb_class = ($active_nb_id === intval($notebook['id'])) ? ' active-notebook' : '';
                        echo '<div class="notebook-item' . $active_nb_class . '" data-id="' . $notebook['id'] . '">';
                        echo '<span>📁 ' . htmlspecialchars($notebook['name']) . '</span>';
                        echo '<button class="btn-delete-trigger" data-type="notebook" data-id="' . $notebook['id'] . '">×</button>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="empty-text">No notebooks created yet.</p>';
                }
                ?>
            </div>
        </div>

    <div class="column" id="pages-col">
            <div class="column-header">
                <h3>Pages</h3>
                <?php if ($active_nb_id): ?>
                    <a href="add_page.php?notebook_id=<?php echo $active_nb_id; ?>" class="btn-add circular" id="btn-new-page" title="Create New Page">+</a>
                <?php endif; ?>
            </div>
            <div class="column-content">
                <?php
                if ($active_nb_id) {
                    $stmt = $conn->prepare("SELECT * FROM pages WHERE notebook_id = :nb_id ORDER BY id DESC");
                    $stmt->execute([':nb_id' => $active_nb_id]);
                    $pages = $stmt->fetchAll();

                    if (count($pages) > 0) {
                        foreach ($pages as $page) {
                            $active_class = ($active_pg_id === intval($page['id'])) ? ' active-page' : '';
                            echo '<div class="page-row-container' . $active_class . '" data-id="' . $page['id'] . '">';
                            echo '<a href="index.php?notebook_id=' . $active_nb_id . '&page_id=' . $page['id'] . '" class="page-link">';
                            echo '<span>📄 ' . htmlspecialchars($page['title'] ?? 'Untitled Page') . '</span>';
                            echo '</a>';
                            echo '<button class="btn-delete-trigger" data-type="page" data-id="' . $page['id'] . '" data-nb-id="' . $active_nb_id . '">×</button>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p class="empty-text">No pages here yet.</p>';
                    }
                } else {
                    echo '<p class="empty-text">Select a notebook to view pages.</p>';
                }
                ?>
            </div>
        </div>

    <div class="column" id="editor-col">
            <?php
            $selected_title = "";
            $selected_content = "";
            $is_page_active = false;

            if ($active_pg_id) {
                $stmt = $conn->prepare("SELECT * FROM pages WHERE id = :id LIMIT 1");
                $stmt->execute([':id' => $active_pg_id]);
                $active_page = $stmt->fetch();

                if ($active_page) {
                    $selected_title = $active_page['title'];
                    $selected_content = $active_page['content'];
                    $is_page_active = true;
                }
            }
            ?>

            <div class="column-header">
                <h3>Active Note Editor</h3>
                <span id="save-status" class="save-status-indicator">All changes saved</span>
            </div>

            <?php if ($is_page_active): ?>
                <div class="column-content editor-form">
                    <input type="hidden" id="editor-page-id" value="<?php echo $active_pg_id; ?>">
                    <input type="hidden" id="editor-notebook-id" value="<?php echo $active_nb_id; ?>">
                    
                    <input type="text" id="editor-title-field" class="editor-title" placeholder="Page Title..." value="<?php echo htmlspecialchars($selected_title ?? ''); ?>" required>
                    <textarea id="editor-content-field" class="editor-textarea" placeholder="Start typing your notes here..."><?php echo htmlspecialchars($selected_content ?? ''); ?></textarea>
                </div>
            <?php else: ?>
                <div class="column-content fallback-container">
                    <p class="empty-text">Select a page from the list or click '+' to start editing.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal-overlay" id="app-modal">
        <div class="modal-box" id="modal-box-content">
            </div>
    </div>

    <script src="script.js"></script>
</body>
</html>