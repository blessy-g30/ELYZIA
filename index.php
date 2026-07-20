<?php
require_once 'config/db.php';
$page_title = "Home";

$where = "1=1";
if (!empty($_GET['q'])) {
    $q = $conn->real_escape_string($_GET['q']);
    $where .= " AND (p.name LIKE '%$q%' OR p.description LIKE '%$q%')";
}
if (!empty($_GET['category'])) {
    $cat = (int)$_GET['category'];
    $where .= " AND p.category_id = $cat";
}

$sql = "SELECT p.*, c.name AS category_name FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE $where ORDER BY p.created_at DESC";
$products = $conn->query($sql);

include 'includes/header.php';
?>



<?php if (empty($_GET['q']) && empty($_GET['category'])): ?>
<section class="slider" id="heroSlider">
  <div class="slide active" style="background:linear-gradient(120deg, #14322C 0%, #2C4A42 100%);">
    <div class="container slide-content">
      <h1>Everything you need,<br>curated with care.</h1>
      <p>Discover electronics, fashion, home essentials and more.</p>
      <a href="#products" class="btn">Shop the collection</a>
    </div>
  </div>
  <div class="slide" style="background:linear-gradient(120deg, #4A3319 0%, #B8863B 100%); position:relative;">
    <div class="container slide-content">
      <h1>Latest smartphones<br>have arrived.</h1>
      <p>Explore the newest phone models with powerful cameras.</p>
      <a href="#products" class="btn">Shop Mobiles</a>
    </div>

    <div class="phone-showcase">
  <svg viewBox="0 0 200 400" xmlns="http://www.w3.org/2000/svg">
    <rect x="10" y="10" width="180" height="380" rx="34" fill="#14322C"/>
    <rect x="22" y="30" width="156" height="340" rx="20" fill="#0F211D"/>
    <circle cx="100" cy="20" r="4" fill="#0F211D"/>
    <rect x="80" y="350" width="40" height="6" rx="3" fill="#B8863B"/>

    <!-- Screen content: photo banner -->
    <rect x="32" y="42" width="136" height="90" rx="10" fill="#D9B876"/>
    <circle cx="65" cy="75" r="14" fill="#B8863B"/>
    <polygon points="90,110 115,75 140,95 160,60 168,110" fill="#14322C" opacity="0.5"/>

    <!-- App icon grid -->
    <rect x="32" y="145" width="34" height="34" rx="9" fill="#B8863B"/>
    <rect x="75" y="145" width="34" height="34" rx="9" fill="#D9B876"/>
    <rect x="118" y="145" width="34" height="34" rx="9" fill="#2C4A42"/>
    <rect x="32" y="188" width="34" height="34" rx="9" fill="#2C4A42"/>
    <rect x="75" y="188" width="34" height="34" rx="9" fill="#B8863B"/>
    <rect x="118" y="188" width="34" height="34" rx="9" fill="#D9B876"/>

    <!-- Bottom "NEW" badge -->
    <rect x="32" y="240" width="136" height="80" rx="10" fill="#1E3A34"/>
    <text x="100" y="275" font-family="Arial" font-size="16" font-weight="bold" fill="#D9B876" text-anchor="middle">NEW</text>
    <text x="100" y="298" font-family="Arial" font-size="11" fill="#D9B876" text-anchor="middle" opacity="0.8">5G Ready</text>
  </svg>
</div>
    </div>
  </div>
  <div class="slide" style="background:linear-gradient(120deg, #1E3A34 0%, #0F211D 100%);">
    <div class="container slide-content">
      <h1>Big Fashion Sale.<br>Up to 40% off.</h1>
      <p>Refresh your wardrobe with our latest fashion collection.</p>
      <a href="#products" class="btn">Explore Fashion</a>
    </div>
  </div>

  <button class="slider-arrow prev" onclick="moveSlide(-1)">&#10094;</button>
  <button class="slider-arrow next" onclick="moveSlide(1)">&#10095;</button>
  <div class="slider-dots">
    <span class="dot active" onclick="goToSlide(0)"></span>
    <span class="dot" onclick="goToSlide(1)"></span>
    <span class="dot" onclick="goToSlide(2)"></span>
  </div>
</section>

<script>
(function(){
  let current = 0;
  const slides = document.querySelectorAll('#heroSlider .slide');
  const dots = document.querySelectorAll('#heroSlider .dot');
  let timer;
  function show(i){
    slides.forEach(s => s.classList.remove('active'));
    dots.forEach(d => d.classList.remove('active'));
    current = (i + slides.length) % slides.length;
    slides[current].classList.add('active');
    dots[current].classList.add('active');
  }
  window.moveSlide = function(dir){ show(current + dir); resetTimer(); };
  window.goToSlide = function(i){ show(i); resetTimer(); };
  function resetTimer(){ clearInterval(timer); timer = setInterval(() => show(current+1), 5000); }
  resetTimer();
})();
</script>
<?php endif; ?>

