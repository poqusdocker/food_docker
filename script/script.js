let currentFilter = 'all';

// Function to render menu items
function renderMenu(filter = 'all') {
    const container = document.getElementById('menu-container');
    container.innerHTML = '';

    const filteredData = filter === 'all' 
        ? menuData 
        : menuData.filter(item => item.category === filter);

    filteredData.forEach(item => {
        const menuItem = `
            <div class="menu-item" data-category="${item.category}">
                <div class="badge">👁 ${item.views}</div>
                <img src="${item.image}" alt="${item.name}">
                <div class="menu-info">
                    <span class="category">${item.category}</span>
                    <h3>${item.name}</h3>
                    <p>${item.description}</p>
                    <div class="recipe-info">
                        <span>🍽️ ${item.servings} servings</span>
                    </div>
                </div>
            </div>
        `;
        container.innerHTML += menuItem;
    });
}

// Function to filter menu
function filterMenu(category) {
    currentFilter = category;
    
    // Update active button
    const buttons = document.querySelectorAll('.filter-container button');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.closest('button').classList.add('active');
    
    // Render filtered menu
    renderMenu(category);
}

function goToDetail(id) {
  window.location.href = `detail.php`;
}

// Initial render
renderMenu();