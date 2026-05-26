document.addEventListener('DOMContentLoaded', () => {

    const toggleSidebarBtn = document.getElementById('toggle-sidebar');
    const notebooksCol = document.getElementById('notebooks-col');
    const pagesCol = document.getElementById('pages-col');
    const appModal = document.getElementById('app-modal');
    const modalBoxContent = document.getElementById('modal-box-content');

    const editorTitle = document.getElementById('editor-title-field');
    const editorContent = document.getElementById('editor-content-field');
    const saveStatus = document.getElementById('save-status');

    let autoSaveTimeout = null;

    if (toggleSidebarBtn) {
        toggleSidebarBtn.addEventListener('click', () => {
            notebooksCol.classList.toggle('collapsed-panel');
            pagesCol.classList.toggle('collapsed-panel');
        });
    }

    const btnNewNotebook = document.getElementById('btn-new-notebook');
    if (btnNewNotebook) {
        btnNewNotebook.addEventListener('click', () => {
            modalBoxContent.innerHTML = `
                <h4>Create New Notebook</h4>
                <form action="add_notebook.php" method="POST">
                    <input type="text" name="notebook_name" placeholder="e.g., Lesson Notes" required autofocus>
                    <div class="modal-buttons">
                        <button type="button" class="btn-cancel" id="btn-close-modal">Cancel</button>
                        <button type="submit" class="btn-submit">Create</button>
                    </div>
                </form>
            `;
            openModal();
        });
    }

    document.body.addEventListener('click', (event) => {
        if (event.target.classList.contains('btn-delete-trigger')) {
            event.preventDefault();
            event.stopPropagation();

            const button = event.target;
            const deleteType = button.getAttribute('data-type');
            const targetId = button.getAttribute('data-id');
            const parentNbId = button.getAttribute('data-nb-id');

            if (localStorage.getItem('skipDeleteConfirmation') === 'true') {
                executeDeletion(deleteType, targetId, parentNbId);
            } else {
                renderDeleteConfirmationModal(deleteType, targetId, parentNbId);
            }
        }
    });

    function renderDeleteConfirmationModal(type, id, parentNbId) {
        const titleText = type === 'notebook' ? 'Delete Notebook?' : 'Delete Page?';
        const bodyText = type === 'notebook' 
            ? 'Are you sure? This will permanently delete this notebook along with all its nested page files.' 
            : 'Are you sure you want to permanently remove this page?';

        modalBoxContent.innerHTML = `
            <h4>${titleText}</h4>
            <p>${bodyText}</p>
            <div class="dont-ask-wrapper">
                <input type="checkbox" id="dont-ask-checkbox">
                <label for="dont-ask-checkbox">Don't ask me next time</label>
            </div>
            <div class="modal-buttons">
                <button type="button" class="btn-cancel" id="btn-close-modal">Cancel</button>
                <button type="button" class="btn-danger-confirm" id="btn-confirm-delete-real">Delete</button>
            </div>
        `;
        
        openModal();

        document.getElementById('btn-confirm-delete-real').addEventListener('click', () => {
            const rememberChoice = document.getElementById('dont-ask-checkbox').checked;
            if (rememberChoice) {
                localStorage.setItem('skipDeleteConfirmation', 'true');
            }
            closeModal();
            executeDeletion(type, id, parentNbId);
        });
    }

    function executeDeletion(type, id, parentNbId) {
        let route = '';
        if (type === 'notebook') {
            route = `delete_notebook.php?notebook_id=${id}`;
        } else {
            route = `delete_page.php?page_id=${id}&notebook_id=${parentNbId}`;
        }
        window.location.href = route;
    }

    function triggerBackgroundSave() {
        if (!editorTitle) return;

        if (saveStatus) saveStatus.textContent = "Saving changes...";
        
        const pageId = document.getElementById('editor-page-id').value;
        const notebookId = document.getElementById('editor-notebook-id').value;
        const titleVal = editorTitle.value;
        const contentVal = editorContent.value;

        const formData = new URLSearchParams();
        formData.append('page_id', pageId);
        formData.append('notebook_id', notebookId);
        formData.append('page_title', titleVal);
        formData.append('page_content', contentVal);

        fetch('save_page.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
        .then(response => {
            if (response.ok) {
                if (saveStatus) saveStatus.textContent = "All changes saved";
                
                const correspondingPageRow = document.querySelector(`.page-row-container[data-id="${pageId}"] .page-link span`);
                if (correspondingPageRow && titleVal.trim() !== "") {
                    correspondingPageRow.textContent = `📄 ${titleVal}`;
                }
            } else {
                if (saveStatus) saveStatus.textContent = "Connection saving error";
            }
        })
        .catch(() => {
            if (saveStatus) saveStatus.textContent = "Server connection lost";
        });
    }

    function queueAutoSave() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(triggerBackgroundSave, 750);
    }

    if (editorTitle && editorContent) {
        editorTitle.addEventListener('input', queueAutoSave);
        editorContent.addEventListener('input', queueAutoSave);

        editorTitle.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                clearTimeout(autoSaveTimeout);
                triggerBackgroundSave();
                editorContent.focus();
            }
        });
    }

    function openModal() { if (appModal) appModal.style.display = 'flex'; }
    function closeModal() { if (appModal) appModal.style.display = 'none'; }

    document.body.addEventListener('click', (e) => {
        if (e.target.id === 'btn-close-modal' || e.target === appModal) {
            closeModal();
        }
    });

    const notebooksContainer = document.querySelector('#notebooks-col .column-content');

    if (notebooksContainer) {
        notebooksContainer.addEventListener('click', (event) => {
            if (event.target.classList.contains('btn-delete-trigger')) return;

            if (event.target.classList.contains('notebook-rename-input')) return;

            const nameSpan = event.target.closest('.notebook-item span');
            if (nameSpan) {
                event.stopPropagation();

                const notebookItem = nameSpan.closest('.notebook-item');
                const notebookId = notebookItem.getAttribute('data-id');
                const currentName = nameSpan.textContent.replace('📁 ', '').trim();

                const input = document.createElement('input');
                input.type = 'text';
                input.value = currentName;
                input.className = 'notebook-rename-input';
                
                nameSpan.replaceWith(input);
                input.focus();

                let isSaved = false;

                const saveRename = () => {
                    if (isSaved) return; 
                    isSaved = true;

                    const updatedName = input.value.trim();
                    
                    if (updatedName === '' || updatedName === currentName) {
                        input.replaceWith(nameSpan);
                        return;
                    }

                    nameSpan.textContent = `📁 ${updatedName}`;
                    input.replaceWith(nameSpan);

                    fetch('rename_notebook.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: notebookId, name: updatedName })
                    });
                };

                input.addEventListener('blur', saveRename);
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        input.blur(); 
                    }
                    if (e.key === 'Escape') {
                        isSaved = true;
                        input.replaceWith(nameSpan);
                    }
                });
                
                return; 
            }

            const item = event.target.closest('.notebook-item');
            if (item) {
                window.location.href = `index.php?notebook_id=${item.getAttribute('data-id')}`;
            }
        });
    }
});