<div class="container" id="products">
  <div class="section-title">
    <h2><?php
      if (!empty($_GET['q'])) echo 'Results for "' . htmlspecialchars($_GET['q']) . '"';
      elseif (!empty($_GET['category'])) echo 'Category Products';
      else echo 'Featured <span>Products</span>';
    ?></h2>
  </div>
  <div class="container">
  <div class="category-slider">
    <?php
    $cat_icons = [
        'Electronics' => '<svg viewBox="0 0 100 100"><rect x="20" y="25" width="60" height="40" rx="6" fill="#14322C"/><rect x="30" y="33" width="40" height="24" fill="#D9B876"/><rect x="40" y="68" width="20" height="6" rx="3" fill="#B8863B"/></svg>',
        'Fashion' => '<svg viewBox="0 0 100 100"><path d="M42 15 L58 15 L58 25 L75 33 L67 48 L58 40 L58 85 L42 85 L42 40 L33 48 L25 33 L42 25 Z" fill="#D9B876" stroke="#14322C" stroke-width="3"/></svg>',
        'Home & Kitchen' => '<svg viewBox="0 0 100 100"><ellipse cx="50" cy="48" rx="30" ry="16" fill="#14322C"/><ellipse cx="50" cy="45" rx="24" ry="12" fill="#D9B876"/><rect x="12" y="42" width="12" height="5" fill="#14322C"/><rect x="76" y="42" width="12" height="5" fill="#14322C"/></svg>',
        'Books' => '<svg viewBox="0 0 100 100"><rect x="28" y="18" width="44" height="64" rx="4" fill="#14322C"/><rect x="34" y="24" width="32" height="52" rx="3" fill="#D9B876"/><line x1="50" y1="24" x2="50" y2="76" stroke="#14322C" stroke-width="2"/></svg>',
        'Beauty' => '<svg viewBox="0 0 100 100"><rect x="35" y="35" width="30" height="45" rx="6" fill="#14322C" opacity="0.85"/><rect x="40" y="20" width="20" height="16" fill="#B8863B"/><rect x="37" y="12" width="26" height="9" rx="3" fill="#14322C"/></svg>',
        'Mobiles' => '<svg viewBox="0 0 100 100"><rect x="32" y="10" width="36" height="80" rx="8" fill="#14322C"/><rect x="38" y="20" width="24" height="56" rx="4" fill="#D9B876"/><circle cx="50" cy="15" r="2" fill="#14322C"/></svg>',
    ];
    $cat_list = $conn->query("SELECT * FROM categories ORDER BY name");
    while ($c = $cat_list->fetch_assoc()):
        $icon = $cat_icons[$c['name']] ?? '<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="30" fill="#14322C"/></svg>';
    ?>
      <a href="/elyzia/index.php?category=<?php echo $c['id']; ?>" class="category-pill">
        <div class="category-icon"><?php echo $icon; ?></div>
        <span><?php echo htmlspecialchars($c['name']); ?></span>
      </a>
    <?php endwhile; ?>
  </div>
</div>

  <div class="product-grid">
    <?php if ($products && $products->num_rows > 0): ?>
      <?php while ($p = $products->fetch_assoc()): ?>
        <a href="/elyzia/product.php?id=<?php echo $p['id']; ?>" class="product-card">
          <div class="product-thumb">
            <img src="/elyzia/assets/images/<?php echo htmlspecialchars($p['image']); ?>"
                 onerror="this.style.display='none'; this.parentElement.innerHTML='<?php echo htmlspecialchars($p['name']); ?>';"
                 alt="<?php echo htmlspecialchars($p['name']); ?>">
          </div>
          <div class="product-body">
            <h3><?php echo htmlspecialchars($p['name']); ?></h3>
            <div class="rating">★ <?php echo $p['rating']; ?></div>
            <div class="price">₹<?php echo number_format($p['price'], 2); ?></div>
          </div>
        </a>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="empty-state">No products found. Try a different search.</div>
    <?php endif; ?>
  </div>
</div>
<div class="container">
  <div class="section-title"><h2>What Our <span>Customers Say</span></h2></div>
  <div class="reviews-grid">
    <?php
    $reviews = [
        ['name' => 'Priya S.', 'rating' => 5, 'text' => 'Fast delivery and the product quality was exactly as shown. Very happy with my purchase!'],
        ['name' => 'Arun K.', 'rating' => 4, 'text' => 'Good value for money. Packaging was neat and the site was easy to use.'],
        ['name' => 'Divya R.', 'rating' => 5, 'text' => 'Loved the collection! Found exactly what I was looking for at a great price.'],
        ['name' => 'Mohan V.', 'rating' => 4, 'text' => 'Smooth checkout process and quick order tracking. Will shop again.'],
    ];
    foreach ($reviews as $r):
    ?>
      <div class="review-card">
        <div class="rating">
          <?php for ($i = 0; $i < 5; $i++) echo $i < $r['rating'] ? '★' : '☆'; ?>
        </div>
        <p class="review-text">"<?php echo htmlspecialchars($r['text']); ?>"</p>
        <div class="review-name">— <?php echo htmlspecialchars($r['name']); ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
