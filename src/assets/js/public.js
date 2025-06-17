document.addEventListener('DOMContentLoaded', () => {
  const navItems = document.querySelectorAll('.bottom-nav .nav-item');
  const isLoggedIn = window.isLoggedIn === true;

  // Handle navigation item login protection
  navItems.forEach(item => {
    const requiresLogin = item.dataset.requireLogin === 'true';
    item.addEventListener('click', e => {
      if (requiresLogin && !isLoggedIn) {
        e.preventDefault();
        alert('Please log in to access this feature.');
      }
    });
  });

  // Highlight current page
  const current = window.location.pathname.split('/').pop();
  navItems.forEach(item => {
    if (item.getAttribute('href') === current) {
      item.classList.add('active');
    }
  });

  const grid = document.getElementById('productGrid');
  const totalCountEl = document.getElementById('totalCount');
  const searchInput = document.getElementById('searchInput');
  const perPage = 10;
  let currentCat = '';
  let currentSort = '';
  let currentSearch = '';
  let debounceTimer;

  const loadProducts = () => {
    const url = `get_products.php?cat=${currentCat}&sort=${currentSort}&search=${encodeURIComponent(currentSearch)}&limit=${perPage}`;
    console.log(`Fetching products from: ${url}`);

    fetch(url)
      .then(res => {
        if (!res.ok) {
          console.error('Failed to fetch products:', res.status, res.statusText);
          return [];
        }
        return res.json();
      })
      .then(data => {
        console.log('Fetched products:', data);
        totalCountEl.textContent = data.total;
        grid.innerHTML = '';

        data.items.forEach(prod => {
          const col = document.createElement('div');
          col.className = 'col-6 col-md-3 mb-4';
          col.innerHTML = `
            <div class="card h-100 product-card" data-product-id="${prod.id}">
              <img src="${prod.image_url}" class="card-img-top" alt="${prod.name}">
              <div class="card-body">
                <h5 class="card-title">${prod.name}</h5>
                <p class="card-text">R${prod.price}</p>
                <p class="card-text">${prod.category_name}</p>
                <p class="card-text">${prod.description}</p>
              </div>
            </div>`;
          grid.appendChild(col);
        });

        attachProductCardListeners();
      })
      .catch(err => console.error('Error fetching products:', err));
  };

// Attach quick view and cart logic to product cards
const attachProductCardListeners = (container = document) => {
  container.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('click', () => {
      const pid = card.dataset.productId;
      fetch(`get_product_detail.php?id=${pid}`)
        .then(res => res.json())
        .then(data => {
          const p = data.product;

          // Update the main product details
          document.getElementById('qv-title').textContent = p.name;
          document.getElementById('qv-image').src = p.image_url;
          document.getElementById('qv-price').textContent = p.amount;
          document.getElementById('qv-category').textContent = p.category_name;
          document.getElementById('qv-desc').textContent = p.description;
          document.getElementById('qv-seller').textContent = p.seller_name;
          document.getElementById('qv-chat-link').href = `chats.php?with_user=${p.seller_id}&product_id=${p.id}&message=${encodeURIComponent('Is this product still available?')}`;

          // Update the "Add to Cart" button functionality
          document.getElementById('qv-add-cart').onclick = () => {
            window.location = `add_to_cart.php?id=${p.id}`;
          };

          // Update the similar products section
          const simDiv = document.getElementById('qv-similar');
          simDiv.innerHTML = '';
          data.similar.forEach(s => {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-3 mb-3';
            col.innerHTML = `
              <div class="card h-100 product-card" data-product-id="${s.id}">
                <img src="${s.image_url}" class="card-img-top" alt="">
                <div class="card-body p-2">
                  <h6 class="card-title mb-1">${s.name}</h6>
                  <p class="mb-0">${s.price}</p>
                </div>
              </div>`;
            simDiv.appendChild(col);
          });

          // Reattach event listeners to the newly added similar product cards
          attachProductCardListeners(simDiv);

          // Show the modal
          const modal = new bootstrap.Modal(document.getElementById('quickViewModal'));
          modal.show();
        })
        .catch(err => {
          console.error('Error fetching product details:', err);
        });
    });
  });
};

// Initial call to attach listeners to product cards
attachProductCardListeners();

// Initial call to attach listeners to product cards
attachProductCardListeners();
  // Category card redirection to category slider
  document.querySelectorAll('.category-card').forEach(card => {
    card.addEventListener('click', () => {
      const catId = card.dataset.catId;
      const slider = document.querySelector('.category-slider');
      if (slider) {
        slider.scrollIntoView({ behavior: 'smooth' });
      }

      const sliderButton = document.querySelector(`.category-btn[data-cat-id="${catId}"]`);
      if (sliderButton) {
        sliderButton.click();
      } else {
        console.error(`No slider button found for category ID: ${catId}`);
      }
    });
  });

  // Category filter button
  document.querySelectorAll('.category-btn').forEach(btn => {
    btn.addEventListener('click', e => {
      currentCat = e.target.dataset.catId;
      loadProducts();
    });
  });

  // Sort dropdown filter
  const sortFilter = document.getElementById('sortFilter');
  if (sortFilter) {
    sortFilter.addEventListener('change', e => {
      currentSort = e.target.value;
      loadProducts();
    });
  }

  // Search input debounce
  if (searchInput) {
    searchInput.addEventListener('input', e => {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        currentSearch = e.target.value.trim();
        loadProducts();
      }, 300);
    });
  }

  // Initial product load
  loadProducts();
});
