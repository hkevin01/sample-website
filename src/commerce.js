const demoProducts = [
  {
    title: 'Range Finder House Blend',
    description: 'A balanced bag of beans roasted for everyday brewing.',
    price: '$16.00',
    image: 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=800&q=80',
  },
  {
    title: 'Hometown Ceramic Mug',
    description: 'A sturdy stoneware mug for slow mornings at home.',
    price: '$24.00',
    image: 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?auto=format&fit=crop&w=800&q=80',
  },
  {
    title: 'Range Finder Tote',
    description: 'A reusable canvas tote made for market days and coffee runs.',
    price: '$18.00',
    image: 'https://images.unsplash.com/photo-1597484662317-9bd7bdda778b?auto=format&fit=crop&w=800&q=80',
  },
];

const commerceConfig = {
  pickupUrl: import.meta.env.VITE_SQUARE_PICKUP_URL || '',
  shopUrl: import.meta.env.VITE_SHOPIFY_SHOP_URL || '',
  shopifyStoreDomain: import.meta.env.VITE_SHOPIFY_STORE_DOMAIN || '',
  shopifyStorefrontToken: import.meta.env.VITE_SHOPIFY_STOREFRONT_TOKEN || '',
};

const shopifyQuery = `
  query Products {
    products(first: 6) {
      nodes {
        title
        description
        onlineStoreUrl
        featuredImage { url altText }
        priceRange { minVariantPrice { amount currencyCode } }
      }
    }
  }
`;

function formatPrice(amount, currencyCode) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: currencyCode }).format(Number(amount));
}

function escapeHtml(value = '') {
  return String(value).replace(/[&<>'"]/g, (character) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    "'": '&#039;',
    '"': '&quot;',
  })[character]);
}

function renderProducts(products) {
  const container = document.querySelector('#shop-products');
  if (!container) return;

  container.innerHTML = products.map((product) => `
    <div class="col-md-6 col-xl-4">
      <article class="product-card h-100">
        <img src="${escapeHtml(product.image)}" alt="${escapeHtml(product.alt || product.title)}" loading="lazy" />
        <div class="p-4">
          <div class="d-flex justify-content-between gap-3 align-items-start">
            <h3>${escapeHtml(product.title)}</h3>
            <strong>${escapeHtml(product.price)}</strong>
          </div>
          <p>${escapeHtml(product.description)}</p>
          <a class="btn btn-sm btn-outline-primary" href="${escapeHtml(product.url || commerceConfig.shopUrl || '#')}" target="_blank" rel="noreferrer">
            View product <i class="bi bi-arrow-up-right ms-1"></i>
          </a>
        </div>
      </article>
    </div>
  `).join('');
}

async function loadShopifyProducts() {
  const endpoint = `https://${commerceConfig.shopifyStoreDomain}/api/2025-01/graphql.json`;
  const response = await fetch(endpoint, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Shopify-Storefront-Access-Token': commerceConfig.shopifyStorefrontToken,
    },
    body: JSON.stringify({ query: shopifyQuery }),
  });

  if (!response.ok) throw new Error(`Shopify request failed with ${response.status}`);
  const payload = await response.json();
  if (payload.errors?.length || !payload.data?.products?.nodes) throw new Error('Shopify returned no products');

  return payload.data.products.nodes.map((product) => ({
    title: product.title,
    description: product.description || 'Made for slow mornings and local adventures.',
    price: formatPrice(product.priceRange.minVariantPrice.amount, product.priceRange.minVariantPrice.currencyCode),
    image: product.featuredImage?.url,
    alt: product.featuredImage?.altText || product.title,
    url: product.onlineStoreUrl,
  }));
}

export async function initCommerce() {
  const pickupLink = document.querySelector('#pickup-order-link');
  const pickupStatus = document.querySelector('#pickup-order-status');
  const shopLink = document.querySelector('#shop-checkout-link');
  const shopStatus = document.querySelector('#shop-status');

  if (commerceConfig.pickupUrl) {
    pickupLink.href = commerceConfig.pickupUrl;
    pickupStatus.textContent = 'Live Square pickup ordering is connected.';
  } else {
    pickupLink.href = '#visit';
    pickupLink.removeAttribute('target');
    pickupLink.addEventListener('click', (event) => {
      event.preventDefault();
      document.querySelector('#visit')?.scrollIntoView({ behavior: 'smooth' });
    });
    pickupStatus.textContent = 'Demo mode: add VITE_SQUARE_PICKUP_URL to enable ordering.';
  }

  if (commerceConfig.shopUrl) shopLink.href = commerceConfig.shopUrl;

  if (commerceConfig.shopifyStoreDomain && commerceConfig.shopifyStorefrontToken) {
    try {
      renderProducts(await loadShopifyProducts());
      shopStatus.textContent = 'Shopify store connected';
    } catch (error) {
      renderProducts(demoProducts);
      shopStatus.textContent = 'Shop preview shown; Shopify could not be reached.';
      console.error(error);
    }
  } else {
    renderProducts(demoProducts);
    shopStatus.textContent = 'Shop preview';
  }
}