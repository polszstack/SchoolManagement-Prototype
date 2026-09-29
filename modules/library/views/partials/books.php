<!-- ====== BOOKS ====== -->
<div class="tab-content" id="tab-books">
    <div class="card mb-2">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-book"></i> Book Catalog Management</h3></div>
        <div class="grid-2">
            <!-- ADD / EDIT FORM -->
            <div>
                <h4 class="mb-1" id="bookFormTitle">Add New Book</h4>
                <input type="hidden" id="editBookId" value="">
                <div class="form-group"><label>Book Title *</label><input type="text" id="bookTitle" placeholder="Enter book title" autocomplete="off"></div>
                <div class="form-group"><label>Author *</label><input type="text" id="bookAuthor" placeholder="Author's full name" autocomplete="off"></div>
                <div class="grid-2">
                    <div class="form-group">
                        <label>Genre</label>
                        <select id="bookGenre">
                            <option value="Fiction">Fiction</option>
                            <option value="Non-Fiction">Non-Fiction</option>
                            <option value="Science Fiction">Science Fiction</option>
                            <option value="Fantasy">Fantasy</option>
                            <option value="Romance">Romance</option>
                            <option value="Mystery">Mystery</option>
                            <option value="Biography">Biography</option>
                            <option value="History">History</option>
                            <option value="Science">Science</option>
                            <option value="Technology">Technology</option>
                            <option value="Children">Children</option>
                            <option value="General">General</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Year</label>
                        <input type="number" id="bookYear" min="1800" max="<?= date('Y') ?>" value="<?= date('Y') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>ISBN <span class="text-muted small">(auto-fetches cover)</span></label>
                    <input type="text" id="bookISBN" placeholder="978-0-0000-0000-0" oninput="app.books.previewISBNCover()">
                </div>
                <div class="form-group"><label>Description</label><textarea id="bookDescription" placeholder="Brief description of the book..."></textarea></div>
                <div class="form-group">
                    <label>Book Cover Image</label>
                    <div class="image-upload-area" id="coverUploadArea"
                         onclick="document.getElementById('coverFileInput').click()"
                         ondragover="app.books.handleDragOver(event)"
                         ondrop="app.books.handleDrop(event)">
                        <img id="coverPreviewImg" src="" alt="" style="display:none;max-height:140px;border-radius:8px;margin-bottom:.75rem">
                        <i class="fas fa-cloud-upload-alt" style="font-size:2rem;color:var(--gray);display:block;margin-bottom:.5rem" id="uploadIcon"></i>
                        <p style="margin:0;color:var(--gray)">Click or drag to upload cover image</p>
                        <p class="text-muted small" style="margin-top:.25rem">JPG, PNG, WebP up to 5MB</p>
                    </div>
                    <input type="file" id="coverFileInput" accept="image/*" style="display:none" onchange="app.books.handleCoverUpload(event)">
                    <input type="hidden" id="bookCoverUrl" value="">
                </div>
                <div class="d-flex gap-1">
                    <button class="btn btn-success w-100" id="bookSubmitBtn" onclick="app.books.submit()"><i class="fas fa-plus-circle"></i> Add Book</button>
                    <button class="btn btn-outline" id="bookCancelBtn" style="display:none" onclick="app.books.cancelEdit()"><i class="fas fa-times"></i> Cancel</button>
                </div>
            </div>
            <!-- TABLE -->
            <div>
                <div class="d-flex gap-1 mb-1 align-center">
                    <div class="search-box" style="flex:1;margin:0">
                        <i class="fas fa-search"></i>
                        <input type="text" id="bookSearch" placeholder="Search books..." oninput="app.books.search(this.value)">
                    </div>
                    <select id="bookStatusFilter" onchange="app.books.filter()" style="width:auto;padding:.875rem">
                        <option value="">All Status</option>
                        <option value="available">Available</option>
                        <option value="borrowed">Borrowed</option>
                    </select>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead><tr><th>Cover</th><th>Title / Author</th><th>Genre</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody id="booksTableBody"><tr><td colspan="5" class="empty-state"><div class="spinner"></div></td></tr></tbody>
                    </table>
                </div>
                <div class="pagination">
                    <span class="pagination-info" id="booksPaginationInfo">Loading...</span>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline" onclick="app.books.changePage(-1)"><i class="fas fa-chevron-left"></i></button>
                        <button class="btn btn-sm btn-outline" onclick="app.books.changePage(1)"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
