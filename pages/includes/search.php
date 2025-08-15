<div class="filter-section">
    <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap; width: 100%;">
        <input type="text" id="searchInput" class="search-input" placeholder="Search medicines..."
            value="<?php echo htmlspecialchars($search); ?>" onkeyup="enhancedSearch()" autocomplete="off">

        <select id="filterSelect" class="filter-select" onchange="enhancedSearch()">
            <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Medicines</option>
            <option value="low-stock" <?php echo $filter === 'low-stock' ? 'selected' : ''; ?>>Low Stock
            </option>
            <option value="expired" <?php echo $filter === 'expired' ? 'selected' : ''; ?>>Expired</option>
            <option value="expiring-soon" <?php echo $filter === 'expiring-soon' ? 'selected' : ''; ?>>
                Expiring Soon</option>
        </select>

        <button type="button" class="btn btn-secondary" onclick="clearFilters()">Clear</button>
    </div>
</div>