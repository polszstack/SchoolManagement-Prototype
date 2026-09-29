<!-- ====== GALLERY ====== -->
<div class="tab-content" id="tab-gallery">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-th-large"></i> Book Gallery</h3>
            <div class="d-flex gap-1 align-center">
                <select id="galleryGenreFilter" onchange="app.gallery.load()" style="width:auto;padding:.625rem .875rem;font-size:.875rem">
                    <option value="">All Genres</option>
                </select>
                <select id="galleryStatusFilter" onchange="app.gallery.load()" style="width:auto;padding:.625rem .875rem;font-size:.875rem">
                    <option value="">All Status</option>
                    <option value="available">Available</option>
                    <option value="borrowed">Borrowed</option>
                </select>
            </div>
        </div>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="gallerySearch" placeholder="Search the gallery..." oninput="app.gallery.search(this.value)">
        </div>
        <div class="books-grid" id="galleryGrid"><div class="spinner"></div></div>
        <div class="pagination">
            <span class="pagination-info" id="galleryPaginationInfo"></span>
            <div class="d-flex gap-1">
                <button class="btn btn-sm btn-outline" onclick="app.gallery.changePage(-1)"><i class="fas fa-chevron-left"></i></button>
                <button class="btn btn-sm btn-outline" onclick="app.gallery.changePage(1)"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</div>
