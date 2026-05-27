<div class="row justify-content-center h-100" id="search-bar" style="display: none" ;>
    <div class="input-group mb-2 shadow">
        <span class="input-group-text bg-dark border-secondary text-light">
            <i class="fa fa-search"></i>
        </span>

        <input type="text"
            class="form-control bg-dark border-secondary text-light fs-3"
            id="search_fulltext"
            placeholder="Search items..."
            value="<?= isset($search_query) ? htmlspecialchars($search_query) : '' ?>">

        <select id="category_filter" class="form-select bg-dark border-secondary text-light" style="max-width: 200px;">
            <option value=""> All Categories</option>
            <?php if (isset($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category->get_id() ?>"
                        <?= isset($selected_category_id) && $selected_category_id == $category->get_id() ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category->get_name()) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>

    </div>
</div>